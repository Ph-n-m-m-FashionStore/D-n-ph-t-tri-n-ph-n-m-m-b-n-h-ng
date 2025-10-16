@extends('admin.layout')

@section('title', 'Thông tin cá nhân')
@section('page-title', 'Thông tin cá nhân')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-user-edit me-2"></i> Thông tin cá nhân
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-2">
                        <label class="form-label">Tên shop:</label>
                        <input class="form-control" value="{{ config('app.name', 'Fashion Store') }}" readonly disabled />
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Địa chỉ:</label>
                        <input name="address" class="form-control" value="{{ old('address', auth()->user()->address ?? '') }}" />
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Mã bưu chính:</label>
                        <input name="postal_code" class="form-control" value="{{ old('postal_code', auth()->user()->postal_code ?? '') }}" />
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Tên chủ shop:</label>
                        <input name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" />
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Địa chỉ thường trú:</label>
                        <input name="owner_address" class="form-control" value="{{ old('owner_address', auth()->user()->address ?? '') }}" />
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Mã số thuế:</label>
                        <input name="tax_id" class="form-control" value="{{ old('tax_id', auth()->user()->tax_id ?? '') }}" />
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">Lưu thông tin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <i class="fas fa-key me-2"></i> Đổi mật khẩu
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.password') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu cũ:</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Nhập mật khẩu" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới:</label>
                        <input type="password" name="password" id="new_password" class="form-control @error('password') is-invalid @enderror" placeholder="Nhập mật khẩu" />
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @include('partials.password-requirements')
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Xác nhận mật khẩu:</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Nhập mật khẩu" />
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="password-match-feedback" class="mt-1" style="display:none;"></div>
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-dark">Đổi mật khẩu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@endsection
