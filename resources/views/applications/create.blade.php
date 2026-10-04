@extends('layouts.app')

@section('title', 'Add Job Application')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9 col-xl-8">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h2 class="h3 fw-bold mb-0">Add Job Application</h2>
                <p class="text-muted small mb-0">Record details of a new job application you've submitted</p>
            </div>
            <a href="{{ route('applications.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form action="{{ route('applications.store') }}" method="POST">
                    @csrf

                    <!-- Row 1: Company & Position -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="company" class="form-label fw-semibold">
                                Company Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="company" 
                                   id="company" 
                                   class="form-control @error('company') is-invalid @enderror" 
                                   value="{{ old('company') }}" 
                                   placeholder="e.g. Google, Acme Corp" 
                                   required>
                            @error('company')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="position" class="form-label fw-semibold">
                                Job Position / Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="position" 
                                   id="position" 
                                   class="form-control @error('position') is-invalid @enderror" 
                                   value="{{ old('position') }}" 
                                   placeholder="e.g. Junior Laravel Developer" 
                                   required>
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 2: Location & Job Type -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="location" class="form-label fw-semibold">
                                Location <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   name="location" 
                                   id="location" 
                                   class="form-control @error('location') is-invalid @enderror" 
                                   value="{{ old('location') }}" 
                                   placeholder="e.g. Remote, New York, NY" 
                                   required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="job_type" class="form-label fw-semibold">
                                Job Type <span class="text-danger">*</span>
                            </label>
                            <select name="job_type" id="job_type" class="form-select @error('job_type') is-invalid @enderror" required>
                                <option value="" disabled {{ old('job_type') ? '' : 'selected' }}>Select Job Type</option>
                                @foreach ($jobTypes as $type)
                                    <option value="{{ $type }}" {{ old('job_type', 'Full-time') === $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('job_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 3: Salary & Status -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="salary" class="form-label fw-semibold">
                                Salary / Compensation <span class="text-muted fw-normal">(Optional)</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                       step="0.01" 
                                       name="salary" 
                                       id="salary" 
                                       class="form-control @error('salary') is-invalid @enderror" 
                                       value="{{ old('salary') }}" 
                                       placeholder="e.g. 75000">
                                @error('salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label fw-semibold">
                                Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                                @foreach ($statuses as $st)
                                    <option value="{{ $st }}" {{ old('status', 'Applied') === $st ? 'selected' : '' }}>
                                        {{ $st }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 4: Applied Date & Follow-up Date -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="applied_date" class="form-label fw-semibold">
                                Date Applied <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   name="applied_date" 
                                   id="applied_date" 
                                   class="form-control @error('applied_date') is-invalid @enderror" 
                                   value="{{ old('applied_date', date('Y-m-d')) }}" 
                                   required>
                            @error('applied_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="follow_up_date" class="form-label fw-semibold">
                                Next Follow-up Date <span class="text-muted fw-normal">(Optional)</span>
                            </label>
                            <input type="date" 
                                   name="follow_up_date" 
                                   id="follow_up_date" 
                                   class="form-control @error('follow_up_date') is-invalid @enderror" 
                                   value="{{ old('follow_up_date') }}">
                            <div class="form-text">We'll alert you on your dashboard when this date arrives.</div>
                            @error('follow_up_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 5: Job Posting URL -->
                    <div class="mb-3">
                        <label for="job_url" class="form-label fw-semibold">
                            Job Posting URL <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                            <input type="url" 
                                   name="job_url" 
                                   id="job_url" 
                                   class="form-control @error('job_url') is-invalid @enderror" 
                                   value="{{ old('job_url') }}" 
                                   placeholder="https://example.com/jobs/php-dev">
                            @error('job_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Row 6: Notes -->
                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold">
                            Notes / Interview Feedback <span class="text-muted fw-normal">(Optional)</span>
                        </label>
                        <textarea name="notes" 
                                  id="notes" 
                                  rows="4" 
                                  class="form-control @error('notes') is-invalid @enderror" 
                                  placeholder="Add details about contacts, interview stages, questions asked, or follow-up discussion points...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="{{ route('applications.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold">
                            <i class="bi bi-check-circle me-1"></i> Save Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
