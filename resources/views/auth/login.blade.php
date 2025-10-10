@extends('layouts.app')
@section('title', 'Đăng nhập')
@section('content')
<div class="auth-container">
    <div class="auth-tabs">
        <div class="auth-forms">
            <h2 style="text-align:center;">Đăng nhập</h2>
            <form method="POST" action="{{ route('login') }}" class="auth-form active">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Nhập email của bạn" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" name="password" id="password" placeholder="Nhập mật khẩu" required>
                </div>
                <button type="submit" class="btn-primary">Đăng nhập</button>
                <a href="{{ route('password.otp.email') }}" class="forgot-password">Quên mật khẩu?</a>
            </form>
            <div class="divider"></div>
            <h2 style="text-align:center;">Tạo tài khoản mới</h2>
            <a href="{{ route('register') }}" class="btn-secondary">Đăng ký ngay</a>
            <div class="benefits-box">
                <p>Đăng ký tài khoản để nhận ưu đãi và trải nghiệm mua sắm tốt hơn!</p>
                <p>• Thanh toán nhanh chóng</p>
                <p>• Nhận mã giảm giá</p>
                <p>• Theo dõi đơn hàng</p>
            </div>
        </div>
    </div>
</div>
@endsection
