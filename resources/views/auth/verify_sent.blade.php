@extends('layouts.app')
@section('title', 'Xác thực email')
@section('content')
<div class="container py-5">
    <div class="bg-white rounded-lg shadow p-6 max-w-2xl mx-auto text-center">
        <h2 class="text-2xl font-bold mb-4">Thư kích hoạt đã được gửi</h2>
        <p class="mb-4">Một email kích hoạt đã được gửi tới <strong>{{ $email }}</strong>. Vui lòng mở email và click vào liên kết để xác thực tài khoản.</p>
        <a href="{{ route('login') }}" class="btn btn-primary">Quay lại trang đăng nhập</a>
    </div>
</div>
@endsection
