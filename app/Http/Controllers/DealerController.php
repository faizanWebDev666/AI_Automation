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
            return redirect('/chat');
        }

        return view('dealer-dashboard', [
            'user' => $user,
            'isVerified' => $user->isVerified(),
            'verificationStatus' => $user->verification_status ?? 'unverified',
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

        $userId = $user->id;
        $storagePath = "public/verification/{$userId}";

        // Store CNIC front
        $cnicFrontPath = $request->file('cnic_front')->store("{$storagePath}", 'local');

        // Store CNIC back
        $cnicBackPath = $request->file('cnic_back')->store("{$storagePath}", 'local');

        // Store live photo (base64 from camera capture)
        $livePhotoData = $request->input('live_photo');
        $livePhotoData = preg_replace('/^data:image\/\w+;base64,/', '', $livePhotoData);
        $livePhotoData = base64_decode($livePhotoData);
        $livePhotoFilename = "public/verification/{$userId}/live_photo.jpg";
        Storage::disk('local')->put($livePhotoFilename, $livePhotoData);

        // Store selfie (optional)
        $selfiePath = null;
        if ($request->hasFile('selfie')) {
            $selfiePath = $request->file('selfie')->store("{$storagePath}", 'local');
        }

        // Update user verification data
        $user->update([
            'cnic_number' => $request->input('cnic_number'),
            'phone' => $request->input('phone'),
            'cnic_front_image' => $cnicFrontPath,
            'cnic_back_image' => $cnicBackPath,
            'live_photo' => $livePhotoFilename,
            'selfie_photo' => $selfiePath,
            'verification_status' => 'pending',
            'verification_submitted_at' => now(),
        ]);

        // Run AI face match
        $aiResult = $this->verifyFaceMatch($user);

        if ($aiResult['match'] && $aiResult['confidence'] >= 70) {
            $user->update([
                'verification_status' => 'verified',
                'verified_at' => now(),
                'verification_notes' => "AI face match: {$aiResult['confidence']}% confidence. Auto-verified.",
            ]);

            return response()->json([
                'success' => true,
                'verified' => true,
                'message' => 'Verification complete! You are now a Verified Dealer ✅',
                'confidence' => $aiResult['confidence'],
            ]);
        }

        // AI match failed or low confidence
        $notes = $aiResult['match']
            ? "AI match confidence too low: {$aiResult['confidence']}%. Needs manual review."
            : "AI face match failed. Confidence: {$aiResult['confidence']}%. Needs manual review.";

        $user->update([
            'verification_notes' => $notes,
        ]);

        return response()->json([
            'success' => true,
            'verified' => false,
            'message' => 'Documents submitted. Your verification is under review.',
            'confidence' => $aiResult['confidence'],
        ]);
    }

    /**
     * AI Face Match: Compare CNIC front photo with live camera photo using OpenRouter
     */
    private function verifyFaceMatch($user): array
    {
        try {
            // Read images as base64: CNIC front + live camera photo
            $cnicFrontBase64 = base64_encode(Storage::disk('local')->get($user->cnic_front_image));
            $livePhotoBase64 = base64_encode(Storage::disk('local')->get($user->live_photo));

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
}
