@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Chỉnh sửa thông tin tài khoản</h1>

    {{-- Notifications shown via global toast in layout --}}

    <form action="{{ route('account.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Tên</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control">
                @error('name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Số điện thoại</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
                @error('phone') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Địa chỉ</label>
            <input type="text" name="address" value="{{ old('address', $user->address) }}" class="form-control" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            @error('address') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success btn-sm fw-bold text-uppercase px-3 py-1">Lưu thay đổi</button>
            <a href="{{ route('account.show') }}" class="btn btn-link ms-2 align-self-center">Hủy</a>
        </div>
    </form>
</div>
@endsection
