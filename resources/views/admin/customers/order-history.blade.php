@extends('admin.layout')

@section('title', 'Lịch sử mua hàng - ' . $customer->name)
@section('page-title', 'Lịch sử mua hàng - ' . $customer->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Lịch sử mua hàng</h4>
        <p class="text-muted mb-0">Khách hàng: {{ $customer->name }}</p>
    </div>
    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>
        Quay lại
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Trạng thái</label>
                <select class="form-select" name="status">
                    <option value="">Tất cả</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                    <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Đang giao</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Từ ngày</label>
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Đến ngày</label>
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search me-1"></i>
                        Lọc
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-body">
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Sản phẩm</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thanh toán</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>
                                    <strong>#{{ $order->id }}</strong>
                                </td>
                                <td>
                                    <div>
                                        <span class="badge bg-light text-dark">
                                            {{ $order->orderItems->count() }} sản phẩm
                                        </span>
                                        @if($order->orderItems->count() > 0)
                                            <br>
                                            <small class="text-muted">
                                                {{ $order->orderItems->first()->product->name }}
                                                @if($order->orderItems->count() > 1)
                                                    và {{ $order->orderItems->count() - 1 }} sản phẩm khác
                                                @endif
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">
                                        {{ number_format($order->total, 0, ',', '.') }}₫
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $order->status_badge }}">
                                        @switch($order->status)
                                            @case('pending') Chờ xử lý @break
                                            @case('confirmed') Đã xác nhận @break
                                            @case('shipping') Đang giao @break
                                            @case('completed') Hoàn thành @break
                                            @case('canceled') Đã hủy @break
                                        @endswitch
                                    </span>
                                </td>
                                <td>
                                    @if($order->payment)
                                        <span class="badge bg-{{ $order->payment->status == 'paid' ? 'success' : 'warning' }}">
                                            {{ $order->payment->status == 'paid' ? 'Đã thanh toán' : 'Chờ thanh toán' }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Chưa thanh toán</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $order->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}" 
                                       class="btn btn-outline-primary btn-sm" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Khách hàng chưa có đơn hàng nào</h5>
                <p class="text-muted">Đơn hàng sẽ xuất hiện ở đây khi khách hàng đặt hàng</p>
            </div>
        @endif
    </div>
</div>
@endsection

