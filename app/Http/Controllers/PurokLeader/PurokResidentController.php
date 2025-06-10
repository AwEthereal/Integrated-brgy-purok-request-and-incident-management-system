<?php

namespace App\Http\Controllers\PurokLeader;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AccountStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PurokResidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Approve a resident's account
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function approve($id): \Illuminate\Http\RedirectResponse
    {
        $resident = User::findOrFail($id);
        
        // Check if user is authorized to approve
        $this->authorize('approveResident', $resident);
        
        try {
            $resident->update([
                'is_approved' => true,
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'rejected_at' => null,
                'rejected_by' => null,
                'rejection_reason' => null,
            ]);
            
            // Send approval notification
            $resident->notify(new AccountStatusNotification('approved'));
            
            return redirect()
                ->back()
                ->with('success', 'Resident account has been approved successfully. An email notification has been sent.');
                
        } catch (\Exception $e) {
            Log::error('Error approving resident: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Failed to approve resident. Please try again.');
        }
    }

    
    /**
     * Show the rejection form
     * 
     * @param  string  $id
     * @return \Illuminate\View\View
     */
    public function showRejectForm($id)
    {
        $resident = User::findOrFail($id);
        $this->authorize('rejectResident', $resident);
        
        return view('purok_leader.residents.reject', compact('resident'));
    }
    
    /**
     * Reject a resident's account
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function reject(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);
        
        $resident = User::findOrFail($id);
        $this->authorize('rejectResident', $resident);
        
        try {
            $resident->update([
                'is_approved' => false,
                'rejected_at' => now(),
                'rejected_by' => Auth::id(),
                'rejection_reason' => $request->rejection_reason,
            ]);
            
            // Send rejection notification
            $resident->notify(new AccountStatusNotification('rejected', $request->rejection_reason));
            
            return redirect()
                ->route('purok_leader.residents')
                ->with('success', 'Resident account has been rejected. A notification has been sent to the resident.');
                
        } catch (\Exception $e) {
            Log::error('Error rejecting resident: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Failed to reject resident. Please try again.');
        }
    }
}
