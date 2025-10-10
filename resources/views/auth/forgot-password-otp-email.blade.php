@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Quên mật khẩu - Nhập email</h2>
    <form method="POST" action="{{ route('password.otp.send') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
            @error('email')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Gửi mã OTP</button>
    </form>
    @if(session('status'))
        <div class="alert alert-success mt-2">{{ session('status') }}</div>
    @endif
</div>
@endsection
