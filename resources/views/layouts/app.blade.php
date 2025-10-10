<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Shop Quần Áo Online')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .navbar {
            background: #0055a5 !important;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }
        body {
            color: #333;
            background-color: #f8f8f8;
            line-height: 1.6;
        }
        a {
            text-decoration: none;
            color: #0066cc;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .auth-container {
            display: flex;
            min-height: calc(100vh - 150px);
            padding: 40px 0;
        }
        .auth-tabs {
            display: flex;
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        .auth-forms {
            flex: 1;
            padding: 40px;
        }
        .auth-form h2 {
            margin: 20px 0;
            font-weight: normal;
            font-size: 24px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .form-group input:focus {
            outline: none;
            border-color: #000;
        }
        .password-requirements {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .password-requirements ul {
            list-style: none;
            margin-top: 5px;
        }
        .password-requirements li {
            margin-bottom: 3px;
            display: flex;
            align-items: center;
        }
        .password-requirements i {
            margin-right: 5px;
            font-size: 12px;
        }
        .divider {
            height: 1px;
            background: #eee;
            margin: 25px 0;
        }
        .btn-primary {
            display: block;
            width: 100%;
            padding: 15px;
            background: #000;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .btn-primary:hover {
            background: #333;
        }
        .btn-secondary {
            display: block;
            width: 100%;
            padding: 15px;
            background: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
            text-align: center;
            margin-top: 15px;
        }
        .btn-secondary:hover {
            background: #e8e8e8;
        }
        .forgot-password {
            text-align: center;
            margin-top: 15px;
            display: block;
        }
        .benefits-box {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 4px;
            margin-top: 20px;
        }
        .benefits-box h3 {
            margin-bottom: 10px;
            font-weight: 500;
        }
        .benefits-box p {
            font-size: 14px;
            color: #666;
        }
        @media (max-width: 768px) {
            .auth-tabs {
                flex-direction: column;
            }
        }
        @media (max-width: 480px) {
            .auth-forms {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/">FashionStore</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="/">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="/products">Sản phẩm</a></li>
                    <li class="nav-item"><a class="nav-link" href="/cart">Giỏ hàng</a></li>
                    <li class="nav-item"><a class="nav-link" href="/orders">Đơn hàng</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    @if(Auth::check())
                        <li class="nav-item"><a class="nav-link" href="{{ route('account.show') }}">Tài khoản</a></li>
                        <li class="nav-item ms-3 d-flex align-items-center">
                            <form method="POST" action="/logout" class="d-inline m-0">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light py-1">Đăng xuất</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="/login">Đăng nhập</a></li>
                        <li class="nav-item"><a class="nav-link" href="/register">Đăng ký</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    {{-- Single top-right toast notification (shows success/error/first validation error) --}}
    @php
        $toastMessage = session('success') ?? session('error') ?? (isset($errors) && $errors->any() ? $errors->first() : null);
        if (session('success')) {
            $toastClass = 'bg-success text-white';
        } elseif (session('error')) {
            $toastClass = 'bg-danger text-white';
        } elseif (isset($errors) && $errors->any()) {
            $toastClass = 'bg-warning text-dark';
        } else {
            $toastClass = null;
        }
    @endphp

    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div id="toast-container" style="position: fixed; top: 1rem; right: 1rem; z-index: 1080;">
                @if($toastMessage)
                <div id="globalToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2000" data-bs-autohide="true">
                    <div class="toast-header {{ $toastClass ?? '' }}">
                        <strong class="me-auto">Thông báo</strong>
                        <small></small>
                        <button type="button" class="btn-close btn-close-white ms-2 mb-1" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        {{ $toastMessage }}
                    </div>
                </div>
            @endif
        </div>
    </div>
    @yield('content')
    <footer class="mt-5 py-4 bg-light border-top">
        <div class="container text-center text-muted">© 2025 Fashion Store. All rights reserved.</div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toastEl = document.getElementById('globalToast');
            if (toastEl) {
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });
    </script>
</body>
</html>
