@extends('layouts.app')

@section('title', 'All Applications')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center pb-3 mb-4 border-bottom">
    <div>
        <h2 class="h3 fw-bold mb-1">Job Applications</h2>
        <p class="text-muted mb-0">Search, filter, and track all your active applications</p>
    </div>
    <div class="mt-3 mt-md-0">
        <a href="{{ route('applications.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-plus-circle"></i> Add Application
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('applications.index') }}" class="row g-2 align-items-center">
            <!-- Search Input -->
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Search by company or position..." 
                           value="{{ $search }}">
                </div>
            </div>

            <!-- Status Dropdown Filter -->
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-funnel text-muted"></i></span>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach ($statuses as $stat)
                            <option value="{{ $stat }}" {{ $selectedStatus === $stat ? 'selected' : '' }}>
                                {{ $stat }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    Filter
                </button>
                @if (!empty($search) || !empty($selectedStatus))
                    <a href="{{ route('applications.index') }}" class="btn btn-outline-secondary" title="Reset Filters">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Applications Table Card -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if ($applications->isEmpty())
            <div class="p-5 text-center text-muted">
                <i class="bi bi-search fs-1 d-block mb-3 text-secondary"></i>
                <h5 class="fw-semibold text-dark">No applications found</h5>
                @if (!empty($search) || !empty($selectedStatus))
                    <p class="mb-3">No applications match your active search/filter criteria.</p>
                    <a href="{{ route('applications.index') }}" class="btn btn-outline-primary btn-sm">Clear Filters</a>
                @else
                    <p class="mb-3">You haven't added any job applications yet.</p>
                    <a href="{{ route('applications.create') }}" class="btn btn-primary btn-sm">Add Your First Application</a>
                @endif
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-muted small text-uppercase">
                        <tr>
                            <th>Company</th>
                            <th>Position</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Applied Date</th>
                            <th>Follow-up Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $app)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $app->company }}</div>
                                    @if ($app->job_url)
                                        <a href="{{ $app->job_url }}" target="_blank" rel="noopener noreferrer" class="small text-decoration-none text-muted">
                                            <i class="bi bi-box-arrow-up-right me-1"></i>Job Posting
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $app->position }}</div>
                                    <span class="badge bg-light text-dark border">{{ $app->job_type }}</span>
                                </td>
                                <td class="text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $app->location }}
                                </td>
                                <td>
                                    <span class="{{ $app->status_badge_class }}">{{ $app->status }}</span>
                                </td>
                                <td class="text-muted small">
                                    {{ $app->applied_date->format('M d, Y') }}
                                </td>
                                <td>
                                    @if ($app->follow_up_date)
                                        @php
                                            $isToday = $app->follow_up_date->isToday();
                                            $isPast = $app->follow_up_date->isPast() && !$isToday;
                                            $isActionable = !in_array($app->status, ['Selected', 'Rejected']);
                                        @endphp
                                        <div class="small fw-semibold {{ $isActionable && ($isToday || $isPast) ? 'text-danger' : 'text-muted' }}">
                                            <i class="bi bi-calendar-event me-1"></i>{{ $app->follow_up_date->format('M d, Y') }}
                                        </div>
                                        @if ($isActionable && $isToday)
                                            <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Due Today</span>
                                        @elseif ($isActionable && $isPast)
                                            <span class="badge bg-danger" style="font-size: 0.7rem;">Overdue</span>
                                        @endif
                                    @else
                                        <span class="text-muted small">&mdash;</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- View -->
                                        <a href="{{ route('applications.show', $app) }}" class="btn btn-outline-primary" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <!-- Edit -->
                                        <a href="{{ route('applications.edit', $app) }}" class="btn btn-outline-secondary" title="Edit Application">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <!-- Delete with Confirmation -->
                                        <form action="{{ route('applications.destroy', $app) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete the application for {{ addslashes($app->position) }} at {{ addslashes($app->company) }}? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete Application">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($applications->hasPages())
                <div class="card-footer bg-white border-top py-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $applications->firstItem() }} to {{ $applications->lastItem() }} of {{ $applications->total() }} applications
                    </div>
                    <div>
                        {{ $applications->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
