@extends('layouts.app')

@section('content')
<div class="container mt-3">
    <h3>Danh Sách Đơn Hàng Chưa Tổng Hợp</h3>
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
        <button type="submit" class="btn btn-primary mb-2">Tìm Kiếm</button>
    </form>

    <div class="row">
        <div class="col-md-12">
            <table class="table" id="ordersTable">
                <thead>
                    <tr>
                        <th>STT</th> <!-- Cột cho nút mở rộng -->
                        <th>Ngày Đặt</th>
                        <th>Mã Đơn Hàng</th>
                        <th>NVBH</th>
                        <th>Trạng Thái</th>
                        <th>Chiết Khấu</th>
                        <th>Thành Tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @include('accounting.partials.scheduled_not_summarized_tbody', ['orders' => $orders])
                </tbody>
            </table>
            <div class="d-flex flex-row-reverse align-items-center"> <!-- flex-row-reverse đảo ngược thứ tự hiển thị các phần tử con -->
                <div class="form-inline">
                    <label for="perPage" class="ml-2">Số hàng:</label>
                    <select id="perPage" class="form-control form-control-sm">
                        <option value="10">10</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div id="pagination-links" class="d-flex align-items-center">
                    <!-- Nội dung của pagination-links -->
                </div>
            </div>

        </div>
    </div>
</div>
<!-- Modal thông báo -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Thành công!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Thao tác thành công!
      </div>
    </div>
  </div>
</div>

@include('accounting.partials.add_summary_order_modal')

@endsection
@push('scripts')
<script>

function showAddSummaryOrderModal(order) {
    // Logic để hiển thị modal và điền dữ liệu vào form
    $('#modalStaff').text(order.staff);
    $('#modalOrderId').val(order.id);
    $('#modalOrderCode').text(order.order_code);
    $('#addSummaryOrderModal').modal('show');
}
function submitSummaryOrder() {
    var formData = $('#addSummaryOrderForm').serialize();
    $.ajax({
        url: 'add-summary-order-for-scheduled',
        type: 'POST',
        data: formData,
        success: function(response) {
            console.log(response);
            $('#addSummaryOrderModal').modal('hide');
            notify500();
            // Cập nhật trạng thái của button
            var orderId = $('#modalOrderId').val();
            $('#addButton-' + orderId).hide(); // Ẩn button "Thêm"
            $('#addedStatus-' + orderId).text('Đã thêm').show(); // Hiển thị chữ "Đã thêm"
        },
        error: function(error) {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi thêm Summary Order.');
        }
    });
}

$(document).ready(function() {
    let currentSearchParams = "";
    let currentPerPage = "";
    let perPage = $('#perPage').val();
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

    fetchData('{{ route('orders.scheduled_not_summarized') }}');

    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        //currentSearchParams = $(this).serialize(); // Lưu trữ các tham số tìm kiếm
        currentSearchParams = updateSearchParams('per_page', perPage, $(this).serialize());
        fetchData('{{ route('orders.scheduled_not_summarized') }}?' + currentSearchParams);
    });

    $('#pagination-links').on('click', 'a.relative', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        fetchData(href + '&' + currentSearchParams); // Thêm tham số tìm kiếm vào URL phân trang
    });

    $('#perPage').on('change', function() {
        var perPage = $(this).val();
        currentSearchParams = updateSearchParams('per_page', perPage, currentSearchParams);
        fetchData('{{ route('orders.scheduled_not_summarized') }}?' + currentSearchParams);
    });
    function updateSearchParams(key, value, paramsString) {
        var searchParams = new URLSearchParams(paramsString);
        searchParams.set(key, value);
        return searchParams.toString();
    }

    function notify500(){
        $('#successModal').modal('show');
        setTimeout(function() {
            $('#successModal').modal('hide');
        }, 500);
    }

    // $('#recoveryOrdersTable').on('click', '.expand-button', function() {
    //     var targetId = $(this).data('target');
    //     $(targetId).toggle();
    // });

});
</script>
@endpush