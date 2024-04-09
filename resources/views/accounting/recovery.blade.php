@extends('layouts.app')

@section('content')
<div class="container mt-3">
    <h4>Tìm Kiếm và Bộ Lọc Đơn Hàng Thu Hồi</h4>
    <form id="searchForm" method="GET" class="form-inline">
        <div class="form-group mb-2">
            <input type="text" class="form-control" id="recovery_code" name="recovery_code" placeholder="Mã Phiếu">
        </div>
        <div class="form-group mx-sm-3 mb-2">
            <select id="staff" name="staff" class="form-control">
                <option value="">Chọn nhân viên</option>
                @foreach($saleStaffs as $staff)
                    <option value="{{ $staff->name }}">{{ $staff->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group mx-sm-3 mb-2">
            <input type="date" class="form-control" id="recovery_creation_date" name="recovery_creation_date" placeholder="Ngày Tạo Phiếu">
        </div>
        <button type="submit" class="btn btn-primary mb-2">Tìm Kiếm</button>
    </form>

    <div class="row">
        <div class="col-md-12">
            <table class="table" id="recoveryOrdersTable">
                <thead>
                    <tr>
                        <th></th> <!-- Cột cho nút mở rộng -->
                        <th>Mã Phiếu</th>
                        <th>Nhân Viên</th>
                        <th>Trạng Thái</th>
                        <th>Ngày Duyệt</th>
                        <th>Ngày Tạo</th>
                        <th>Ngày Thu</th>
                    </tr>
                </thead>
                <tbody>
                    @include('accounting.partials.recovery_orders_tbody', ['recoveryOrders' => $recoveryOrders])
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
                $('#recoveryOrdersTable tbody').html(response.table);
                $('#pagination-links').html(response.links);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    fetchData('{{ route('orders.recovery') }}');

    let currentSearchParams = "";
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        currentSearchParams = $(this).serialize(); // Lưu trữ các tham số tìm kiếm
        fetchData('{{ route('orders.recovery') }}?' + currentSearchParams);
    });

    $('#pagination-links').on('click', 'a.relative', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        fetchData(href + '&' + currentSearchParams); // Thêm tham số tìm kiếm vào URL phân trang
    });

    $('#recoveryOrdersTable').on('click', '.expand-button', function() {
        var targetId = $(this).data('target');
        $(targetId).toggle();
    });
});
</script>
@endpush
