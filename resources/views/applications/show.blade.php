@extends('layouts.app')

@section('title', $application->position . ' at ' . $application->company)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <!-- Header & Nav Buttons -->
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center pb-3 mb-4 border-bottom gap-2">
            <div>
                <a href="{{ route('applications.index') }}" class="text-decoration-none text-muted small d-inline-flex align-items-center gap-1 mb-1">
                    <i class="bi bi-arrow-left"></i> Back to all applications
                </a>
                <h2 class="h3 fw-bold mb-0 text-dark">{{ $application->position }}</h2>
                <div class="text-secondary fs-5">{{ $application->company }}</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('applications.edit', $application) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <form action="{{ route('applications.destroy', $application) }}" 
                      method="POST" 
                      class="d-inline"
                      onsubmit="return confirm('Are you sure you want to delete this job application? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger d-inline-flex align-items-center gap-1">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        <!-- Application Details Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <!-- Status & Key Badges -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pb-3 mb-4 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted fw-semibold">Current Status:</span>
                        <span class="{{ $application->status_badge_class }} fs-6">{{ $application->status }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="bi bi-briefcase me-1"></i>{{ $application->job_type }}
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="bi bi-geo-alt me-1"></i>{{ $application->location }}
                        </span>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="row g-4 mb-4">
                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small text-uppercase fw-semibold">Applied Date</div>
                        <div class="fw-bold fs-6 text-dark mt-1">
                            <i class="bi bi-calendar-check text-primary me-1"></i>
                            {{ $application->applied_date->format('M d, Y') }}
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small text-uppercase fw-semibold">Follow-Up Date</div>
                        <div class="fw-bold fs-6 text-dark mt-1">
                            @if ($application->follow_up_date)
                                <i class="bi bi-bell text-warning me-1"></i>
                                {{ $application->follow_up_date->format('M d, Y') }}
                                @php
                                    $isToday = $application->follow_up_date->isToday();
                                    $isPast = $application->follow_up_date->isPast() && !$isToday;
                                    $isActionable = !in_array($application->status, ['Selected', 'Rejected']);
                                @endphp
                                @if ($isActionable && $isToday)
                                    <div class="badge bg-warning text-dark mt-1 d-block">Due Today</div>
                                @elseif ($isActionable && $isPast)
                                    <div class="badge bg-danger mt-1 d-block">Overdue</div>
                                @endif
                            @else
                                <span class="text-muted fw-normal">Not scheduled</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small text-uppercase fw-semibold">Salary / Rate</div>
                        <div class="fw-bold fs-6 text-dark mt-1">
                            @if ($application->salary !== null)
                                <i class="bi bi-currency-dollar text-success me-1"></i>
                                {{ number_format($application->salary, 2) }}
                            @else
                                <span class="text-muted fw-normal">Not specified</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-3">
                        <div class="text-muted small text-uppercase fw-semibold">Job Posting</div>
                        <div class="mt-1">
                            @if ($application->job_url)
                                <a href="{{ $application->job_url }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                                    <i class="bi bi-box-arrow-up-right"></i> Open Link
                                </a>
                            @else
                                <span class="text-muted fw-normal">None provided</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Notes Section -->
                <div class="border-top pt-4">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-sticky text-secondary"></i> Application & Interview Notes
                    </h5>
                    @if ($application->notes)
                        <div class="p-3 bg-light rounded border text-secondary" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;">{{ $application->notes }}</div>
                    @else
                        <div class="p-3 bg-light rounded text-muted text-center small">
                            No notes added yet for this application. <a href="{{ route('applications.edit', $application) }}">Click here to add notes</a>.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer Timestamps -->
            <div class="card-footer bg-white border-top text-muted small d-flex justify-content-between">
                <span>Created: {{ $application->created_at->format('M d, Y H:i') }}</span>
                <span>Last updated: {{ $application->updated_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
