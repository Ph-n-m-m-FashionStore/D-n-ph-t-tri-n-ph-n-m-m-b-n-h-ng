@extends('admin.layout')

@section('page-title', 'Chi tiết đánh giá')

@section('content')
<div class="card">
    <div class="card-header">Chi tiết đánh giá #{{ $review->id }}</div>
    <div class="card-body">
        <p><strong>Sản phẩm:</strong> {{ $review->product->name ?? '—' }}</p>
        <p><strong>Người dùng:</strong> {{ $review->user->name ?? 'Khách' }} (ID: {{ $review->user_id }})</p>
        <p><strong>Rating:</strong> {{ $review->rating }}</p>
        <p><strong>Nội dung:</strong></p>
        <div class="p-3 border rounded">{{ $review->comment }}</div>
        <div class="mt-3">
            <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('Xác nhận xóa?');">
                @csrf
                @method('DELETE')
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">Quay lại</a>
                <button class="btn btn-danger">Xóa đánh giá</button>
            </form>
        </div>
    </div>
</div>
@endsection
