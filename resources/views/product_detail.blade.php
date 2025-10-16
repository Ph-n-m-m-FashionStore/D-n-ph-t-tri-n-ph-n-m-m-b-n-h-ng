@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container mx-auto py-8">
    <div class="bg-white rounded-lg shadow p-6 flex flex-col md:flex-row gap-8">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center justify-content-center">
                <img src="{{ asset('images/' . basename($product->image_url)) }}" style="width:340px;height:340px;object-fit:cover;border-radius:16px;box-shadow:0 2px 16px rgba(0,0,0,0.12);transition:box-shadow 0.2s;" alt="{{ $product->name }}">
            </div>
            <div class="col-md-6">
                <h2>{{ $product->name }}</h2>
                <p class="text-muted">{{ $product->brand }}</p>
                <h4 class="text-danger">{{ number_format($product->price, 0, ',', '.') }}₫</h4>
                <p>{{ $product->description }}</p>
                <form method="POST" action="/cart/add" class="mb-3 d-flex flex-column gap-2">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="mb-2">Thương hiệu: <span class="font-semibold">{{ $product->brand }}</span></div>
                    <div class="mb-2">Size: <span class="font-semibold">{{ $product->size ?? 'M, L, XL' }}</span></div>
                    <div class="mb-2">Mô tả: {{ $product->description }}</div>
                    <div class="mb-4">Số lượng: <input type="number" name="quantity" min="1" value="1" class="border rounded px-2 py-1 w-16" /></div>
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-success btn-lg" style="font-size:18px;min-width:160px;">Thêm vào giỏ hàng</button>
                        <button type="button" class="btn btn-warning btn-lg" style="font-size:18px;min-width:120px;" onclick="document.getElementById('buyNowForm').submit();">Mua ngay</button>
                    </div>
                </form>
                <form id="buyNowForm" method="POST" action="/cart/add">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="buy_now" value="1">
                </form>
                <div class="bg-orange-100 border-l-4 border-orange-500 p-2 mb-4">
                    <span class="font-bold text-orange-700">Khuyến mãi:</span> Nhập mã <span class="font-bold">SALE50</span> giảm 50% cho đơn hàng đầu tiên!
                </div>
                @php
                    $reviewsQuery = $product->reviews();
                    $reviewsCount = $reviewsQuery->count();
                    $avgRating = $reviewsCount ? round($reviewsQuery->avg('rating'), 1) : 0;
                @endphp
                <div class="flex items-center mb-2">
                    <span class="font-bold mr-2">{{ number_format($avgRating, 1) }}</span>
                    <span class="text-warning">
                        @for($i = 1; $i <= 5; $i++)
                            @php
                                $full = $i <= floor($avgRating);
                                $half = (!$full && ($i - $avgRating) < 1 && ($avgRating - floor($avgRating)) >= 0.5);
                            @endphp
                            @if($full)
                                <i class="fa fa-star"></i>
                            @elseif($half)
                                <i class="fa fa-star-half-alt"></i>
                            @else
                                <i class="fa fa-star-o"></i>
                            @endif
                        @endfor
                    </span>
                    <span class="ml-2 text-gray-600">({{ $reviewsCount }} đánh giá)</span>
                </div>
            </div>
        </div>
    </div>
    <!-- Bình luận, đánh giá -->
    <div class="bg-white rounded-lg shadow p-6 mt-8" id="reviewBox" style="transition:transform 0.3s;">
        <h2 class="text-lg font-semibold mb-4">Đánh giá sản phẩm</h2>
        @php
            $canReview = false;
            if (Auth::check()) {
                $canReview = \App\Models\Order::where('user_id', Auth::id())
                    ->where('status', 'completed')
                    ->whereHas('orderItems', function($q) use ($product) {
                        $q->where('product_id', $product->id);
                    })->exists();
            }
        @endphp

        @auth
            @if($canReview)
                <form method="POST" action="{{ route('reviews.store', $product->id) }}">
                    @csrf
                    <textarea name="comment" class="w-full border rounded p-2 @error('comment') is-invalid @enderror" rows="3" placeholder="Viết đánh giá của bạn..." id="reviewTextarea" required>{{ old('comment') }}</textarea>
                    @error('comment')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <label>Chấm sao:</label>
                        <select name="rating" class="form-select w-auto">
                            @for($i=5;$i>=1;$i--)<option value="{{ $i }}">{{ $i }}</option>@endfor
                        </select>
                    </div>
                    <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded mt-2">Gửi đánh giá</button>
                </form>
            @else
                <p class="text-muted">Bạn chỉ có thể đánh giá sản phẩm này sau khi đơn hàng chứa sản phẩm được xác nhận hoàn tất.</p>
            @endif
        @else
            <p>Vui lòng <a href="{{ route('login') }}" class="text-blue-500">đăng nhập</a> để đánh giá.</p>
        @endauth
        <script>
        document.getElementById('reviewTextarea')?.addEventListener('focus', function() {
            document.getElementById('reviewBox').style.transform = 'translateY(-40px)';
        });
        document.getElementById('reviewTextarea')?.addEventListener('blur', function() {
            document.getElementById('reviewBox').style.transform = 'translateY(0)';
        });
        </script>
        <div class="border-top pt-4">
            @php $reviews = \App\Models\Review::with('user')->where('product_id', $product->id)->latest()->get(); @endphp
            @forelse($reviews as $review)
                <div class="mb-2">
                    <span class="fw-bold">{{ $review->user->name ?? 'Khách' }}</span>
                    <span class="text-warning">
                        @for($i=1;$i<=5;$i++) {!! $i <= $review->rating ? '★' : '☆' !!} @endfor
                    </span>
                    <div>{{ $review->comment }}</div>
                </div>
            @empty
                <p>Chưa có đánh giá nào.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
