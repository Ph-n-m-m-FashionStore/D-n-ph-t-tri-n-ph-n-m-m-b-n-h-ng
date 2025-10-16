@extends('admin.layout')

@section('page-title', 'Quản lý đánh giá')

@section('content')
<div class="card">
    <div class="card-header">Danh sách đánh giá</div>
    <div class="card-body">
        @if($reviews->count())
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Sản phẩm</th>
                    <th>Người dùng</th>
                    <th>Rating</th>
                    <th>Nội dung</th>
                    <th>Thời gian</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reviews as $review)
                <tr>
                    <td>{{ $review->id }}</td>
                    <td>{{ $review->product->name ?? '—' }}</td>
                    <td>{{ $review->user->name ?? 'Khách' }}</td>
                    <td>{{ $review->rating }}</td>
                    <td style="max-width:320px;word-break:break-word;">{{ Str::limit($review->comment, 120) }}</td>
                    <td>{{ $review->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-sm btn-primary">Xem</a>
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Xác nhận xóa?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $reviews->links() }}
        @else
            <p>Chưa có đánh giá.</p>
        @endif
    </div>
</div>
@endsection
