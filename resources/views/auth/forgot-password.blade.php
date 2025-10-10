@extends('layouts.app')
@section('title', 'Quên mật khẩu')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="mb-4">Quên mật khẩu</h2>
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group mb-3">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required autofocus>
                </div>
                <button type="submit" class="btn btn-primary w-100">Gửi liên kết đặt lại mật khẩu</button>
            </form>
        </div>
    </div>
</div>
@endsection
