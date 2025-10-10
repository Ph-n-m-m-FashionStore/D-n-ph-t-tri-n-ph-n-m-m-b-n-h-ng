


@extends('layouts.app')

@section('title', 'Trang chủ')
@section('content')
<style>
    /* Page layout tweaks */
    .page-header {
        padding: 24px 20px;
        background: #fff;
        border-bottom: 1px solid #eee;
    }
    .page-header h1 { font-size: 28px; font-weight: 600; margin-bottom: 6px; }
    .page-header p { color: #666; margin-bottom: 12px; }

    /* Promo banner */
    .promo-banner { background: #f9fafb; padding: 12px 20px; font-size: 14px; margin-bottom: 20px; border-left: 4px solid #28a745; }

    /* Product grid (responsive) */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 22px;
        padding: 0 18px 40px;
    }

    .product-card {
        background-color: #fff;
        transition: transform 0.22s ease, box-shadow 0.22s ease;
        border-radius: 10px;
        box-shadow: 0 6px 18px rgba(21,30,38,0.04);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        min-height: 100%;
    }

    .product-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 14px 30px rgba(21,30,38,0.08);
    }

    .product-image {
        height: 280px;
        background-color: #fafafa;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.35s ease;
    }

    .product-card:hover .product-image img { transform: scale(1.03); }

    /* Tag overlay */
    .product-tag {
        position: absolute;
        top: 12px;
        right: 12px;
        background-color: rgba(0,0,0,0.78);
        color: #fff;
        padding: 6px 10px;
        font-size: 12px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    .product-info {
        padding: 14px 16px 18px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex: 1 1 auto;
    }

    .product-name { font-size: 15px; font-weight: 700; color: #222; }
    .product-price { font-weight: 700; color: #e40000; }

    .product-actions {
        margin-top: auto; /* push button to bottom */
    }

    .product-colors { display:flex; gap:8px; align-items:center; }
    .color-swatch { width:14px; height:14px; border-radius:50%; border:1px solid #eee; }

    /* Responsive breakpoints */
    @media (max-width: 1100px) { .product-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 800px)  { .product-grid { grid-template-columns: repeat(2, 1fr); } .product-image { height: 240px; } }
    @media (max-width: 480px)  { .product-grid { grid-template-columns: 1fr; padding: 0 12px 30px; } .product-image { height: 200px; } }

    /* Utility */
    .search-form .form-label { font-size: 13px; font-weight:600; }
    .search-form .form-control, .search-form .form-select { height: 40px; }
</style>

<div class="page-header container">
    <h1>Sản phẩm mới nhất</h1>
    <p>Khám phá các mẫu áo khoác, jacket, vest mới nhất tại shop!</p>

    <!-- Thanh tìm kiếm và lọc sản phẩm -->
    <form method="GET" action="" class="row g-2 align-items-end search-form" style="margin-top:12px;">
        <div class="col-md-4">
            <label class="form-label">Tìm kiếm sản phẩm</label>
            <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Nhập tên sản phẩm...">
        </div>
        <div class="col-md-2">
            <label class="form-label">Giá từ</label>
            <input type="number" name="min_price" class="form-control" value="{{ request('min_price') }}" placeholder="VNĐ">
        </div>
        <div class="col-md-2">
            <label class="form-label">Giới tính</label>
            <select name="gender" class="form-select">
                <option value="">Tất cả</option>
                <option value="nam" {{ request('gender')=='nam'?'selected':'' }}>Nam</option>
                <option value="nu" {{ request('gender')=='nu'?'selected':'' }}>Nữ</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100"><i class="fas fa-search me-1"></i> Lọc</button>
        </div>
    </form>
</div>

<div class="promo-banner container">
    @if(request('payment')==='failed' && request('order'))
        <div class="alert alert-warning mb-2">Thanh toán chưa được xác nhận cho đơn #{{ request('order') }}. Vui lòng chuyển khoản và thử lại.</div>
    @endif
    <span>Khuyến mãi: Nhập mã <b>SALE50</b> giảm 50% cho đơn hàng đầu tiên!</span>
</div>

<div class="container">
    <div class="product-grid">
        @foreach($products as $product)
            <article class="product-card" aria-labelledby="product-{{ $product->id }}">
                <div class="product-image">
                    <img src="{{ url('/images/' . ltrim(basename($product->image_url))) }}" alt="{{ $product->name }}">
                    <span class="product-tag">NEW</span>
                </div>
                <div class="product-info">
                    <div id="product-{{ $product->id }}" class="product-name">{{ $product->name }}</div>
                    <div class="product-price">{{ number_format($product->price, 0, ',', '.') }}₫</div>
                    <div class="product-colors" aria-hidden="true">
                        <div class="color-swatch" style="background-color: #000;"></div>
                        <div class="color-swatch" style="background-color: #2b3d82;"></div>
                        <div class="color-swatch" style="background-color: #8d6949;"></div>
                    </div>

                    <div class="product-actions">
                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary w-100 mt-2">Xem chi tiết</a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-4 d-flex justify-content-center">{{ $products->links('pagination::simple-bootstrap-4') }}</div>
</div>

@endsection
