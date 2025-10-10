@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-2">Tổng doanh thu</h6>
                    <h3 class="mb-0">{{ number_format($stats['total_revenue'], 0, ',', '.') }}₫</h3>
                </div>
                <div class="icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-2">Doanh thu tháng này</h6>
                    <h3 class="mb-0">{{ number_format($stats['monthly_revenue'], 0, ',', '.') }}₫</h3>
                    @if($revenueGrowth > 0)
                        <small class="text-success">
                            <i class="fas fa-arrow-up me-1"></i>
                            +{{ number_format($revenueGrowth, 1) }}%
                        </small>
                    @elseif($revenueGrowth < 0)
                        <small class="text-danger">
                            <i class="fas fa-arrow-down me-1"></i>
                            {{ number_format($revenueGrowth, 1) }}%
                        </small>
                    @endif
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-2">Tổng đơn hàng</h6>
                    <h3 class="mb-0">{{ number_format($stats['total_orders']) }}</h3>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between">
                <div>
                    <h6 class="text-white-50 mb-2">Đơn chờ xử lý</h6>
                    <h3 class="mb-0">{{ number_format($stats['pending_orders']) }}</h3>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Revenue Chart -->
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Doanh thu 7 ngày gần đây
                </h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Thống kê nhanh
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border-end">
                            <h4 class="text-primary">{{ number_format($stats['total_customers'] ?? 0) }}</h4>
                            <small class="text-muted">Khách hàng</small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <h4 class="text-success">{{ number_format($stats['total_products'] ?? 0) }}</h4>
                        <small class="text-muted">Sản phẩm</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-warning">{{ number_format($stats['active_products'] ?? 0) }}</h4>
                        <small class="text-muted">Đang bán</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info">{{ number_format($stats['pending_orders'] ?? 0) }}</h4>
                        <small class="text-muted">Chờ xử lý</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">{{ number_format($stats['new_customers'] ?? 0) }}</h4>
                        <small class="text-muted">Khách hàng mới tháng này</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info">{{ number_format($stats['active_customers'] ?? 0) }}</h4>
                        <small class="text-muted">Khách hàng hoạt động tháng này</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Products -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-star me-2"></i>
                    Sản phẩm bán chạy tháng này
                </h5>
            </div>
            <div class="card-body">
                @if($topProducts->count() > 0)
                    @foreach($topProducts as $index => $product)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <span class="badge bg-primary rounded-circle me-2" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                    {{ $index + 1 }}
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $product->name }}</h6>
                                <small class="text-muted">Đã bán: {{ $product->total_sold }} sản phẩm</small>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-success">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center">Chưa có dữ liệu sản phẩm</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-receipt me-2"></i>
                    Đơn hàng gần đây
                </h5>
            </div>
            <div class="card-body">
                @if($recentOrders->count() > 0)
                    @foreach($recentOrders as $order)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <span class="badge bg-{{ $order->status_badge }} rounded-pill">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Đơn #{{ $order->id }}</h6>
                                <small class="text-muted">
                                    {{ $order->customer_name }} - {{ $order->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold">{{ number_format($order->total, 0, ',', '.') }}₫</span>
                            </div>
                        </div>
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary btn-sm">
                            Xem tất cả đơn hàng
                        </a>
                    </div>
                @else
                    <p class="text-muted text-center">Chưa có đơn hàng nào</p>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Revenue Chart
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [
            @foreach($dailyRevenue as $day)
                '{{ \Carbon\Carbon::parse($day["date"])->format("d/m") }}',
            @endforeach
        ],
        datasets: [{
            label: 'Doanh thu (₫)',
            data: [
                @foreach($dailyRevenue as $day)
                    {{ $day['revenue'] }},
                @endforeach
            ],
            borderColor: 'rgb(102, 126, 234)',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('vi-VN').format(value) + '₫';
                    }
                }
            }
        }
    }
});
</script>
@endsection
