<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DealerController extends Controller
{
    /**
     * Show the dealer dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        if ($user->role !== 'dealer') {
            return redirect('/home');
        }

        // Plan & subscription data
        $currentPlan = $user->getCurrentPlan();
        $subscription = $user->getActiveSubscription();
        $remainingListings = $user->getRemainingListings();
        $totalAllowed = $currentPlan ? $currentPlan->listings_per_month : 3;
        $usedListings = $subscription ? $subscription->listed_this_month : 0;
        $propertyCount = \App\Models\Property::where('user_id', $user->id)->count();

        return view('dealer-dashboard', [
            'user' => $user,
            'isVerified' => $user->isVerified(),
            'verificationStatus' => $user->verification_status ?? 'unverified',
            'currentPlan' => $currentPlan,
            'subscription' => $subscription,
            'remainingListings' => $remainingListings,
            'totalAllowed' => $totalAllowed,
            'usedListings' => $usedListings,
            'propertyCount' => $propertyCount,
        ]);
    }

    /**
     * Submit verification documents
     */
    public function submitVerification(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'dealer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if user is banned from verification
        if ($user->isVerificationBanned()) {
            return response()->json([
                'error' => 'Verification Banned',
                'message' => 'You have exceeded the maximum verification attempts. Your email has been banned. Please contact support.',
                'banned_reason' => $user->verification_ban_reason,
                'banned_at' => $user->verification_banned_at,
            ], 403);
        }

        if ($user->isVerified()) {
            return response()->json(['error' => 'Already verified'], 400);
        }

        $request->validate([
            'cnic_number' => 'required|string|regex:/^\d{5}-\d{7}-\d{1}$/',
            'phone' => 'required|string|min:10|max:15',
            'cnic_front' => 'required|image|max:5120',
            'cnic_back' => 'required|image|max:5120',
            'live_photo' => 'required|string', // base64 from camera
            'selfie' => 'nullable|image|max:5120', // optional selfie with CNIC
        ]);

        $manualCnicNumber = $request->input('cnic_number');

        // ===== STEP 1: Store files =====
        $userId = $user->id;
        $storagePath = "verification/{$userId}";

        // Store CNIC front
        $cnicFrontPath = $request->file('cnic_front')->store("{$storagePath}", 'public');

        // Store CNIC back
        $cnicBackPath = $request->file('cnic_back')->store("{$storagePath}", 'public');

        // Store live photo (base64 from camera capture)
        $livePhotoData = $request->input('live_photo');
        $livePhotoData = preg_replace('/^data:image\/\w+;base64,/', '', $livePhotoData);
        $livePhotoData = base64_decode($livePhotoData);
        $livePhotoFilename = "verification/{$userId}/live_photo.jpg";
        Storage::disk('public')->put($livePhotoFilename, $livePhotoData);

        // Store selfie (optional)
        $selfiePath = null;
        if ($request->hasFile('selfie')) {
            $selfiePath = $request->file('selfie')->store("{$storagePath}", 'public');
        }

        // Update user verification data
        $user->update([
            'cnic_number' => $manualCnicNumber,
            'phone' => $request->input('phone'),
            'cnic_front_image' => $cnicFrontPath,
            'cnic_back_image' => $cnicBackPath,
            'live_photo' => $livePhotoFilename,
            'selfie_photo' => $selfiePath,
            'verification_status' => 'pending',
            'verification_submitted_at' => now(),
        ]);

        // ===== STEP 3: Run AI face match =====
        $aiResult = $this->verifyFaceMatch($user);

        if ($aiResult['match'] && $aiResult['confidence'] >= 70) {
            // Successful verification
            $user->update([
                'verification_status' => 'verified',
                'verified_at' => now(),
                'verification_notes' => "AI face match: {$aiResult['confidence']}% confidence. Auto-verified.",
            ]);

            // Reset failed attempts on successful verification
            $user->resetVerificationAttempts();

            return response()->json([
                'success' => true,
                'verified' => true,
                'message' => 'Verification complete! You are now a Verified Dealer ✅',
                'confidence' => $aiResult['confidence'],
            ]);
        }

        // AI match failed or low confidence - Record the failed attempt
        $notes = $aiResult['match']
            ? "AI match confidence too low: {$aiResult['confidence']}%. Needs manual review."
            : "AI face match failed. Confidence: {$aiResult['confidence']}%. Needs manual review.";

        $user->update([
            'verification_notes' => $notes,
        ]);

        // Record failed attempt
        $user->recordFailedVerification();

        // Check if verification is now banned
        if ($user->isVerificationBanned()) {
            // Send ban notification email
            $this->sendVerificationBannedEmail($user);

            return response()->json([
                'success' => false,
                'verified' => false,
                'message' => 'Verification failed. You have used all 5 attempts. Your email is now banned from verification.',
                'confidence' => $aiResult['confidence'],
                'attempts_remaining' => 0,
                'is_banned' => true,
            ]);
        }

        // Return with remaining attempts
        $remainingAttempts = $user->getRemainingVerificationAttempts();

        return response()->json([
            'success' => true,
            'verified' => false,
            'message' => "Verification failed. You have {$remainingAttempts} attempts remaining. Please try again with clearer photos.",
            'confidence' => $aiResult['confidence'],
            'attempts_remaining' => $remainingAttempts,
            'attempts_used' => $user->verification_failed_attempts,
            'is_banned' => false,
        ]);
    }

    /**
     * Send verification banned email
     */
    private function sendVerificationBannedEmail($user): void
    {
        try {
            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Account Verification Banned - Action Required')
                    ->html(view('emails.verification-banned', [
                        'user' => $user,
                        'bannedAt' => $user->verification_banned_at,
                        'reason' => $user->verification_ban_reason,
                    ])->render());
            });

            Log::info('Verification banned email sent', ['user_id' => $user->id, 'email' => $user->email]);
        } catch (\Exception $e) {
            Log::error('Failed to send verification banned email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * AI Face Match: Compare CNIC front photo with live camera photo using OpenRouter
     */
    private function verifyFaceMatch($user): array
    {
        try {
            // Read images as base64: CNIC front + live camera photo
            $cnicFrontBase64 = base64_encode(Storage::disk('public')->get($user->cnic_front_image));
            $livePhotoBase64 = base64_encode(Storage::disk('public')->get($user->live_photo));

            $apiKey = config('services.openrouter.api_key', env('OPENROUTER_API_KEY'));
            $baseUrl = config('services.openrouter.base_url', env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'));

            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url', 'http://localhost'),
            ])->timeout(60)->post("{$baseUrl}/chat/completions", [
                'model' => 'google/gemini-2.0-flash-001',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'You are a face verification system for identity verification. Compare the face on the CNIC ID card (first image) with the live camera photo of the person (second image). Determine if the person in the live photo is the same person shown on the CNIC card. Return ONLY a JSON object with no other text: {"match": true/false, "confidence": 0-100}. Be strict but fair — the photos may differ in lighting, angle, and age but the core facial features should match.',
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:image/jpeg;base64,{$cnicFrontBase64}",
                                ],
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:image/jpeg;base64,{$livePhotoBase64}",
                                ],
                            ],
                        ],
                    ],
                ],
                'max_tokens' => 100,
                'temperature' => 0.1,
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content', '');
                Log::info('OpenRouter AI Face Match Response', ['content' => $content]);

                // Extract JSON from response
                preg_match('/\{.*\}/s', $content, $matches);
                if (!empty($matches)) {
                    $result = json_decode($matches[0], true);
                    if (isset($result['match']) && isset($result['confidence'])) {
                        return [
                            'match' => (bool) $result['match'],
                            'confidence' => (int) $result['confidence'],
                        ];
                    }
                }
            }

            Log::error('OpenRouter AI Face Match failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return ['match' => false, 'confidence' => 0];
        } catch (\Exception $e) {
            Log::error('Face match exception: ' . $e->getMessage());
            return ['match' => false, 'confidence' => 0];
        }
    }

    /**
     * Clear previous verification attempt and allow re-upload
     */
    public function clearVerification(Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'dealer') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($user->isVerified()) {
            return response()->json(['error' => 'Already verified'], 400);
        }

        try {
            // Delete previous verification files from storage
            if ($user->cnic_front_image) {
                Storage::disk('public')->delete($user->cnic_front_image);
            }
            if ($user->cnic_back_image) {
                Storage::disk('public')->delete($user->cnic_back_image);
            }
            if ($user->live_photo) {
                Storage::disk('public')->delete($user->live_photo);
            }
            if ($user->selfie_photo) {
                Storage::disk('public')->delete($user->selfie_photo);
            }

            // Clear verification data from database
            $user->update([
                'cnic_number' => null,
                'phone' => null,
                'cnic_front_image' => null,
                'cnic_back_image' => null,
                'live_photo' => null,
                'selfie_photo' => null,
                'verification_status' => 'unverified',
                'verification_notes' => null,
                'verification_submitted_at' => null,
            ]);

            Log::info('Verification cleared for user', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Previous verification data cleared. You can now upload new documents.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing verification', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to clear verification data',
            ], 500);
        }
    }

    /**
     * Get all dealers for the frontend listings
     */
    public function index()
    {
        try {
            // Fetch all users with role 'dealer'
            $dealers = \App\Models\User::where('role', 'dealer')
                ->where('verification_status', 'verified') // Optional: only show verified dealers
                ->get()
                ->map(function ($dealer) {
                    $plan = $dealer->getCurrentPlan();
                    return [
                        'id' => $dealer->id,
                        'name' => $dealer->name,
                        'location' => $dealer->location ?? 'Karachi, Pakistan', // Default location if missing
                        'image' => $dealer->avatar ? asset('storage/' . $dealer->avatar) : null,
                        'plan' => $plan ? $plan->slug : 'free',
                    ];
                });

            return response()->json($dealers);
        } catch (\Exception $e) {
            Log::error('Error fetching dealers: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load dealer listings'], 500);
        }
    }
}
