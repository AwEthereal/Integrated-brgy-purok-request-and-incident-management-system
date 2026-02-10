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

    public function publicIndex()
    {
        $announcements = Announcement::active()
            ->published()
            ->with('creator')
            ->orderBy('is_featured', 'desc')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('public.announcements', compact('announcements'));
    }

    public function publicShow(Announcement $announcement)
    {
        abort_unless($announcement->is_active, 404);
        if (!is_null($announcement->expires_at) && $announcement->expires_at->lte(now())) {
            abort(404);
        }
        if (!is_null($announcement->published_at) && $announcement->published_at->gt(now())) {
            abort(404);
        }

        $announcement->load('creator');

        return view('announcements.show_public', compact('announcement'));
    }
}
