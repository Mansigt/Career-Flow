<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CareerFlow') - Smart Job Application Tracker</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Clean Custom Styles -->
    <style>
        :root {
            --cf-primary: #3b82f6;
            --cf-dark: #1e293b;
            --cf-bg: #f8fafc;
        }
        body {
            background-color: var(--cf-bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #334155;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .navbar-brand i {
            color: #60a5fa;
        }
        .nav-link.active {
            font-weight: 600;
            color: #ffffff !important;
            border-bottom: 2px solid #60a5fa;
        }
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .stat-card {
            border-left-width: 4px;
        }
        .stat-card.total { border-left-color: #64748b; }
        .stat-card.applied { border-left-color: #3b82f6; }
        .stat-card.shortlisted { border-left-color: #06b6d4; }
        .stat-card.interview { border-left-color: #f59e0b; }
        .stat-card.selected { border-left-color: #10b981; }
        .stat-card.rejected { border-left-color: #ef4444; }
        
        .badge {
            font-weight: 500;
            padding: 0.4em 0.7em;
            border-radius: 0.375rem;
        }
        .table > :not(caption) > * > * {
            padding: 0.85rem 0.85rem;
            vertical-align: middle;
        }
        footer {
            margin-top: auto;
            border-top: 1px solid #e2e8f0;
            background: #ffffff;
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm py-2">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ auth()->check() ? route('dashboard') : route('login') }}">
                <i class="bi bi-briefcase-fill fs-4"></i>
                <span>CareerFlow</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarContent">
                @auth
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('applications.index') ? 'active' : '' }}" href="{{ route('applications.index') }}">
                                <i class="bi bi-list-check me-1"></i> Applications
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('applications.create') ? 'active' : '' }}" href="{{ route('applications.create') }}">
                                <i class="bi bi-plus-circle me-1"></i> Add Application
                            </a>
                        </li>
                    </ul>

                    <ul class="navbar-nav ms-auto align-items-lg-center">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 text-white" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle fs-5 text-secondary"></i>
                                <span>{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                                <li class="dropdown-item-text text-muted small">
                                    Signed in as<br><strong>{{ auth()->user()->email }}</strong>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @else
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
                                <i class="bi bi-person-plus me-1"></i> Register
                            </a>
                        </li>
                    </ul>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Container Content -->
    <main class="container py-4">
        <!-- Flash Success Notification -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Flash Error Notification -->
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Clean Footer -->
    <footer class="py-3 text-center text-muted small">
        <div class="container">
            <span>CareerFlow &copy; {{ date('Y') }} — Built with Laravel {{ app()->version() }} & Bootstrap 5</span>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
