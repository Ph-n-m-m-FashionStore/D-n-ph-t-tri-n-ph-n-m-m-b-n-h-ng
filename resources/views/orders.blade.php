@extends('layouts.app')

@section('title', 'Đơn hàng của bạn')

@section('content')
<div class="container py-4">
    <div class="bg-white rounded-lg shadow p-6 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Theo dõi đơn hàng</h1>
        
        @if($orders && count($orders))
            <table class="table table-bordered mb-6">
                <thead>
                    <tr class="bg-gray-100">
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ảnh sản phẩm</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td class="p-2">#{{ $order->id }}</td>
                            <td class="p-2 text-center">{{ $order->created_at->format('d/m/Y') }}</td>
                            <td class="p-2 text-center text-orange-500">{{ $order->total ? number_format($order->total, 0, ',', '.') : '---' }}₫</td>
                            <td class="p-2 text-center"><span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded">{{ ucfirst($order->status) }}</span></td>
                            <td class="p-2 text-center">{{ $order->payment_type ?? '---' }}</td>
                            <td class="p-2 text-center">
                                @if($order->items && count($order->items))
                                    @foreach($order->items as $item)
                                        <img src="{{ $item->product->image_url ?? 'https://via.placeholder.com/60x60?text=No+Image' }}" alt="{{ $item->product->name }}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;margin-right:4px;">
                                    @endforeach
                                @else
                                    <span class="text-muted">Không có sản phẩm</span>
                                @endif
                            </td>
                            <td class="p-2 text-center">
                                <a href="/orders/{{ $order->id }}" class="text-blue-500 hover:underline">Xem</a>
                                <div style="margin-top:8px;">
                                    <div style="display:flex;gap:8px;justify-content:center;">
                                        @php
                                            $steps = [
                                                'pending' => 'Chưa thanh toán',
                                                'shipping' => 'Đang vận chuyển',
                                                'delivered' => 'Đã giao hàng',
                                                'returned' => 'Đã trả hàng'
                                            ];
                                            $orderStatus = $order->status;
                                            $orderIndex = array_search($orderStatus, array_keys($steps));
                                        @endphp
                                        @foreach($steps as $key => $label)
                                            @php
                                                $stepIndex = array_search($key, array_keys($steps));
                                                $color = $stepIndex < $orderIndex ? '#22c55e' : ($stepIndex == $orderIndex ? '#0ea5e9' : '#d1d5db');
                                                $textColor = $stepIndex <= $orderIndex ? '#fff' : '#6b7280';
                                            @endphp
                                            <div style="display:flex;flex-direction:column;align-items:center;">
                                                <div style="width:28px;height:28px;border-radius:50%;background:{{ $color }};color:{{ $textColor }};display:flex;align-items:center;justify-content:center;font-weight:bold;">
                                                    {{ $loop->iteration }}
                                                </div>
                                                <div style="font-size:12px;margin-top:2px;">{{ $label }}</div>
                                            </div>
                                            @if(!$loop->last)
                                                <div style="width:32px;height:4px;background:{{ $stepIndex < $orderIndex ? '#22c55e' : '#d1d5db' }};align-self:center;border-radius:2px;"></div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="alert alert-info">Bạn chưa có đơn hàng nào.</div>
        @endif
    </div>
</div>
@endsection