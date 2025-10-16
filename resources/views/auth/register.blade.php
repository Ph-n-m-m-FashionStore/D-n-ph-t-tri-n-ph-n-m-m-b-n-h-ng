@extends('layouts.app')
@section('title', 'Đăng ký')
@section('content')
<div class="auth-container">
    <div class="auth-tabs">
        <div class="auth-forms">
            <h2 style="text-align:center;">Tạo tài khoản mới</h2>
            <form method="POST" action="{{ route('register') }}" class="auth-form active">
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
                    <label for="name">Họ và tên</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Nhập họ và tên" required autofocus>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Nhập email" required>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" name="password" id="password" placeholder="Nhập mật khẩu" class="@error('password') is-invalid @enderror" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @include('partials.password-requirements')
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Nhập lại mật khẩu</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Nhập lại mật khẩu" required>
                </div>
                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Nhập số điện thoại (tùy chọn)">
                </div>
                <div class="form-group">
                    <label for="address">Địa chỉ</label>
                    <input type="text" name="address" id="address" value="{{ old('address') }}" placeholder="Nhập địa chỉ (tùy chọn)">
                </div>
                <button type="submit" class="btn-primary">Đăng ký</button>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var pwInput = document.getElementById('password');
                    var pwLength = document.getElementById('pw-length');
                    var pwCase = document.getElementById('pw-case');
                    var pwSpecial = document.getElementById('pw-special');
                    pwInput.addEventListener('input', function() {
                        var val = pwInput.value;
                        // Kiểm tra độ dài
                        if (val.length >= 8) {
                            pwLength.style.color = '#28a745';
                            pwLength.querySelector('i').className = 'fa fa-check';
                        } else {
                            pwLength.style.color = 'red';
                            pwLength.querySelector('i').className = 'fa fa-times';
                        }
                        // Kiểm tra chữ hoa và thường
                        if (/[a-z]/.test(val) && /[A-Z]/.test(val)) {
                            pwCase.style.color = '#28a745';
                            pwCase.querySelector('i').className = 'fa fa-check';
                        } else {
                            pwCase.style.color = 'red';
                            pwCase.querySelector('i').className = 'fa fa-times';
                        }
                        // Kiểm tra số hoặc ký tự đặc biệt
                        if (/[0-9!@#$%^&*(),.?":{}|<>]/.test(val)) {
                            pwSpecial.style.color = '#28a745';
                            pwSpecial.querySelector('i').className = 'fa fa-check';
                        } else {
                            pwSpecial.style.color = 'red';
                            pwSpecial.querySelector('i').className = 'fa fa-times';
                        }
                        // Viền đỏ cho input nếu có điều kiện sai
                        if (
                            val.length < 8 ||
                            !(/[a-z]/.test(val) && /[A-Z]/.test(val)) ||
                            !(/[0-9!@#$%^&*(),.?":{}|<>]/.test(val))
                        ) {
                            pwInput.style.borderColor = 'red';
                        } else {
                            pwInput.style.borderColor = '#28a745';
                        }
                    });
                });
                </script>
            </form>
            <div class="divider"></div>
            <a href="{{ route('login') }}" class="btn-secondary">Đã có tài khoản? Đăng nhập</a>
            <div class="benefits-box">
                <h3>Lợi ích khi đăng ký tài khoản</h3>
                <p>• Thanh toán nhanh chóng</p>
                <p>• Nhận mã giảm giá và khuyến mãi</p>
                <p>• Theo dõi lịch sử đơn hàng</p>
                <p>• Lưu sản phẩm yêu thích</p>
            </div>
        </div>
    </div>
</div>
@endsection
