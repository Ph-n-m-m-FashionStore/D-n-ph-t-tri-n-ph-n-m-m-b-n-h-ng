@extends('admin.layout')

@section('title', 'Quản lý đơn hàng')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-3">Đơn hàng</h1>

    <div class="row mb-3">
        <div class="col">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Tìm theo mã đơn, tên, SĐT, email">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        @foreach(['pending'=>'Chờ xử lý','confirmed'=>'Đã xác nhận','shipping'=>'Đang giao','completed'=>'Hoàn tất','canceled'=>'Đã hủy'] as $k=>$v)
                            <option value="{{ $k }}" {{ request('status')===$k?'selected':'' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="payment_status" class="form-select">
                        <option value="">Tất cả thanh toán</option>
                        @foreach(['pending'=>'Chưa thanh toán','paid'=>'Đã thanh toán'] as $k=>$v)
                            <option value="{{ $k }}" {{ request('payment_status')===$k?'selected':'' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100">Lọc</button>
                </div>
            </form>
        </div>
    </div>

    @isset($statusCounts)
    <div class="row mb-3">
        @foreach($statusCounts as $k=>$count)
            <div class="col-md-2 mb-2">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <div class="fw-bold text-uppercase" style="font-size: 12px;">{{ $k }}</div>
                        <div class="fs-4">{{ $count }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @endisset

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
                <tr>
                    <th>Mã</th>
                    <th>Khách hàng</th>
                    <th>Ngày đặt</th>
                    <th>Tổng</th>
                    <th>TT đơn</th>
                    <th>TT thanh toán</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->user->name ?? $order->name }}</td>
                        <td>{{ optional($order->created_at)->format('d/m/Y H:i') }}</td>
                        <td>{{ number_format($order->total ?? 0, 0, ',', '.') }}₫</td>
                        <td><span class="badge bg-secondary">{{ ucfirst($order->status) }}</span></td>
                        <td><span class="badge {{ optional($order->payment)->status==='paid'?'bg-success':'bg-warning text-dark' }}">{{ optional($order->payment)->status ?? 'pending' }}</span></td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">Xem</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">Chưa có đơn hàng.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($orders, 'links'))
        <div class="mt-3">{{ $orders->links('pagination::simple-bootstrap-4') }}</div>
    @endif
</div>
@endsection













