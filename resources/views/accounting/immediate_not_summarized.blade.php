@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
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
    
    <div class="d-flex align-items-center">
        <button id="showSummaryBtn" class="btn btn-warning mr-2">Tổng hợp</button>
        <div>Đã chọn: <span class="badge badge-primary" id="selectedCount">0</span> hàng</div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table" id="ordersTable">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="checkAll">
                        </th>
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
                    @include('accounting.partials.immediate_not_summarized_tbody', ['orders' => $orders])
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

<!-- Modal -->
<div class="modal fade" id="summaryModal" tabindex="-1" role="dialog" aria-labelledby="summaryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="summaryModalLabel">Tóm Tắt Đơn Hàng</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="summaryModalBody">
                <!-- Thêm form để nhập thông tin -->
                <form id="summaryForm">
                    <div class="form-group">
                        <label for="invoice_code">Mã Hóa Đơn:</label>
                        <input type="text" class="form-control" id="invoice_code" name="invoice_code" required>
                    </div>
                    <div class="form-group">
                        <label for="report_date">Ngày Báo Cáo:</label>
                        <input type="date" class="form-control" id="report_date" name="report_date" required>
                    </div>
                </form>
                <!-- Đây là nơi hiển thị thông tin tóm tắt các đơn hàng đã chọn -->
                <div id="selectedOrdersSummary"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="addSummaryBtn">Tổng hợp</button>
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

@endsection
@push('scripts')
<script>
    //Nếu tổng hợp đơn bị sót thì cập nhật thêm vào GroupOrder order_id đúng group_id đó
    $('#showSummaryBtn').click(function() {
        let selectedRows = $('.order-checkbox:checked').closest('tr');
        let totalDiscount = 0;
        let totalAmount = 0;

        // Bắt đầu bảng và thêm tiêu đề cột
        let summaryContent = `
            <table class="table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Mã Đơn Hàng</th>
                        <th>NVBH</th>
                        <th>Chiết Khấu</th>
                        <th>Thành Tiền</th>
                    </tr>
                </thead>
                <tbody>
        `;

        if (selectedRows.length > 0) {
            selectedRows.each(function(index) {
                let order = JSON.parse($(this).attr('data-order'));
                totalDiscount += order.discount; // order.discount là số nguyên
                totalAmount += order.total_amount; // order.total_amount là số nguyên

                // Thêm hàng cho mỗi đơn hàng được chọn
                summaryContent += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${order.order_code}</td>
                        <td>${order.staff}</td>
                        <td>${order.discount}</td> <!-- Không cần parseFloat và .toFixed -->
                        <td>${order.total_amount}</td> <!-- Không cần parseFloat và .toFixed -->
                    </tr>
                `;
            });

            // Thêm hàng tổng cộng vào cuối bảng
            summaryContent += `
                <tr>
                    <td colspan="3"><strong>Tổng</strong></td>
                    <td><strong>${totalDiscount}</strong></td>
                    <td><strong>${totalAmount}</strong></td>
                </tr>
            `;

            // Kết thúc tbody và table
            summaryContent += `</tbody></table>`;

            // Đặt nội dung vào modal và hiển thị modal
            $('#selectedOrdersSummary').html(summaryContent);
            $('#summaryModal').modal('show');
        } else {
            alert('Vui lòng chọn ít nhất một đơn hàng.');
        }
    });

    $('#addSummaryBtn').click(function() {
        let selectedRows = $('.order-checkbox:checked').closest('tr');
        let staffNames = [];
        let ordersData = [];
        // Lấy dữ liệu từ form
        let invoiceCode = $('#invoice_code').val();
        let reportDate = $('#report_date').val();

        if (selectedRows.length === 0) {
            alert('Vui lòng chọn ít nhất một đơn hàng.');
            return;
        }

        // Lặp qua các hàng được chọn để kiểm tra tên nhân viên và thu thập dữ liệu
        selectedRows.each(function() {
            let order = JSON.parse($(this).attr('data-order'));
            staffNames.push(order.staff);
            ordersData.push({
                order_id: order.id,
                order_code: order.order_code,
                staff: order.staff,
                discount: order.discount,
                total_amount: order.total_amount
            });
        });

        // Kiểm tra xem tất cả các tên nhân viên có giống nhau không
        let isAllStaffSame = staffNames.every(staff => staff === staffNames[0]);

        if (!isAllStaffSame) {
            alert('Không trùng tên nhân viên.');
            return;
        }

        // Gửi dữ liệu qua AJAX để tạo các bản ghi mới trong group_orders và summary_orders
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        //console.log(invoiceCode);
        //console.log(reportDate);
        //console.log(ordersData);
        $.ajax({
            url: 'add-summary-order-for-immediate', 
            type: 'POST',
            data: {
                invoice_code: invoiceCode,
                report_date: reportDate,
                orders: ordersData
            },
            success: function(response) {
                // Xử lý phản hồi từ server
                notify500();
                $('#summaryModal').modal('hide'); // Đóng modal
                // Thay thế checkbox bằng icon tick màu xanh cho các hàng được chọn
                $('.order-checkbox:checked').each(function() {
                    $(this).closest('.checkbox-container').html('<i class="fas fa-check text-success"></i>');
                });
                $('#checkAll').prop('checked', false);//bỏ checkall
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi thêm vào Summary Orders.');
            }
        });
    });

    function notify500(){
        $('#successModal').modal('show');
        setTimeout(function() {
            $('#successModal').modal('hide');
        }, 500);
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

    fetchData('{{ route('orders.immediate_not_summarized') }}');

    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        //currentSearchParams = $(this).serialize(); // Lưu trữ các tham số tìm kiếm
        currentSearchParams = updateSearchParams('per_page', perPage, $(this).serialize());
        fetchData('{{ route('orders.immediate_not_summarized') }}?' + currentSearchParams);
    });

    $('#pagination-links').on('click', 'a.relative', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        fetchData(href + '&' + currentSearchParams); // Thêm tham số tìm kiếm vào URL phân trang
    });

    $('#checkAll').on('click', function() {
        var isChecked = $(this).prop('checked');
        $('.checkItem').prop('checked', isChecked);
        updateCount(); 
    });

    $('#perPage').on('change', function() {
        var perPage = $(this).val();
        currentSearchParams = updateSearchParams('per_page', perPage, currentSearchParams);
        fetchData('{{ route('orders.immediate_not_summarized') }}?' + currentSearchParams);
    });
    function updateSearchParams(key, value, paramsString) {
        var searchParams = new URLSearchParams(paramsString);
        searchParams.set(key, value);
        return searchParams.toString();
    }

    function updateCount() {
        var count = $('.checkItem:checked').length;
        $('#selectedCount').text(count);
    }
    $(document).on('click', '.checkItem', function() {
        updateCount();
    });
    updateCount();  

    // $('#recoveryOrdersTable').on('click', '.expand-button', function() {
    //     var targetId = $(this).data('target');
    //     $(targetId).toggle();
    // });
});
</script>
@endpush