<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the CareerFlow dashboard with metrics, reminders, and recent activity.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Application metrics for the current user
        $stats = [
            'total'       => $user->applications()->count(),
            'applied'     => $user->applications()->where('status', Application::STATUS_APPLIED)->count(),
            'shortlisted' => $user->applications()->where('status', Application::STATUS_SHORTLISTED)->count(),
            'interview'   => $user->applications()->where('status', Application::STATUS_INTERVIEW)->count(),
            'selected'    => $user->applications()->where('status', Application::STATUS_SELECTED)->count(),
            'rejected'    => $user->applications()->where('status', Application::STATUS_REJECTED)->count(),
        ];

        // Reminders: applications with follow_up_date <= today and not yet Selected/Rejected
        $dueFollowUps = $user->applications()
            ->dueFollowUps()
            ->get();

        // Recent applications (latest 5)
        $recentApplications = $user->applications()
            ->latest('created_at')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'dueFollowUps', 'recentApplications'));
    }
}
