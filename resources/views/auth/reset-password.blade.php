@extends('layouts.app')
@section('title', 'Đặt lại mật khẩu')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4">Đặt lại mật khẩu</h2>
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ request('email') }}">
                <div class="form-group mb-3">
                    <label for="password">Mật khẩu mới</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="form-group mb-3">
                    <label for="password_confirmation">Nhập lại mật khẩu mới</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Đặt lại mật khẩu</button>
            </form>
        </div>
    </div>
</div>
@endsection
