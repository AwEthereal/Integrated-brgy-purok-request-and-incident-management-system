<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementPublicController extends Controller
{
    /**
     * Display announcements for residents and purok leaders
     */
    public function index()
    {
        // Get active and published announcements
        $announcements = Announcement::active()
            ->published()
            ->with('creator')
            ->orderBy('is_featured', 'desc')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('announcements.public', compact('announcements'));
    }
}
