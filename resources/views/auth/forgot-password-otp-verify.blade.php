@extends('layouts.app')
@section('content')
<div class="container">
    <h2>Nhập mã OTP</h2>
    <form method="POST" action="{{ route('password.otp.verify') }}">
        @csrf
        <div class="mb-3">
            <label for="otp" class="form-label">Mã OTP</label>
            <input type="text" name="otp" id="otp" class="form-control" maxlength="6" required>
            @error('otp')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Xác nhận mã OTP</button>
    </form>
    @if(session('status'))
        <div class="alert alert-success mt-2">{{ session('status') }}</div>
    @endif
</div>
@endsection
