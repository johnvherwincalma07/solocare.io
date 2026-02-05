<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;

class AnnouncementController extends Controller
{
    public function fetchAnnouncements()
    {
        // Fetch latest announcements (newest first)
        $announcements = Announcement::orderBy('created_at', 'desc')->get();

        // Return as JSON
        return response()->json($announcements);
    }
}
