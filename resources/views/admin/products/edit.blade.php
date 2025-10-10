@extends('admin.layout')

@section('title', 'Chỉnh sửa sản phẩm')
@section('page-title', 'Chỉnh sửa sản phẩm')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Chỉnh sửa sản phẩm: {{ $product->name }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Tên sản phẩm -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Mô tả -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Mô tả sản phẩm <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" required>{{ old('description', $product->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <!-- Giá -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Giá sản phẩm (₫) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" value="{{ old('price', $product->price) }}" min="0" step="1000" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Loại sản phẩm -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="product_type" class="form-label">Loại sản phẩm <span class="text-danger">*</span></label>
                                        <select class="form-select @error('product_type') is-invalid @enderror" 
                                                id="product_type" name="product_type" required>
                                            <option value="">Chọn loại sản phẩm</option>
                                            <option value="clothing" {{ old('product_type', $product->product_type) == 'clothing' ? 'selected' : '' }}>Quần áo</option>
                                            <option value="accessories" {{ old('product_type', $product->product_type) == 'accessories' ? 'selected' : '' }}>Phụ kiện</option>
                                            <option value="shoes" {{ old('product_type', $product->product_type) == 'shoes' ? 'selected' : '' }}>Giày dép</option>
                                        </select>
                                        @error('product_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Trạng thái -->
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Sản phẩm đang bán
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Ảnh hiện tại -->
                            <div class="mb-3">
                                <label class="form-label">Ảnh hiện tại</label>
                                <div class="text-center">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" 
                                         class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            </div>

                            <!-- Upload ảnh mới -->
                            <div class="mb-3">
                                <label for="image" class="form-label">Thay đổi ảnh</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/png,image/jpg,image/jpeg">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Để trống nếu không muốn thay đổi ảnh
                                </div>
                            </div>

                            <!-- Preview ảnh mới -->
                            <div id="image-preview" class="text-center" style="display: none;">
                                <img id="preview-img" src="" alt="Preview" 
                                     class="img-fluid rounded" style="max-height: 200px;">
                                <p class="text-muted mt-2">Ảnh mới</p>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>
                            Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Cập nhật sản phẩm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Preview ảnh khi chọn file
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        document.getElementById('image-preview').style.display = 'none';
    }
});

// Format giá tiền
document.getElementById('price').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    e.target.value = value;
});
</script>
@endsection
