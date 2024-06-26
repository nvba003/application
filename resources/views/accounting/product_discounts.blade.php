@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Chỉnh Sửa Chiết Khấu Sản Phẩm</h1>
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form id="discountForm">
        @csrf
        <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
        <table class="table">
            <thead>
                <tr>
                    <th>Mã SP</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Giá bán</th>
                    <th>cKhấu (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productDiscounts as $product)
                <tr>
                    <td>{{ $product->sap_code }}</td>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->price }}</td>
                    <td>
                        <input type="number" step="0.01" name="discounts[{{ $product->sap_code }}]" value="{{ $product->discount_percentage ?? 0 }}" class="form-control">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#discountForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '{{ route("updateProductDiscount") }}',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                alert('Cập nhật thành công');
                location.reload(); // Reload page để cập nhật dữ liệu mới
            },
            error: function(error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra');
            }
        });
    });
});
</script>
@endpush
