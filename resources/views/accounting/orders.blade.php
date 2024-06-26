@extends('layouts.app')

@section('content')
<div class="container mt-3">
    <h4>Tìm Kiếm và Bộ Lọc Đơn Hàng</h4>
    <form id="searchForm" method="GET" class="form-inline">
        <div class="form-group mb-2">
            <input type="text" class="form-control" id="order_code" name="order_code" placeholder="Mã Đơn Hàng">
        </div>
        <div class="form-group mx-sm-3 mb-2">
            <select id="staff" name="staff" class="form-control">
                <option value="">Chọn nhân viên</option>
                @foreach($saleStaffs as $staff)
                    <option value="{{ $staff->name }}">{{ $staff->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mb-2">
            <select class="form-control" id="status" name="status">
                <option value="">Trạng Thái</option>
                <option value="Đơn mới">Đơn mới</option>
                <option value="Đã xuất kho">Đã xuất kho</option>
                <option value="Đang giao hàng">Đang giao hàng</option>
                <option value="Đã hoàn thành">Đã hoàn thành</option>
                <option value="Đã hủy">Đã hủy</option>
            </select>
        </div>
        <div class="form-group mx-sm-3 mb-2">
            <input type="date" class="form-control" id="order_date" name="order_date" placeholder="Ngày Đặt">
        </div>
        <div class="form-group mb-2">
            <select class="form-control" id="order_type" name="order_type">
                <option value="">Chọn loại đơn hàng</option>
                <option value="Đơn bán / Giao ngay">Giao ngay</option>
                <option value="Đơn bán / Giao sau">Giao sau</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mb-2 ml-2">Tìm Kiếm</button>
    </form>


    <div class="row">
        <div class="col-md-12">
            <table class="table" id="ordersTable">
            <thead>
                <tr>
                    <th></th> <!-- Cột cho nút mở rộng -->
                    <th>Mã Đơn Hàng</th>
                    <th>Ngày Đặt</th>
                    <th>Nhân Viên</th>
                    <th>Trạng Thái</th>
                    <th>Chiết Khấu</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @include('accounting.partials.orders_table_body', ['orders' => $orders])
            </tbody>
            </table>
            <div id="pagination-links" class="mt-3">
                
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function fetchData(url) {
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                $('#ordersTable tbody').html(response.table);
                $('#pagination-links').html(response.links);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    // Gọi hàm fetchData khi trang được tải để tải dữ liệu ban đầu
    fetchData('{{ route('orders.index') }}');

    // Xử lý form tìm kiếm
    let currentSearchParams = "";
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        currentSearchParams = $(this).serialize(); // Lưu trữ các tham số tìm kiếm
        fetchData('{{ route('orders.index') }}?' + currentSearchParams);
    });

    // Xử lý sự kiện click trên links phân trang
    $('#pagination-links').on('click', 'a.relative', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        fetchData(href + '&' + currentSearchParams); // Thêm tham số tìm kiếm vào URL phân trang
    });

    // Xử lý nút mở rộng để hiển thị chi tiết đơn hàng
    $('#ordersTable').on('click', '.expand-button', function() {
        var targetId = $(this).data('target');
        $(targetId).toggle();
    });
});

</script>
@endpush
