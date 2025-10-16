@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Đặt lại mật khẩu mới</h2>
    <form method="POST" action="{{ route('password.otp.update') }}">
        @csrf
        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu mới</label>
            <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            @include('partials.password-requirements')
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Đổi mật khẩu</button>
    </form>
    
</div>
@endsection
