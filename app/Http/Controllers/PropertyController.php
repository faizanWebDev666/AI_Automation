<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PropertyController extends Controller
{
    /**
     * Show the property listing page (Blade)
     */
    public function indexPage()
    {
        return view('dealer.properties.index');
    }

    /**
     * List dealer's properties (JSON for AJAX)
     */
    public function index()
    {
        $properties = Property::where('user_id', Auth::id())
            ->with('images')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['success' => true, 'properties' => $properties]);
    }

    /**
     * Show property details (Public Page)
     */
    public function show($id)
    {
        $property = Property::with(['images', 'user'])->findOrFail($id);

        // Only show approved properties to public, but allow the owner to see it
        if ($property->status !== 'approved' && (!Auth::check() || Auth::id() !== $property->user_id)) {
            abort(404, 'Property not found or pending approval.');
        }

        return view('properties.show', compact('property'));
    }

    /**
     * Show the create property form (Blade)
     */
    public function create()
    {
        $user = Auth::user();
        if (!$user->isVerified()) {
            return redirect()->route('dealer.dashboard')->with('error', 'You must be verified to list properties.');
        }

        // Check plan and listing limits
        $currentPlan = $user->getCurrentPlan();
        $remainingListings = $user->getRemainingListings();
        $canAdd = $user->canAddListing();
        $totalAllowed = $currentPlan ? $currentPlan->listings_per_month : 3;
        $subscription = $user->getActiveSubscription();
        $usedListings = $subscription ? $subscription->listed_this_month : 0;

        return view('dealer.properties.create', compact(
            'currentPlan', 'remainingListings', 'canAdd', 'totalAllowed', 'usedListings'
        ));
    }

    /**
     * Store a new property listing
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isVerified()) {
            return response()->json(['error' => 'You must be verified to list properties.'], 403);
        }

        // ═══════ PLAN LISTING LIMIT CHECK ═══════
        if (!$user->canAddListing()) {
            $currentPlan = $user->getCurrentPlan();
            $planName = $currentPlan ? $currentPlan->name : 'Free Plan';
            $limit = $currentPlan ? ($currentPlan->listings_per_month ?? 'unlimited') : 3;

            return response()->json([
                'error' => "You've reached your {$planName} listing limit ({$limit} per month). Please upgrade your plan to add more listings.",
                'limit_reached' => true,
                'current_plan' => $planName,
                'upgrade_url' => route('subscription.plans'),
            ], 403);
        }

        $request->validate([
            'title' => 'required|string|max:200',
            'property_type' => 'required|in:house,portion,apartment,plot,commercial',
            'listing_type' => 'required|in:sale,rent',
            'ownership_type' => 'required|in:owner,dealer,builder',
            'price' => 'required|numeric|min:1000',
            'area_marla' => 'required|numeric|min:0.5|max:500',
            'bedrooms' => 'required|integer|min:0|max:20',
            'bathrooms' => 'required|integer|min:0|max:20',
            'kitchens' => 'required|integer|min:0|max:10',
            'floors' => 'required|integer|min:1|max:20',
            'furnished' => 'required|in:furnished,semi-furnished,unfurnished',
            'description' => 'nullable|string|max:5000',
            'city' => 'required|string|max:100',
            'area_name' => 'required|string|max:200',
            'full_address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'contact_phone' => 'required|string|min:10|max:15',
            'electricity_bill' => 'nullable|file|max:5120',
            'ownership_proof' => 'nullable|file|max:5120',
            'images.*' => 'nullable|image|max:5120',
            'live_photo' => 'required|string', // base64 camera photo
        ]);

        $flags = [];

        // ═══════ FRAUD CHECK 1: Spam detection (same phone → many listings) ═══════
        $recentListings = Property::where('contact_phone', $request->contact_phone)
            ->where('created_at', '>=', now()->subDay())
            ->count();

        if ($recentListings >= 5) {
            $flags[] = 'spam_phone: ' . $recentListings . ' listings in 24h from same phone';
        }

        // ═══════ FRAUD CHECK 2: Price anomaly (basic check) ═══════
        $avgPrice = Property::where('city', $request->city)
            ->where('property_type', $request->property_type)
            ->where('status', 'approved')
            ->avg('price');

        if ($avgPrice && $request->price < ($avgPrice * 0.4)) {
            $flags[] = 'price_anomaly: Price Rs ' . number_format($request->price) .
                       ' is <40% of avg Rs ' . number_format($avgPrice) . ' for ' . $request->city;
        }

        // Store documents
        $timestamp = time();
        $folderName = "properties/{$user->id}/{$timestamp}";
        $storagePath = $folderName;
        
        $electricityBillPath = null;
        $ownershipProofPath = null;

        if ($request->hasFile('electricity_bill')) {
            $electricityBillPath = $request->file('electricity_bill')->store($storagePath, 'public');
        }

        if ($request->hasFile('ownership_proof')) {
            $ownershipProofPath = $request->file('ownership_proof')->store($storagePath, 'public');
        }

        // Create property
        $property = Property::create([
            'user_id' => $user->id,
            'title' => $request->title,
            'property_type' => $request->property_type,
            'listing_type' => $request->listing_type,
            'ownership_type' => $request->ownership_type,
            'price' => $request->price,
            'area_marla' => $request->area_marla,
            'bedrooms' => $request->bedrooms,
            'bathrooms' => $request->bathrooms,
            'kitchens' => $request->kitchens,
            'floors' => $request->floors,
            'furnished' => $request->furnished,
            'description' => $request->description,
            'city' => $request->city,
            'area_name' => $request->area_name,
            'full_address' => $request->full_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'electricity_bill' => $electricityBillPath,
            'ownership_proof' => $ownershipProofPath,
            'contact_phone' => $request->contact_phone,
            'status' => !empty($flags) ? 'flagged' : 'pending_review',
            'flags' => !empty($flags) ? $flags : null,
        ]);

        // ═══════ PROCESS LIVE CAMERA PHOTO ═══════
        $livePhotoData = $request->input('live_photo');
        $livePhotoData = preg_replace('/^data:image\/\w+;base64,/', '', $livePhotoData);
        $livePhotoDecoded = base64_decode($livePhotoData);
        $livePhotoHash = hash('sha256', $livePhotoDecoded);

        // Check for cross-listing duplicates
        $isDuplicate = $this->checkDuplicateHash($livePhotoHash, $property->id, $request->city, $flags);

        // Watermark the live photo
        $watermarkedLivePhoto = $this->addWatermark($livePhotoDecoded);

        $livePhotoPath = "{$storagePath}/live_photo.jpg";
        Storage::disk('public')->put($livePhotoPath, $watermarkedLivePhoto);

        PropertyImage::create([
            'property_id' => $property->id,
            'image_path' => $livePhotoPath,
            'image_hash' => $livePhotoHash,
            'is_live_photo' => true,
            'is_watermarked' => true,
            'is_duplicate' => $isDuplicate,
            'sort_order' => 0,
        ]);

        // ═══════ PROCESS UPLOADED IMAGES ═══════
        if ($request->hasFile('images')) {
            $order = 1;
            foreach ($request->file('images') as $image) {
                $imageContent = file_get_contents($image->getRealPath());
                $imageHash = hash('sha256', $imageContent);

                // Check duplicate
                $isDup = $this->checkDuplicateHash($imageHash, $property->id, $request->city, $flags);

                // Watermark
                $watermarked = $this->addWatermark($imageContent);

                $imgPath = "{$storagePath}/img_{$order}.jpg";
                Storage::disk('public')->put($imgPath, $watermarked);

                PropertyImage::create([
                    'property_id' => $property->id,
                    'image_path' => $imgPath,
                    'image_hash' => $imageHash,
                    'is_live_photo' => false,
                    'is_watermarked' => true,
                    'is_duplicate' => $isDup,
                    'sort_order' => $order,
                ]);

                $order++;
            }
        }

        // Update flags if any new ones were added during image processing
        if (!empty($flags) && $property->status !== 'flagged') {
            $property->update([
                'status' => 'flagged',
                'flags' => $flags,
            ]);
        }

        // ═══════ INCREMENT LISTING COUNTER ═══════
        $user->addListing();

        return response()->json([
            'success' => true,
            'message' => !empty($flags)
                ? 'Listing submitted but flagged for review. Our team will check it.'
                : 'Listing submitted for admin approval! You\'ll be notified once approved.',
            'property' => $property->load('images'),
            'flags' => $flags,
        ]);
    }

    /**
     * Delete a property
     */
    public function destroy($id)
    {
        $property = Property::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        // Delete image files
        foreach ($property->images as $image) {
            Storage::disk('local')->delete($image->image_path);
        }

        if ($property->electricity_bill) {
            Storage::disk('local')->delete($property->electricity_bill);
        }
        if ($property->ownership_proof) {
            Storage::disk('local')->delete($property->ownership_proof);
        }

        $property->delete();

        return response()->json(['success' => true, 'message' => 'Listing deleted.']);
    }

    /**
     * Check if image hash is a duplicate across listings
     */
    private function checkDuplicateHash(string $hash, int $currentPropertyId, string $city, array &$flags): bool
    {
        $existing = PropertyImage::where('image_hash', $hash)
            ->where('property_id', '!=', $currentPropertyId)
            ->with('property:id,city,user_id')
            ->first();

        if ($existing) {
            $flags[] = "duplicate_image: Same image found in listing #{$existing->property_id}";

            // Cross-city check
            if ($existing->property && $existing->property->city !== $city) {
                $flags[] = "cross_city_duplicate: Same image in {$existing->property->city} and {$city}";
            }

            return true;
        }

        return false;
    }

    /**
     * Add watermark to image using GD library
     */
    private function addWatermark(string $imageData): string
    {
        try {
            $image = @imagecreatefromstring($imageData);
            if (!$image) {
                return $imageData; // Return original if GD can't process
            }

            $width = imagesx($image);
            $height = imagesy($image);

            // Create watermark text
            $watermarkText = 'Uploaded on ResellZone • ' . date('d M Y');

            // Semi-transparent white background bar
            $bgColor = imagecolorallocatealpha($image, 0, 0, 0, 80);
            $barHeight = max(30, (int)($height * 0.05));
            imagefilledrectangle($image, 0, $height - $barHeight, $width, $height, $bgColor);

            // White text
            $textColor = imagecolorallocate($image, 255, 255, 255);
            $fontSize = max(2, min(5, (int)($width / 200)));
            $textWidth = imagefontwidth($fontSize) * strlen($watermarkText);
            $textX = max(10, (int)(($width - $textWidth) / 2));
            $textY = $height - $barHeight + (int)(($barHeight - imagefontheight($fontSize)) / 2);

            imagestring($image, $fontSize, $textX, $textY, $watermarkText, $textColor);

            // Also add small diagonal repeating watermark
            $wmColor = imagecolorallocatealpha($image, 255, 255, 255, 110);
            $smallText = 'ResellZone';
            for ($y = 50; $y < $height - $barHeight; $y += 150) {
                for ($x = -100; $x < $width; $x += 300) {
                    imagestring($image, 2, $x, $y, $smallText, $wmColor);
                }
            }

            // Output to string
            ob_start();
            imagejpeg($image, null, 90);
            $result = ob_get_clean();
            imagedestroy($image);

            return $result;
        } catch (\Exception $e) {
            Log::error('Watermark failed: ' . $e->getMessage());
            return $imageData;
        }
    }
}
