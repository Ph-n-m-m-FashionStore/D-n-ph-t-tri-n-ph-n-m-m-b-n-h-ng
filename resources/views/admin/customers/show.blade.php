@extends('admin.layout')

@section('title', 'Chi tiết khách hàng: ' . $customer->name)
@section('page-title', 'Chi tiết khách hàng: ' . $customer->name)

@section('content')
<div class="row">
    <div class="col-lg-4">
        <!-- Customer Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    Thông tin khách hàng
                </h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="avatar bg-primary text-white rounded-circle mx-auto mb-3" 
                         style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                        {{ strtoupper(substr($customer->name, 0, 1)) }}
                    </div>
                    <h4>{{ $customer->name }}</h4>
                    <p class="text-muted">{{ $customer->email }}</p>
                </div>

                <div class="row text-center mb-4">
                    <div class="col-6">
                        <h5 class="text-primary">{{ $customerStats['total_orders'] }}</h5>
                        <small class="text-muted">Đơn hàng</small>
                    </div>
                    <div class="col-6">
                        <h5 class="text-success">{{ number_format($customerStats['total_spent'], 0, ',', '.') }}₫</h5>
                        <small class="text-muted">Tổng chi tiêu</small>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <strong>Email:</strong><br>
                    <span class="text-muted">{{ $customer->email }}</span>
                </div>

                @if($customer->phone)
                    <div class="mb-3">
                        <strong>Số điện thoại:</strong><br>
                        <span class="text-muted">{{ $customer->phone }}</span>
                    </div>
                @endif

                @if($customer->address)
                    <div class="mb-3">
                        <strong>Địa chỉ:</strong><br>
                        <span class="text-muted">{{ $customer->address }}</span>
                    </div>
                @endif

                <div class="mb-3">
                    <strong>Ngày đăng ký:</strong><br>
                    <span class="text-muted">{{ $customer->created_at->format('d/m/Y H:i') }}</span>
                </div>

                <div class="mb-3">
                    <strong>Trạng thái:</strong><br>
                    <form method="POST" action="{{ route('admin.customers.toggle-status', $customer) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $customer->is_active ? 'btn-success' : 'btn-secondary' }}">
                            {{ $customer->is_active ? 'Hoạt động' : 'Bị khóa' }}
                        </button>
                    </form>
                </div>

                @if($customerStats['last_order_date'])
                    <div class="mb-3">
                        <strong>Đơn hàng cuối:</strong><br>
                        <span class="text-muted">{{ $customerStats['last_order_date']->format('d/m/Y H:i') }}</span>
                    </div>
                @endif

                <div class="d-grid gap-2">
                    <a href="{{ route('admin.customers.order-history', $customer) }}" class="btn btn-outline-primary">
                        <i class="fas fa-history me-2"></i>
                        Xem lịch sử mua hàng
                    </a>
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <!-- Customer Stats -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="text-primary">{{ $customerStats['total_orders'] }}</h5>
                        <small class="text-muted">Tổng đơn hàng</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="text-success">{{ number_format($customerStats['total_spent'], 0, ',', '.') }}₫</h5>
                        <small class="text-muted">Tổng chi tiêu</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="text-info">{{ number_format($customerStats['avg_order_value'], 0, ',', '.') }}₫</h5>
                        <small class="text-muted">Giá trị TB/đơn</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="text-warning">{{ $customerStats['orders_by_status']->get('completed', 0) }}</h5>
                        <small class="text-muted">Đơn hoàn thành</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders by Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Đơn hàng theo trạng thái
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($customerStats['orders_by_status'] as $status => $count)
                        <div class="col-md-2 text-center">
                            <h6 class="text-{{ $status == 'completed' ? 'success' : ($status == 'pending' ? 'warning' : ($status == 'canceled' ? 'danger' : 'info')) }}">
                                {{ $count }}
                            </h6>
                            <small class="text-muted">
                                @switch($status)
                                    @case('pending') Chờ xử lý @break
                                    @case('confirmed') Đã xác nhận @break
                                    @case('shipping') Đang giao @break
                                    @case('completed') Hoàn thành @break
                                    @case('canceled') Đã hủy @break
                                    @default {{ $status }}
                                @endswitch
                            </small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-receipt me-2"></i>
                    Đơn hàng gần đây
                </h5>
            </div>
            <div class="card-body">
                @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Trạng thái</th>
                                    <th>Tổng tiền</th>
                                    <th>Ngày tạo</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td>
                                            <strong>#{{ $order->id }}</strong>
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
                                            <span class="fw-bold text-success">
                                                {{ number_format($order->computed_total, 0, ',', '.') }}₫
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $order->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" 
                                               class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('admin.customers.order-history', $customer) }}" class="btn btn-outline-primary">
                            Xem tất cả đơn hàng
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-2x text-muted mb-3"></i>
                        <h6 class="text-muted">Khách hàng chưa có đơn hàng nào</h6>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

