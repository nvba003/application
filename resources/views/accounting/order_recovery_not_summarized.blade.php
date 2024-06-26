@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="container mt-3">
    <h3>Danh Sách Thu Hồi Chưa Tổng Hợp</h3>
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
        <div class="form-group mb-2">
            <select class="form-control" id="status" name="status">
                <option value="">Trạng Thái</option>
                <option value="Chờ duyệt">Chờ duyệt</option>
                <option value="Đã duyệt">Đã duyệt</option>
            </select>
        </div>
        <div class="form-group mx-sm-3 mb-2">
            <input type="date" class="form-control" id="recovery_date" name="recovery_date" placeholder="Ngày thu hồi">
        </div>
        <button type="submit" class="btn btn-primary mb-2">Tìm Kiếm</button>
    </form>
    <button id="showSummaryBtn" class="btn btn-warning">Tổng hợp</button>

    <div class="row">
        <div class="col-md-12">
            <table class="table" id="ordersTable">
                <thead>
                    <tr>
                        <th>
                            <!-- <input type="checkbox" id="checkAll"> -->
                        </th>
                        <th></th>
                        <th>STT</th> <!-- Cột cho nút mở rộng -->
                        <th>Ngày Thu</th>
                        <th>Mã Phiếu</th>
                        <th>NVBH</th>
                        <th>Khách hàng</th>
                        <th>Chiết Khấu</th>
                        <th>Thành Tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @include('accounting.partials.order_recovery_not_summarized_tbody', ['recoveryOrders' => $recoveryOrders])
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
                        <input type="text" class="form-control" id="invoice_code" name="invoice_code">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="report_date">Ngày Báo Cáo:</label>
                                <input type="date" class="form-control" id="report_date" name="report_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="recovery_type">Loại đơn thu hồi:</label>
                                <select class="form-control" id="recovery_type" name="recovery_type" required>
                                    <option value="">Chọn</option>
                                    <option value="1">Giao ngay</option>
                                    <option value="0">Giao sau</option>
                                </select>
                            </div>
                        </div>
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
                        <th>Mã Phiếu</th>
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
                console.log(order);
                totalDiscount += order.total_discount;
                totalAmount += order.total_discounted_amount;

                // Thêm hàng cho mỗi đơn hàng được chọn
                summaryContent += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${order.recovery_code}</td>
                        <td>${order.staffs[0].staff}</td>
                        <td>${order.total_discount}</td> <!-- Không cần parseFloat và .toFixed -->
                        <td>${order.total_discounted_amount}</td> <!-- Không cần parseFloat và .toFixed -->
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
        let recoveryType = $('#recovery_type').val();

        if (selectedRows.length === 0) {
            alert('Vui lòng chọn ít nhất một đơn hàng.');
            return;
        }

        // Lặp qua các hàng được chọn để kiểm tra tên nhân viên và thu thập dữ liệu
        selectedRows.each(function() {
            let order = JSON.parse($(this).attr('data-order'));
            staffNames.push(order.staffs[0].staff);
            console.log(order.orderDetail);
            ordersData.push({
                order_id: order.id,
                order_code: order.recovery_code,
                staff: order.staffs[0].staff,
                discount: order.total_discount,
                total_amount: order.total_discounted_amount,
                order_detail: order.orderDetail
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
        // console.log(invoiceCode);
        // console.log(reportDate);
        // console.log(ordersData);
        $.ajax({
            url: 'add-summary-order-for-order-recovery', 
            type: 'POST',
            data: {
                invoice_code: invoiceCode,
                report_date: reportDate,
                orders: ordersData,
                recovery_type: recoveryType
            },
            success: function(response) {
                // Xử lý phản hồi từ server
                notify500();
                $('#summaryModal').modal('hide'); // Đóng modal
                // Thay thế checkbox bằng icon tick màu xanh cho các hàng được chọn
                $('.order-checkbox:checked').each(function() {
                    $(this).closest('.checkbox-container').html('<i class="fas fa-check text-success"></i>');
                });
                //$('#checkAll').prop('checked', false);//bỏ checkall
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
    $(document).on('click', '.checkItem', function() {
        $('.checkItem').not(this).prop('checked', false);//chỉ chọn 1 item/lần
    });
    let currentSearchParams = "";
    let currentPerPage = "";
    let perPage = $('#perPage').val();
    var recoveryOrders = @json($recoveryOrders)['data'];
    console.log(recoveryOrders);
    function fetchData(url) {
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                //console.log(response.recoveryOrders['data']);
                $('#ordersTable tbody').html(response.table);
                $('#pagination-links').html(response.links);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    fetchData('{{ route('order_recovery_not_summarized') }}');

    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        perPage = $('#perPage').val();
        // currentSearchParams = $(this).serialize(); // Lưu trữ các tham số tìm kiếm
        currentSearchParams = updateSearchParams('per_page', perPage, $(this).serialize());
        fetchData('{{ route('order_recovery_not_summarized') }}?' + currentSearchParams);
    });

    $('#pagination-links').on('click', 'a.relative', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        fetchData(href + '&' + currentSearchParams); // Thêm tham số tìm kiếm vào URL phân trang
    });

    // $('#checkAll').on('click', function() {
    //     var isChecked = $(this).prop('checked');
    //     $('.checkItem').prop('checked', isChecked);
    // });
    

    $('#perPage').on('change', function() {
        var perPage = $(this).val();
        currentSearchParams = updateSearchParams('per_page', perPage, currentSearchParams);
        fetchData('{{ route('order_recovery_not_summarized') }}?' + currentSearchParams);
    });
    function updateSearchParams(key, value, paramsString) {
        var searchParams = new URLSearchParams(paramsString);
        searchParams.set(key, value);
        return searchParams.toString();
    }

    $('#ordersTable').on('click', '.expand-button', function() {
        var orderId = $(this).data('order-id'); // Lấy id của order từ attribute data
        var targetId = '#searchFilter' + orderId; // Tạo id của div tương ứng
        var detailsId = '#filterDetails' + orderId; // Tạo id của div tương ứng
        var orderDetails = '#orderDetails' + orderId;
        var order = $(this).data('order'); // Lấy thông tin order từ thuộc tính data-order
        console.log(order);
        $('#customer_name' + orderId).val(order.customer_name);
        $('#phone' + orderId).val(order.phone);

        // Lấy ngày hiện tại và ngày 30 ngày trước
        var today = new Date();
        today.setHours(today.getHours() + 7); // Thêm 7 giờ để chuyển sang múi giờ GMT+7
        var pastDate = new Date(today);
        pastDate.setDate(pastDate.getDate() - 30);

        // Định dạng ngày tháng theo yyyy-mm-dd để phù hợp với input[type='date'] trong HTML
        var formattedToday = today.toISOString().slice(0, 10);
        var formattedPastDate = pastDate.toISOString().slice(0, 10);

        $('#from_date' + orderId).val(formattedPastDate);
        $('#to_date' + orderId).val(formattedToday);
        $(targetId).toggle(); // Toggle hiển thị div
        $(detailsId).toggle();
        $(orderDetails).toggle();
    });

    $('#ordersTable').on('click', '.btn-filter', function() {
        var order = $(this).data('order');
        //console.log(order);
        fetchOrderDetails(order.id);
    });

    function fetchOrderDetails(orderId) {
        var fromDate = $('#from_date' + orderId).val();
        var toDate = $('#to_date' + orderId).val();
        var customerName = $('#customer_name' + orderId).val();
        var phone = $('#phone' + orderId).val();
        console.log({ fromDate, toDate, customerName, phone, orderId });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: `get-recovery-order-details`,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ fromDate, toDate, customerName, phone, orderId }),
            success: function(data) {
                console.log(data);
                displayOrderDetails(data, orderId);
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    }

    // Xây dựng bảng dữ liệu
    function displayOrderDetails(data, orderId) {
        const detailsDiv = $(`#filterDetails${orderId} td`);

        // Xây dựng bảng dữ liệu
        function buildTable(orders, title) {
            let tableHtml = `<div class="col-6"><h5>${title}</h5><div class="table-responsive" style="max-height: 400px; overflow-y: auto;"><table class='table table-striped bg-light'>
            <thead><tr>
            <th>STT</th>
            <th>Mã SP</th>
            <th>Tên sản phẩm</th>
            <th>SL</th>
            <th>Ngày</th>
            </tr></thead><tbody>`;
            orders.forEach(order => {
                order.order_details.forEach((item, index) => {
                    var quantity = item.packing * item.thung + item.le; // Tính toán số lượng
                    var date = new Date(item.created_at);
                    var formattedDate = `${date.getDate()}/${date.getMonth() + 1}`; // Định dạng ngày tháng theo d/m
                    tableHtml += `<tr><td>${index + 1}</td><td>${item.product_code}</td><td>${item.product_name}</td><td>${quantity}</td><td>${formattedDate}</td></tr>`;
                });
            });
            tableHtml += '</tbody></table></div></div>';
            return tableHtml;
        }

        // Lấy thông tin về các đơn Giao ngay và Giao sau
        const lateDeliveryTable = buildTable(data.lateDelivery, 'Giao sau');
        const immediateDeliveryTable = buildTable(data.immediateDelivery, 'Giao ngay');

        // Thêm vào DOM trong row
        detailsDiv.html(`<div class="row">${lateDeliveryTable}${immediateDeliveryTable}</div>`);
    }





});
</script>
@endpush