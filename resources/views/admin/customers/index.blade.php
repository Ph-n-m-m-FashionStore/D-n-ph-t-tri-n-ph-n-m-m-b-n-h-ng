@extends('admin.layout')

@section('title', 'Quản lý khách hàng')
@section('page-title', 'Quản lý khách hàng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Danh sách khách hàng</h4>
        <p class="text-muted mb-0">Quản lý thông tin và lịch sử mua hàng của khách hàng</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="text-primary">{{ number_format($stats['total_customers'] ?? 0) }}</h5>
                <small class="text-muted">Tổng khách hàng</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="text-success">{{ number_format($stats['new_customers'] ?? 0) }}</h5>
                <small class="text-muted">Khách hàng mới tháng này</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="text-info">{{ number_format($stats['active_customers'] ?? 0) }}</h5>
                <small class="text-muted">Khách hàng hoạt động</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="text-warning">{{ number_format($stats['total_revenue'], 0, ',', '.') }}₫</h5>
                <small class="text-muted">Tổng doanh thu</small>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Tìm kiếm</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                       placeholder="Tên, email, SĐT...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Từ ngày</label>
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Đến ngày</label>
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
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

<!-- Customers Table -->
<div class="card">
    <div class="card-body">
        @if($customers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Khách hàng</th>
                            <th>Liên hệ</th>
                            <th>Số đơn hàng</th>
                            <th>Tổng chi tiêu</th>
                            <th>Trạng thái</th>
                            <th>Ngày đăng ký</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary text-white rounded-circle me-3" 
                                             style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $customer->name }}</h6>
                                            <small class="text-muted">ID: {{ $customer->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="mb-1">{{ $customer->email }}</div>
                                        @if($customer->phone)
                                            <small class="text-muted">{{ $customer->phone }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $customer->orders_count }} đơn
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">
                                        {{ number_format($customer->total_spent ?? 0, 0, ',', '.') }}₫
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.customers.toggle-status', $customer) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $customer->is_active ? 'btn-success' : 'btn-secondary' }}">
                                            {{ $customer->is_active ? 'Hoạt động' : 'Bị khóa' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $customer->created_at->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.customers.show', $customer) }}" 
                                           class="btn btn-outline-primary btn-sm" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.customers.order-history', $customer) }}" 
                                           class="btn btn-outline-info btn-sm" title="Lịch sử mua hàng">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        @if($customer->orders_count == 0)
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    onclick="confirmDelete({{ $customer->id }})" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <!-- Delete Form -->
                                    <form id="delete-form-{{ $customer->id }}" 
                                          action="{{ route('admin.customers.destroy', $customer) }}" 
                                          method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $customers->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Chưa có khách hàng nào</h5>
                <p class="text-muted">Khách hàng sẽ xuất hiện ở đây khi họ đăng ký tài khoản</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
function confirmDelete(customerId) {
    if (confirm('Bạn có chắc chắn muốn xóa khách hàng này? Hành động này không thể hoàn tác!')) {
        document.getElementById('delete-form-' + customerId).submit();
    }
}
</script>
@endsection
