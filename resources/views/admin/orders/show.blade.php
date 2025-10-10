@extends('admin.layout')

@section('title', 'Chi tiết đơn hàng #' . $order->id)
@section('page-title', 'Chi tiết đơn hàng #' . $order->id)

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Order Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Thông tin đơn hàng
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Thông tin khách hàng</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Tên:</strong></td>
                                <td>{{ $order->customer_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>SĐT:</strong></td>
                                <td>{{ $order->customer_phone }}</td>
                            </tr>
                            <tr>
                                <td><strong>Địa chỉ:</strong></td>
                                <td>{{ $order->address }}</td>
                            </tr>
                            @if($order->user)
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $order->user->email }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Thông tin đơn hàng</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Mã đơn:</strong></td>
                                <td>#{{ $order->id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Trạng thái:</strong></td>
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
                            </tr>
                            <tr>
                                <td><strong>Ngày tạo:</strong></td>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Phương thức TT:</strong></td>
                                <td>{{ $order->payment_type ?? 'COD' }}</td>
                            </tr>
                            @if($order->note)
                                <tr>
                                    <td><strong>Ghi chú:</strong></td>
                                    <td>{{ $order->note }}</td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-box me-2"></i>
                    Sản phẩm trong đơn hàng
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Hình ảnh</th>
                                <th>Tên sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" 
                                             class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td>
                                        <h6 class="mb-1">{{ $item->product->name }}</h6>
                                        <small class="text-muted">{{ $item->product->product_type }}</small>
                                    </td>
                                    <td>{{ number_format($item->price, 0, ',', '.') }}₫</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="fw-bold">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-light">
                                <td colspan="4" class="text-end"><strong>Tổng cộng:</strong></td>
                                <td class="fw-bold text-success">{{ number_format($order->total, 0, ',', '.') }}₫</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Payment Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-credit-card me-2"></i>
                    Trạng thái thanh toán
                </h5>
            </div>
            <div class="card-body">
                @if($order->payment)
                    <div class="text-center">
                        @if($order->payment->status == 'paid')
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <h5 class="text-success">Đã thanh toán</h5>
                            <p class="text-muted">Phương thức: {{ $order->payment->method }}</p>
                            <small class="text-muted">
                                {{ $order->payment->updated_at->format('d/m/Y H:i') }}
                            </small>
                        @else
                            <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                            <h5 class="text-warning">Chờ thanh toán</h5>
                            <p class="text-muted">Phương thức: {{ $order->payment->method }}</p>
                        @endif
                    </div>
                @else
                    <div class="text-center">
                        <i class="fas fa-exclamation-circle fa-3x text-secondary mb-3"></i>
                        <h5 class="text-secondary">Chưa thanh toán</h5>
                        <p class="text-muted">Phương thức: {{ $order->payment_type ?? 'COD' }}</p>
                    </div>
                @endif

                @if(!$order->payment || $order->payment->status != 'paid')
                    <div class="d-grid mt-3">
                        <form method="POST" action="{{ route('admin.orders.confirm-payment', $order) }}">
                            @csrf
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-2"></i>
                                Xác nhận thanh toán
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    Thao tác đơn hàng
                </h5>
            </div>
            <div class="card-body">
                @if($order->status === 'pending')
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="mb-2">
                        @csrf
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check-circle me-2"></i>
                            Xác nhận đơn hàng
                        </button>
                    </form>
                @endif

                @if($order->status === 'confirmed')
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="mb-2">
                        @csrf
                        <input type="hidden" name="status" value="shipping">
                        <button type="submit" class="btn btn-info w-100">
                            <i class="fas fa-truck me-2"></i>
                            Bắt đầu giao hàng
                        </button>
                    </form>
                @endif

                @if($order->status === 'shipping')
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="mb-2">
                        @csrf
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check-double me-2"></i>
                            Hoàn thành đơn hàng
                        </button>
                    </form>
                @endif

                @if(!in_array($order->status, ['completed', 'canceled']))
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" 
                          onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                        @csrf
                        <input type="hidden" name="status" value="canceled">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times me-2"></i>
                            Hủy đơn hàng
                        </button>
                    </form>
                @endif

                <hr>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-arrow-left me-2"></i>
                    Quay lại danh sách
                </a>
            </div>
        </div>

        <!-- Order Timeline -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    Lịch sử đơn hàng
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6>Đơn hàng được tạo</h6>
                            <small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    
                    @if($order->status !== 'pending')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6>Đơn hàng được xác nhận</h6>
                                <small class="text-muted">{{ $order->updated_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    @endif
                    
                    @if(in_array($order->status, ['shipping', 'completed']))
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6>Đang giao hàng</h6>
                                <small class="text-muted">Đang xử lý</small>
                            </div>
                        </div>
                    @endif
                    
                    @if($order->status === 'completed')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h6>Đơn hàng hoàn thành</h6>
                                <small class="text-muted">Giao hàng thành công</small>
                            </div>
                        </div>
                    @endif
                    
                    @if($order->status === 'canceled')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h6>Đơn hàng bị hủy</h6>
                                <small class="text-muted">Đơn hàng đã bị hủy</small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-size: 14px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: -30px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}
</style>
@endsection
