@extends('layouts.app')

@section('content')
<div class="container">
    <table class="table">
        <thead>
            <tr>
                <th>Mã SP</th>
                <th>Mã SAP</th>
                <th>Tên sản phẩm</th>
                <th>Trạng thái</th>
                <th>Quy cách thùng</th>
                <th>Giá Sellin thùng</th>
                <th>Giá Sellin lẻ</th>
                <th>Giá Sellout thùng</th>
                <th>Giá Sellout lẻ</th>
            </tr>
        </thead>
        <tbody>
            <!-- Lặp qua mỗi sản phẩm và hiển thị thông tin -->
            @foreach ($products as $product)
            <tr>
                <td>{{ $product->product_code }}</td>
                <td>{{ $product->sap_code }}</td>
                <td>{{ $product->product_name }}</td>
                <td>{{ $product->status }}</td>
                <td>{{ $product->packaging }}</td>
                <td>{{ number_format($product->price_sellin_per_pack) }}₫</td>
                <td>{{ number_format($product->price_sellin_per_unit) }}₫</td>
                <td>{{ number_format($product->price_sellout_per_pack) }}₫</td>
                <td>{{ number_format($product->price_sellout_per_unit) }}₫</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection