@extends('layouts.app')
@section('title', 'Theo dõi trạng thái đơn hàng')
@section('content')
<div class="container py-4">
    <div class="bg-white rounded-lg shadow p-6 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Trạng thái đơn hàng của bạn</h1>
        @if($orders && count($orders))
            <table class="table table-bordered mb-6">
                <thead>
                    <tr class="bg-gray-100">
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        @if(auth()->user() && auth()->user()->is_admin)
                        <th>Cập nhật</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td class="p-2">#{{ $order->id }}</td>
                            <td class="p-2 text-center">{{ $order->created_at->format('d/m/Y') }}</td>
                            <td class="p-2 text-center text-orange-500">{{ number_format($order->total, 0, ',', '.') }}₫</td>
                            <td class="p-2 text-center"><span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded">{{ ucfirst($order->status) }}</span></td>
                            @if(auth()->user() && auth()->user()->is_admin)
                            <td class="p-2 text-center">
                                <form method="POST" action="{{ route('order-status.update', $order->id) }}">
                                    @csrf
                                    @method('POST')
                                    <select name="status" class="form-select">
                                        <option value="pending" @if($order->status=='pending') selected @endif>Chờ xác nhận</option>
                                        <option value="confirmed" @if($order->status=='confirmed') selected @endif>Đã xác nhận</option>
                                        <option value="shipping" @if($order->status=='shipping') selected @endif>Đang giao</option>
                                        <option value="completed" @if($order->status=='completed') selected @endif>Đã giao</option>
                                        <option value="canceled" @if($order->status=='canceled') selected @endif>Đã hủy</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm mt-2">Cập nhật</button>
                                </form>
                            </td>
                            @endif
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
