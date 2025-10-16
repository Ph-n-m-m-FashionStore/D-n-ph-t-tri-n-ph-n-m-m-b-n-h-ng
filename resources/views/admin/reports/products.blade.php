@extends('admin.layout')

@section('title', 'Thống kê sản phẩm')
@section('page-title', 'Thống kê sản phẩm')

@section('content')
<div class="container">
    <h2 class="mb-4">Thống kê sản phẩm theo loại</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Loại sản phẩm</th>
                <th>ID</th>
                <th>Tên</th>
                <th>Giá</th>
                <th>Đã bán</th>
                <th>Doanh thu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productTypeStats as $product)
            <tr>
                <td>
                    @php
                        $typeLabels = [
                            'clothing' => 'Quần áo',
                            'accessories' => 'Phụ kiện',
                            'shoes' => 'Giày'
                        ];
                    @endphp
                    {{ $typeLabels[$product->product_type] ?? $product->product_type }}
                </td>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ number_format($product->price, 0, ',', '.') }}₫</td>
                <td>{{ $product->total_sold ?? 0 }}</td>
                <td>{{ number_format($product->total_revenue ?? 0, 0, ',', '.') }}₫</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
