@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center pb-3 mb-4 border-bottom">
    <div>
        <h2 class="h3 fw-bold mb-1">Dashboard</h2>
        <p class="text-muted mb-0">Overview of your job applications and upcoming follow-ups</p>
    </div>
    <div class="mt-3 mt-md-0">
        <a href="{{ route('applications.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-plus-circle"></i> Add New Application
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <!-- Total -->
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card total h-100">
            <div class="card-body">
                <div class="text-muted small fw-semibold text-uppercase">Total</div>
                <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['total'] }}</div>
                <a href="{{ route('applications.index') }}" class="small text-muted text-decoration-none mt-2 d-inline-block">View all &rarr;</a>
            </div>
        </div>
    </div>
    <!-- Applied -->
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card applied h-100">
            <div class="card-body">
                <div class="text-primary small fw-semibold text-uppercase">Applied</div>
                <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['applied'] }}</div>
                <a href="{{ route('applications.index', ['status' => 'Applied']) }}" class="small text-primary text-decoration-none mt-2 d-inline-block">Filter &rarr;</a>
            </div>
        </div>
    </div>
    <!-- Shortlisted -->
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card shortlisted h-100">
            <div class="card-body">
                <div class="text-info small fw-semibold text-uppercase">Shortlisted</div>
                <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['shortlisted'] }}</div>
                <a href="{{ route('applications.index', ['status' => 'Shortlisted']) }}" class="small text-info text-decoration-none mt-2 d-inline-block">Filter &rarr;</a>
            </div>
        </div>
    </div>
    <!-- Interview -->
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card interview h-100">
            <div class="card-body">
                <div class="text-warning small fw-semibold text-uppercase">Interview</div>
                <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['interview'] }}</div>
                <a href="{{ route('applications.index', ['status' => 'Interview']) }}" class="small text-warning text-decoration-none mt-2 d-inline-block">Filter &rarr;</a>
            </div>
        </div>
    </div>
    <!-- Selected -->
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card selected h-100">
            <div class="card-body">
                <div class="text-success small fw-semibold text-uppercase">Selected</div>
                <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['selected'] }}</div>
                <a href="{{ route('applications.index', ['status' => 'Selected']) }}" class="small text-success text-decoration-none mt-2 d-inline-block">Filter &rarr;</a>
            </div>
        </div>
    </div>
    <!-- Rejected -->
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card stat-card rejected h-100">
            <div class="card-body">
                <div class="text-danger small fw-semibold text-uppercase">Rejected</div>
                <div class="fs-2 fw-bold text-dark mt-1">{{ $stats['rejected'] }}</div>
                <a href="{{ route('applications.index', ['status' => 'Rejected']) }}" class="small text-danger text-decoration-none mt-2 d-inline-block">Filter &rarr;</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Follow-up Reminders Section -->
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bell-fill text-warning fs-5"></i>
                    <h5 class="card-title fw-bold mb-0">Follow-up Reminders</h5>
                </div>
                <span class="badge {{ $dueFollowUps->count() > 0 ? 'bg-danger' : 'bg-secondary' }}">
                    {{ $dueFollowUps->count() }} Due
                </span>
            </div>
            <div class="card-body p-0">
                @if ($dueFollowUps->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-calendar-check fs-1 text-success d-block mb-2"></i>
                        <p class="mb-0 fw-medium">All caught up!</p>
                        <small>No pending follow-ups due today or overdue.</small>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($dueFollowUps as $reminder)
                            @php
                                $isToday = $reminder->follow_up_date->isToday();
                                $isOverdue = $reminder->follow_up_date->isPast() && !$isToday;
                            @endphp
                            <div class="list-group-item list-group-item-action p-3">
                                <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                                    <h6 class="mb-1 fw-bold text-dark">
                                        Follow up with {{ $reminder->company }}
                                    </h6>
                                    <span class="badge {{ $isToday ? 'bg-warning text-dark' : 'bg-danger' }}">
                                        {{ $isToday ? 'Due Today' : 'Overdue (' . $reminder->follow_up_date->format('M d, Y') . ')' }}
                                    </span>
                                </div>
                                <p class="text-muted small mb-2">
                                    Position: <span class="fw-semibold text-secondary">{{ $reminder->position }}</span>
                                    &bull; Current Status: <span class="{{ $reminder->status_badge_class }}">{{ $reminder->status }}</span>
                                </p>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('applications.show', $reminder) }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.8rem;">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                    <a href="{{ route('applications.edit', $reminder) }}" class="btn btn-sm btn-outline-secondary py-0 px-2" style="font-size: 0.8rem;">
                                        <i class="bi bi-pencil"></i> Update Status
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Applications Section -->
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-primary fs-5"></i>
                    <h5 class="card-title fw-bold mb-0">Recent Applications</h5>
                </div>
                <a href="{{ route('applications.index') }}" class="btn btn-sm btn-outline-secondary">
                    View All
                </a>
            </div>
            <div class="card-body p-0">
                @if ($recentApplications->isEmpty())
                    <div class="p-4 text-center text-muted">
                        <i class="bi bi-folder2-open fs-1 d-block mb-2"></i>
                        <p class="mb-2 fw-medium">No job applications yet</p>
                        <a href="{{ route('applications.create') }}" class="btn btn-sm btn-primary">
                            Add your first application
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Company</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    <th>Applied Date</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentApplications as $app)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $app->company }}</span>
                                            <div class="text-muted small">{{ $app->location }}</div>
                                        </td>
                                        <td>
                                            <span>{{ $app->position }}</span>
                                            <div class="text-muted small">{{ $app->job_type }}</div>
                                        </td>
                                        <td>
                                            <span class="{{ $app->status_badge_class }}">{{ $app->status }}</span>
                                        </td>
                                        <td class="text-muted small">
                                            {{ $app->applied_date->format('M d, Y') }}
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('applications.show', $app) }}" class="btn btn-sm btn-light border" title="View details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
