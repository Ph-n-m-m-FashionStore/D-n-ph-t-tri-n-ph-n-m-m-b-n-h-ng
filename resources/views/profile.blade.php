@extends('layouts.app')
@section('title', 'Hồ sơ cá nhân')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">Hồ sơ cá nhân</div>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label for="name" class="form-label">Họ tên</label>
                            <input type="text" class="form-control" id="name" value="{{ Auth::user()->name ?? 'Guest' }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" value="{{ Auth::user()->email ?? 'guest@email.com' }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" value="{{ Auth::user()->phone ?? '' }}" readonly>
                        </div>
                        <a href="/orders" class="btn btn-warning">Xem đơn hàng</a>
                        @if(Auth::check())
                            <a href="{{ route('account.edit') }}" class="btn btn-primary btn-lg ms-2 shadow">
                                <i class="fa fa-edit me-1"></i> Chỉnh sửa thông tin
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
