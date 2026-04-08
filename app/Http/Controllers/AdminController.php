<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard for property management
     */
    public function dashboard()
    {
        $pendingProperties = Property::where('status', 'pending_review')
            ->with(['user', 'images'])
            ->orderBy('created_at', 'desc')
            ->get();

        $approvedProperties = Property::where('status', 'approved')
            ->with(['user', 'images'])
            ->orderBy('approved_at', 'desc')
            ->limit(10)
            ->get();

        $rejectedProperties = Property::where('status', 'rejected')
            ->with(['user', 'images'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('pendingProperties', 'approvedProperties', 'rejectedProperties'));
    }

    /**
     * Show approved properties listing
     */
    public function approved()
    {
        $properties = Property::where('status', 'approved')
            ->with(['user', 'images'])
            ->orderBy('approved_at', 'desc')
            ->paginate(24);

        return view('admin.dashboard', compact('properties'));
    }

    /**
     * Show rejected properties listing
     */
    public function rejected()
    {
        $properties = Property::where('status', 'rejected')
            ->with(['user', 'images'])
            ->orderBy('updated_at', 'desc')
            ->paginate(24);

        return view('admin.dashboard', compact('properties'));
    }

    /**
     * Show full preview of a single property
     */
    public function show($id)
    {
        $property = Property::with(['user', 'images'])->findOrFail($id);
        return view('admin.dashboard', compact('property'));
    }

    /**
     * Approve a property listing
     */
    public function approveProperty($id)
    {
        try {
            $property = Property::findOrFail($id);
            $property->update([
                'status' => 'approved',
                'approved_at' => now(),
                'admin_notes' => 'Approved by admin'
            ]);

            return response()->json(['success' => true, 'message' => 'Property approved successfully!']);
        } catch (\Exception $e) {
            Log::error('Error approving property: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to approve property.'], 500);
        }
    }

    /**
     * Reject a property listing
     */
    public function rejectProperty(Request $request, $id)
    {
        try {
            $property = Property::findOrFail($id);
            $property->update([
                'status' => 'rejected',
                'admin_notes' => $request->input('reason', 'Rejected by admin')
            ]);

            return response()->json(['success' => true, 'message' => 'Property rejected successfully!']);
        } catch (\Exception $e) {
            Log::error('Error rejecting property: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to reject property.'], 500);
        }
    }

    /**
     * Show list of registered dealers
     */
    public function dealers()
    {
        $dealers = User::where('role', 'dealer')
            ->orderBy('created_at', 'desc')
            ->paginate(24);

        return view('admin.dashboard', compact('dealers'));
    }

    /**
     * Show a specific dealer's preview along with their properties
     */
    public function showDealer($id)
    {
        $dealer = User::findOrFail($id);
        
        $properties = Property::where('user_id', $dealer->id)
            ->with('images')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('admin.dashboard', compact('dealer', 'properties'));
    }

    /**
     * Admin manually verify a dealer
     */
    public function verifyDealer(Request $request, $id)
    {
        try {
            $dealer = User::findOrFail($id);
            $dealer->update([
                'verification_status' => 'verified',
                'verified_at' => now(),
                'verification_banned' => false,
                'verification_notes' => 'Manually verified by admin. ' . $request->input('reason', ''),
            ]);
            // Reset attempts so they aren't banned later if something changes
            $dealer->resetVerificationAttempts();

            return response()->json(['success' => true, 'message' => 'Dealer verified successfully!']);
        } catch (\Exception $e) {
            Log::error('Error manually verifying dealer: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to verify dealer.'], 500);
        }
    }

    /**
     * Admin manually reject/ban a dealer
     */
    public function rejectDealer(Request $request, $id)
    {
        try {
            $dealer = User::findOrFail($id);
            $dealer->update([
                'verification_status' => 'unverified',
                'verification_banned' => true,
                'verification_banned_at' => now(),
                'verification_ban_reason' => 'Manually banned by admin. ' . $request->input('reason', 'Violation of terms'),
                'verification_notes' => 'Banned by admin: ' . $request->input('reason', ''),
            ]);

            return response()->json(['success' => true, 'message' => 'Dealer rejected/banned successfully!']);
        } catch (\Exception $e) {
            Log::error('Error manually rejecting dealer: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Failed to reject dealer.'], 500);
        }
    }
}
