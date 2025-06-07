<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PurokApprovalController extends Controller
{
    /**
     * Get the count of pending purok approvals
     *
     * @return JsonResponse
     */
    public function getPendingCount(): JsonResponse
    {
        $user = Auth::user();
        
        // Only purok leaders and presidents should see their pending requests
        if (!in_array($user->role, ['purok_leader', 'purok_president'])) {
            return response()->json(['count' => 0]);
        }

        $count = Request::where('purok_id', $user->purok_id)
            ->where('status', 'pending')
            ->count();

        return response()->json(['count' => $count]);
    }
}
