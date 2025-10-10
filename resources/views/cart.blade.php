@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<div class="container py-4">
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-6">Giỏ hàng của bạn</h1>
        @if(!empty($cartItems) && count($cartItems))
            <table class="table table-bordered mb-6">
                <thead>
                    <tr class="bg-gray-100">
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $item)
                        <tr>
                            <td>
                                <img src="{{ asset('images/' . basename($item->product->image_url)) }}" class="w-16 h-16 object-cover rounded border" />
                                {{ $item->product->name }}
                            </td>
                            <td>{{ number_format($item->product->price, 0, ',', '.') }}₫</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}₫</td>
                            <td>
                                <form method="POST" action="/cart/remove/{{ $item->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-end align-items-center mb-4" style="gap: 1rem;">
                <span class="text-xl font-bold">Tổng tiền: <span class="text-orange-500">{{ number_format($cartItems->sum(function($item){return $item->product->price * $item->quantity; }), 0, ',', '.') }}đ</span></span>
                <a href="{{ route('cart.checkout.index') }}" class="btn btn-success btn-lg" style="font-weight:bold;">Thanh toán</a>
            </div>
        @else
            <div class="alert alert-info">Giỏ hàng của bạn đang trống.</div>
        @endif
    </div>
</div>
@endsection
