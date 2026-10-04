<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationRequest;
use App\Models\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the user's job applications with search and status filtering.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');

        // Always scope queries to the currently authenticated user
        $query = $request->user()->applications();

        // Search by company or position
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('company', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Filter by status if selected
        if (!empty($status) && in_array($status, Application::STATUSES, true)) {
            $query->where('status', $status);
        }

        // Order by latest applied date
        $applications = $query->latest('applied_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('applications.index', [
            'applications' => $applications,
            'search'       => $search,
            'selectedStatus' => $status,
            'statuses'     => Application::STATUSES,
        ]);
    }

    /**
     * Show the form for creating a new job application.
     */
    public function create(): View
    {
        return view('applications.create', [
            'statuses' => Application::STATUSES,
            'jobTypes' => Application::JOB_TYPES,
        ]);
    }

    /**
     * Store a newly created job application in storage.
     */
    public function store(ApplicationRequest $request): RedirectResponse
    {
        // Eloquent relationship automatically sets the foreign key user_id
        $application = $request->user()->applications()->create($request->validated());

        return redirect()->route('applications.show', $application)
            ->with('success', 'Job application added successfully!');
    }

    /**
     * Display the specified job application.
     */
    public function show(Request $request, Application $application): View
    {
        $this->authorizeOwnership($request, $application);

        return view('applications.show', compact('application'));
    }

    /**
     * Show the form for editing the specified job application.
     */
    public function edit(Request $request, Application $application): View
    {
        $this->authorizeOwnership($request, $application);

        return view('applications.edit', [
            'application' => $application,
            'statuses'    => Application::STATUSES,
            'jobTypes'    => Application::JOB_TYPES,
        ]);
    }

    /**
     * Update the specified job application in storage.
     */
    public function update(ApplicationRequest $request, Application $application): RedirectResponse
    {
        $this->authorizeOwnership($request, $application);

        $application->update($request->validated());

        return redirect()->route('applications.show', $application)
            ->with('success', 'Job application updated successfully!');
    }

    /**
     * Remove the specified job application from storage.
     */
    public function destroy(Request $request, Application $application): RedirectResponse
    {
        $this->authorizeOwnership($request, $application);

        $company = $application->company;
        $position = $application->position;

        $application->delete();

        return redirect()->route('applications.index')
            ->with('success', "Application for {$position} at {$company} deleted successfully.");
    }

    /**
     * Helper to verify ownership.
     * Prevents users from viewing/modifying other users' records by guessing IDs in the URL.
     */
    private function authorizeOwnership(Request $request, Application $application): void
    {
        if ($application->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action. You can only manage your own job applications.');
        }
    }
}
