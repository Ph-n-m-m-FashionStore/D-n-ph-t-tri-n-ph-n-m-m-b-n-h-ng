@extends('admin.layout')

@section('title', 'Thống kê khách hàng')
@section('page-title', 'Thống kê khách hàng')

@section('content')
<div class="container">
    <h2 class="mb-4">Thống kê khách hàng</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tổng khách hàng</th>
                <th>Khách hàng mới tháng này</th>
                <th>Khách hàng hoạt động</th>
                <th>Giá trị đơn trung bình</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $customerStats['total_customers'] ?? 0 }}</td>
                <td>{{ $customerStats['new_customers'] ?? 0 }}</td>
                <td>{{ $customerStats['active_customers'] ?? 0 }}</td>
                <td>{{ number_format($customerStats['avg_order_value'] ?? 0, 0, ',', '.') }}₫</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
