@extends('admin.layout')

@section('title', 'Báo cáo doanh thu')
@section('page-title', 'Báo cáo doanh thu')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Báo cáo doanh thu</h4>
        <p class="text-muted mb-0">Thống kê và phân tích doanh thu bán hàng</p>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="text-success">{{ number_format($stats['total_orders']) }}</h5>
                <small class="text-muted">Tổng đơn hàng</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="text-info">{{ number_format($stats['avg_order_value'], 0, ',', '.') }}₫</h5>
                <small class="text-muted">Giá trị TB/đơn</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="text-warning">{{ number_format($stats['total_customers']) }}</h5>
                <small class="text-muted">Khách hàng</small>
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
                    Biểu đồ doanh thu theo ngày
                </h5>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>

    {{-- Embed chart data as JSON so Blade isn't injected inside JS expressions --}}
    <script id="revenueLabelsData" type="application/json">{!! json_encode(is_iterable($dailyRevenue) ? (\collect($dailyRevenue)->map(function($d){ return \Carbon\Carbon::parse($d['date'])->format('d/m'); })->values()->all()) : []) !!}</script>
    <script id="revenueValuesData" type="application/json">{!! json_encode(is_iterable($dailyRevenue) ? (\collect($dailyRevenue)->map(function($d){ return $d['revenue']; })->values()->all()) : []) !!}</script>

    <!-- Top Products -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-star me-2"></i>
                    Sản phẩm bán chạy
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
                                <span class="badge bg-success">{{ number_format($product->total_revenue, 0, ',', '.') }}₫</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted text-center">Chưa có dữ liệu sản phẩm</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="row">
    <div class="col-12">
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
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Sản phẩm</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
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
                                            <div>
                                                <h6 class="mb-1">{{ $order->customer_name }}</h6>
                                                <small class="text-muted">{{ $order->customer_phone }}</small>
                                            </div>
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
                                                {{ number_format($order->computed_total, 0, ',', '.') }}₫
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
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-2x text-muted mb-3"></i>
                        <h6 class="text-muted">Chưa có đơn hàng nào trong khoảng thời gian này</h6>
                    </div>
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
const revenueLabels = JSON.parse(document.getElementById('revenueLabelsData').textContent || '[]');
const revenueData = JSON.parse(document.getElementById('revenueValuesData').textContent || '[]');

const revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: revenueLabels,
        datasets: [{
            label: 'Doanh thu (₫)',
            data: revenueData,
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

