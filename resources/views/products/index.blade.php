@extends('layouts.app')
@section('title', 'Danh sách sản phẩm')
@section('content')
<div class="container">
    <h2 class="mb-4">Danh sách sản phẩm</h2>
    <div class="row">
        @forelse ($products as $product)
            <div class="col-6 col-sm-4 col-md-3 mb-4">
                <div class="card h-100">
                    <img src="{{ asset('images/' . basename($product->image_url)) }}" class="card-img-top product-img" alt="{{ $product->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">{{ $product->description }}</p>
                        <ul class="list-unstyled">
                            <li><strong>Thương hiệu:</strong> {{ $product->brand }}</li>
                            <li><strong>Size:</strong> {{ $product->size }}</li>
                            <li><strong>Giá:</strong> {{ number_format($product->price) }}₫</li>
                            <li><strong>Tồn kho:</strong> {{ $product->stock }}</li>
                        </ul>
                        <form method="POST" action="{{ route('cart.add') }}" class="mt-2">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-success btn-sm w-100">Thêm vào giỏ hàng</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p>Không có sản phẩm nào phù hợp.</p>
            </div>
        @endforelse
    </div>
    <div class="d-flex justify-content-center">
           {{ $products->links('vendor.pagination.simple-default') }}
    </div>
    <style>
        .product-img { width: 100%; aspect-ratio: 1 / 1; object-fit: cover; }
    </style>
</div>
@endsection
