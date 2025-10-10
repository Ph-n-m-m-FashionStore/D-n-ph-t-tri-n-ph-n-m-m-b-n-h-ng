@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Thông tin tài khoản</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded p-4">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <p><strong>Tên:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Số điện thoại:</strong> {{ $user->phone ?? 'Chưa cập nhật' }}</p>
                <p><strong>Địa chỉ:</strong> {{ $user->address ?? 'Chưa cập nhật' }}</p>
            </div>
            <div>
                <a href="{{ route('account.edit') }}" class="btn btn-primary btn-lg shadow" role="button">
                    <i class="fa fa-edit me-2" aria-hidden="true"></i> Chỉnh sửa
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded p-4 mt-4">
        <h2 class="h5 mb-3">Đổi mật khẩu</h2>
        <form action="{{ route('account.password') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Mật khẩu hiện tại</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Mật khẩu mới</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Xác nhận mật khẩu mới</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">Đổi mật khẩu</button>
        </form>
    </div>
</div>
@endsection
