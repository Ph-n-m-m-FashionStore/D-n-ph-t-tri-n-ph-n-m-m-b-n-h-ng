@extends('admin.layout')

@section('title', 'Quản lý sản phẩm')
@section('page-title', 'Quản lý sản phẩm')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Danh sách sản phẩm</h4>
        <p class="text-muted mb-0">Quản lý tất cả sản phẩm trong cửa hàng</p>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>
        Thêm sản phẩm mới
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Tìm kiếm</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Tên sản phẩm...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Trạng thái</label>
                <select class="form-select" name="status">
                    <option value="">Tất cả</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đang bán</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Ngừng bán</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Loại sản phẩm</label>
                <select class="form-select" name="product_type">
                    <option value="">Tất cả</option>
                    <option value="clothing" {{ request('product_type') == 'clothing' ? 'selected' : '' }}>Quần áo</option>
                    <option value="accessories" {{ request('product_type') == 'accessories' ? 'selected' : '' }}>Phụ kiện</option>
                    <option value="shoes" {{ request('product_type') == 'shoes' ? 'selected' : '' }}>Giày dép</option>
                </select>
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

<!-- Products Table -->
<div class="card">
    <div class="card-body">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Loại</th>
                            <th>Giá</th>
                            <th>Trạng thái</th>
                            <th>Đánh giá</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    <img src="{{ asset('images/' . basename($product->image_url)) }}" alt="{{ $product->name }}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-1">{{ $product->name }}</h6>
                                        <small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        @switch($product->product_type)
                                            @case('clothing') Quần áo @break
                                            @case('accessories') Phụ kiện @break
                                            @case('shoes') Giày dép @break
                                            @default {{ $product->product_type }}
                                        @endswitch
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">
                                        {{ number_format($product->price, 0, ',', '.') }}₫
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.products.toggle-status', $product) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $product->is_active ? 'btn-success' : 'btn-secondary' }}">
                                            {{ $product->is_active ? 'Đang bán' : 'Ngừng bán' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-star text-warning me-1"></i>
                                        <span>{{ $product->reviews_count }}</span>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $product->created_at->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.products.edit', $product) }}" 
                                           class="btn btn-outline-primary btn-sm" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                onclick="confirmDelete({{ $product->id }})" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Form -->
                                    <form id="delete-form-{{ $product->id }}" 
                                          action="{{ route('admin.products.destroy', $product) }}" 
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
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Chưa có sản phẩm nào</h5>
                <p class="text-muted">Hãy thêm sản phẩm đầu tiên để bắt đầu bán hàng</p>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Thêm sản phẩm mới
                </a>
            </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script>
function confirmDelete(productId) {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này? Hành động này không thể hoàn tác!')) {
        document.getElementById('delete-form-' + productId).submit();
    }
}
</script>
@endsection
