@extends('layouts.app')

@section('content')
<div class="container mt-3">
    <h4>Tìm Kiếm và Bộ Lọc Đơn Hàng</h4>
    <form id="searchForm" method="GET" class="form-inline">
        <div class="form-group mb-2">
            <input type="text" class="form-control" id="order_code" name="order_code" placeholder="Mã Đơn Hàng">
        </div>
        <div class="form-group mx-sm-3 mb-2">
            <input type="text" class="form-control" id="staff" name="staff" placeholder="Tên Nhân Viên">
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
        <button type="submit" class="btn btn-primary mb-2">Tìm Kiếm</button>
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
                {{-- Links phân trang sẽ được tải và hiển thị tại đây thông qua AJAX --}}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Hàm tải dữ liệu
    function fetchData(url) {
        console.log('AJAX request to:', url); // Debug URL
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                console.log('Success:', response.table); // Debug Response
                console.log('Link:', response.links);
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
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        fetchData('{{ route('orders.index') }}?' + $(this).serialize());
    });

    // Xử lý sự kiện click trên links phân trang
    $('#pagination-links').on('click', 'a.page-link', function(e) {
        e.preventDefault();
        var pageUrl = $(this).attr('href');
        console.log('Fetching:', pageUrl); // Debug URL
        fetchData(pageUrl);
    });

    // Xử lý nút mở rộng để hiển thị chi tiết đơn hàng
    $('#ordersTable').on('click', '.expand-button', function() {
        var targetId = $(this).data('target');
        $(targetId).toggle();
    });
});

</script>
@endpush
