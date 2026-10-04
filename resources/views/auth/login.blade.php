@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center mt-4">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 56px; height: 56px;">
                        <i class="bi bi-person-lock fs-2"></i>
                    </div>
                    <h4 class="card-title fw-bold">Sign In</h4>
                    <p class="text-muted small mb-0">Track and organize your job search</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" 
                                   name="email" 
                                   id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   placeholder="name@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" 
                                   name="password" 
                                   id="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   required 
                                   placeholder="••••••••">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-4 form-check">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small text-muted" for="remember">Keep me logged in</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Log In
                    </button>
                </form>

                <hr class="my-4">

                <div class="text-center small text-muted">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-semibold">Create one</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
