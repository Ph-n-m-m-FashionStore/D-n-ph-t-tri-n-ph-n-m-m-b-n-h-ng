@extends('layouts.app')
@section('title', 'Chi tiết đơn hàng')
@section('content')
<div class="container py-4">
    <div class="bg-white rounded-lg shadow p-6 max-w-2xl mx-auto">
        @if(!$order)
            <h1 class="text-2xl font-bold mb-4">Bạn chưa có đơn hàng nào</h1>
            <div class="text-center">
                <a href="{{ route('products.index') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
            </div>
        @else
            <h1 class="text-2xl font-bold mb-6">Đơn hàng #{{ $order->id }}</h1>
            <div class="mb-4">
                <span class="font-semibold">Trạng thái:</span>
                <span class="badge bg-info">{{ ucfirst($order->status) }}</span>
            </div>
            <div class="mb-4">
                <span class="font-semibold">Người nhận:</span> {{ $order->name }}<br>
                <span class="font-semibold">SĐT:</span> {{ $order->phone }}<br>
                <span class="font-semibold">Địa chỉ:</span> {{ $order->address }}
            </div>
            <div class="mb-4">
                <span class="font-semibold">Ghi chú:</span> {{ $order->note ?? '---' }}
            </div>
            <h2 class="text-lg font-bold mb-2">Sản phẩm</h2>
            <table class="table table-bordered mb-4">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(($order->orderItems ?? []) as $item)
                    <tr>
                        <td>
                            <img src="{{ $item->display_image }}" class="w-16 h-16 object-cover rounded border" />
                            {{ $item->display_name ?? 'Sản phẩm' }}
                        </td>
                        <td>{{ number_format($item->price ?? 0, 0, ',', '.') }}₫</td>
                        <td>{{ $item->quantity ?? 0 }}</td>
                        <td>{{ number_format(($item->price ?? 0) * ($item->quantity ?? 0), 0, ',', '.') }}₫</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @php
                $itemsTotal = collect($order->orderItems ?? [])->sum(function($it){ return ($it->price ?? 0) * ($it->quantity ?? 0); });
                $shipping = $order->shipping_fee ?? $order->shipping ?? 0;
                $discount = $order->discount ?? 0;
                $tax = $order->tax ?? 0;
                $computedTotal = $itemsTotal + $shipping - $discount + $tax;
            @endphp
            <div class="text-right font-bold text-xl">
                Tổng cộng: <span class="text-orange-500">{{ number_format($computedTotal ?? 0, 0, ',', '.') }}₫</span>
            </div>
            <div class="mt-6 text-center">
                <a href="/orders" class="btn btn-outline-primary">Quay lại</a>
            </div>
        @endif
    </div>
</div>
@endsection
