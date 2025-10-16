<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Fashion Store') - Fashion Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Sidebar styles */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 0 !important; /* uniform vertical spacing for links */
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        /* Ensure dropdown wrappers don't add extra vertical gaps and make dropdown links match nav-link spacing */
        .sidebar .dropdown { margin: 0 !important; }
        .sidebar .dropdown .nav-link,
        .sidebar .nav-item .dropdown-toggle,
        .sidebar .dropdown-toggle {
            margin: 4px 0 !important;
        }

        /* Main content alignment and stacking */
        .main-content { z-index: 1; padding-top: 20px; padding-left: 24px; background-color: #f8f9fa; min-height: 100vh; }
        .sidebar { z-index: 2; }

        /* Card visuals */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        /* Tables, badges, stats */
        .table { border-radius: 10px; overflow: hidden; }
        .badge { border-radius: 20px; padding: 8px 12px; }
        .stats-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; padding: 20px; margin-bottom: 20px; }
        .stats-card .icon { font-size: 2.5rem; opacity: 0.8; }

        /* Admin action buttons: uniform size, spacing and clickable */
        .admin-action-buttons { width: 100%; }
        .admin-action-buttons form { margin: 0; }
        .admin-action-buttons .btn {
            min-height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding-left: 12px;
            padding-right: 12px;
            cursor: pointer;
            white-space: nowrap;
            width: 100%; /* make each action full-width for consistent look */
        }

        /* Stepper badges: prevent overflow and wrap nicely */
        .order-stepper { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
        .order-stepper .badge { font-size: .85rem; padding: .45rem .75rem; }

        /* Prevent some overlays from intercepting clicks unintentionally */
        .overlay, .modal-backdrop { pointer-events: auto; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Responsive Sidebar: offcanvas for small screens, fixed for md+ -->
            <div class="col-12 d-md-none">
                <nav class="navbar bg-light mb-3">
                    <div class="container-fluid">
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar">
                            <i class="fas fa-bars"></i>
                        </button>
                        <span class="navbar-brand mb-0 h1">Fashion Store</span>
                        <div class="ms-auto">
                            <a class="btn btn-outline-secondary" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng xuất</a>
                        </div>
                    </div>
                </nav>

                <div class="offcanvas offcanvas-start" tabindex="-1" id="adminSidebar" aria-labelledby="adminSidebarLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="adminSidebarLabel">Menu</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body p-0">
                        <div class="sidebar p-3">
                            <h4 class="text-white mb-4"><i class="fas fa-store me-2"></i>Fashion Store</h4>
                            <nav class="nav flex-column">
                                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a>
                                <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}"><i class="fas fa-box me-2"></i>Quản lý sản phẩm</a>
                                <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}"><i class="fas fa-comment-dots me-2"></i>Quản lý đánh giá</a>
                                <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}"><i class="fas fa-shopping-cart me-2"></i>Quản lý đơn hàng</a>
                                <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}"><i class="fas fa-users me-2"></i>Quản lý khách hàng</a>
                                <div class="dropdown">
                                    {{-- ensure dropdown sits visually inline with other nav items --}}
                                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"><i class="fas fa-chart-bar me-2"></i>Báo cáo & Thống kê</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('admin.reports.sales') }}">Báo cáo doanh thu</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.reports.products') }}">Báo cáo sản phẩm</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.reports.customers') }}">Báo cáo khách hàng</a></li>
                                    </ul>
                                </div>
                                <hr class="text-white-50">
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop sidebar -->
            <div class="col-md-3 col-lg-2 d-none d-md-block px-0">
                <div class="sidebar">
                    <div class="p-3">
                        <h4 class="text-white mb-4">
                            <i class="fas fa-store me-2"></i>
                            Fashion Store
                        </h4>
                        <nav class="nav flex-column">
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                                <i class="fas fa-box me-2"></i>
                                Quản lý sản phẩm
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" href="{{ route('admin.reviews.index') }}">
                                <i class="fas fa-comment-dots me-2"></i>
                                Quản lý đánh giá
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Quản lý đơn hàng
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}" href="{{ route('admin.customers.index') }}">
                                <i class="fas fa-users me-2"></i>
                                Quản lý khách hàng
                            </a>
                            <div class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    Báo cáo & Thống kê
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('admin.reports.sales') }}">Báo cáo doanh thu</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.reports.products') }}">Báo cáo sản phẩm</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.reports.customers') }}">Báo cáo khách hàng</a></li>
                                </ul>
                            </div>
                            <hr class="text-white-50">
                            <!-- Back-to-home link removed per request -->
                            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Đăng xuất
                            </a>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0">@yield('page-title', 'Dashboard')</h2>
                        <div class="d-flex align-items-center">
                            <span class="text-muted me-3">Xin chào, {{ auth()->user()->name }}</span>
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle me-1"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('profile.view') }}">Hồ sơ cá nhân</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng xuất</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Page Content -->
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('scripts')
</body>
</html>

<style>
/* Ensure buttons are above decorative elements in admin UI to remain clickable */
.card { position: relative; z-index: 1; }
.card .btn { position: relative; z-index: 3; }
.badge { position: relative; z-index: 1; }
/* Make timeline connector behind content */
.timeline::before { z-index: 0; }
</style>
