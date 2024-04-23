@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="container mt-3">
    <div class="card">
        <div class="filter-section mb-3">
            <form id="searchForm" class="form-inline">
                <div class="form-group mb-2">
                    <input type="date" class="form-control form-control-sm" id="reportDate" name="report_date" placeholder="Ngày báo cáo">
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <select id="staff" name="staff" class="form-control form-control-sm">
                        <option value="">Chọn nhân viên</option>
                        @foreach($saleStaffs as $staff)
                            <option value="{{ $staff->name }}">{{ $staff->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-2">
                    <input type="number" class="form-control form-control-sm" id="transaction_id" name="transaction_id" placeholder="Số giao dịch" min="1">
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <select class="form-control form-control-sm" id="is_group" name="is_group">
                        <option value="">Loại đơn</option>
                        <option value="1">Giao Ngay</option>
                        <option value="2">Giao Sau</option>
                        <option value="3">Thu hồi</option>
                        <option value="4">Giao Ngay & Thu hồi</option>
                        <option value="5">Giao Ngay & TH Giao Ngay</option>
                        <option value="6">Giao Sau & TH Giao Sau</option>
                    </select>
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <select class="form-control form-control-sm" id="has_transaction_id" name="has_transaction_id">
                        <option value="">Trạng thái</option>
                        <option value="1">Đã tạo phiếu</option>
                        <option value="0">Chưa tạo phiếu</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-sm mb-2">Tìm kiếm</button>
            </form>
            <div class="d-flex align-items-center">
                <button id="showSummaryBtn" class="btn btn-warning mr-3">Tạo phiếu thu</button>
                <div>Đã chọn: <span class="badge badge-primary" id="selectedCount">0</span> hàng</div>
                <button id="showRecovery" class="btn btn-secondary ml-3">Xem tổng hợp chi tiết</button>
            </div>
        </div>
    </div>

    <div class="card-body">
        <div id="ordersTable">
            <table class="table table-auto w-full">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="checkAll">
                        </th>
                        <th></th>
                        <th>Ngày BC</th>
                        <th>NVBH</th>
                        <th style="display: none;">Số HĐ</th>
                        <th>Số GD</th>
                        <th>C.Khấu</th>
                        <th>T.Tiền</th>
                        <th>Loại</th>
                        <th>Khách hàng</th>
                        <th>Ghi chú</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @include('accounting.partials.summary_orders_table', ['summaryOrders' => $summaryOrders])
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-row-reverse align-items-center"> <!-- flex-row-reverse đảo ngược thứ tự hiển thị các phần tử con -->
            <div class="form-inline w-25">
                <label for="perPage" class="ml-2">Số hàng:</label>
                <select id="perPage" class="form-control form-control-sm w-25">
                    <option value="20">20</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div id="pagination-links" class="d-flex align-items-center w-100">
                <!-- Nội dung của pagination-links -->
            </div>
        </div>
    </div>
</div>

<!-- Modal tạo phiếu thu-->
<div class="modal fade" id="summaryModal" tabindex="-1" role="dialog" aria-labelledby="summaryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="summaryModalLabel">Thông tin thanh toán</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!-- Thêm form để nhập thông tin -->
            <form id="summaryForm">
                <div class="container">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="staff">NV phụ trách:</label>
                            <select id="staff_id" class="form-control">
                                @foreach($saleStaffs as $staff)
                                    <option value="{{ $staff->name }}">{{ $staff->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="pay_date">Ngày báo cáo:</label>
                            <div id="pay_date"></div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col">
                            <textarea id="notes" rows=1 class="form-control" placeholder="Ghi chú"></textarea>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="customer">Khách hàng:</label>
                            <input type="text" id="customer" name="customer" class="form-control" placeholder="Tên khách hàng">
                        </div>
                    </div>
                </div>
                
            </form>

            <div class="modal-body" id="summaryModalBody">
                <div id="tableContainer"></div>
                <!-- Đây là nơi hiển thị thông tin tóm tắt các đơn hàng đã chọn -->
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="addSummaryBtn">Thu</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal xem tổng hợp đơn thu hồi-->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="productModalLabel"><strong>* Lưu ý: Phải chọn đúng các đơn cần xem</strong></h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table">
          <thead>
            <tr>
              <th scope="col">STT</th>
              <th scope="col">Mã SP</th>
              <th scope="col">Tên sản phẩm</th>
              <th scope="col">Đơn giá</th>
              <th scope="col">Số lượng</th>
              <th scope="col">Chiết khấu</th>
              <th scope="col">Thành tiền</th>
            </tr>
          </thead>
          <tbody id="productRecoveryDetails">
            <!-- Dữ liệu sản phẩm sẽ được thêm vào đây bằng JavaScript -->
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Chỉnh Sửa -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Chỉnh Sửa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <input type="hidden" id="edit-id">
                    <div class="form-group">
                        <label for="edit-report-date">Ngày báo cáo</label>
                        <input type="date" class="form-control" id="edit-report-date">
                    </div>
                    <div class="form-group">
                        <label for="edit-invoice-code">Số hóa đơn</label>
                        <input type="text" class="form-control" id="edit-invoice-code">
                    </div>
                    <div class="form-group">
                        <label for="edit-is-entered">Trạng thái nhập</label>
                        <select class="form-control" id="edit-is-entered">
                            <option value="1">Đã nhập</option>
                            <option value="0">Chưa</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-notes">Ghi chú</label>
                        <textarea class="form-control" id="edit-notes"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" id="saveChanges">Lưu Thay Đổi</button>
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
$(document).ready(function() {
    // Xử lý form tìm kiếm
    let currentSearchParams = "";
    let currentPerPage = "";
    let perPage = $('#perPage').val();
    var summaryOrders = @json($summaryOrders)['data'];
    console.log(summaryOrders);
    function fetchData(url) {
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                // console.log(response.summaryOrders);
                $('#ordersTable tbody').html(response.table);
                $('#pagination-links').html(response.links);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    // Gọi hàm fetchData khi trang được tải để tải dữ liệu ban đầu
    fetchData('{{ route('summary_orders') }}');

    
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        perPage = $('#perPage').val();
        //currentSearchParams = $(this).serialize(); // Lưu trữ các tham số tìm kiếm
        currentSearchParams = updateSearchParams('per_page', perPage, $(this).serialize());
        fetchData('{{ route('summary_orders') }}?' + currentSearchParams);
    });

    // Xử lý sự kiện click trên links phân trang
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
        perPage = $(this).val();
        currentSearchParams = updateSearchParams('per_page', perPage, currentSearchParams);
        fetchData('{{ route('summary_orders') }}?' + currentSearchParams);
    });
    function updateSearchParams(key, value, paramsString) {
        var searchParams = new URLSearchParams(paramsString);
        searchParams.set(key, value);
        return searchParams.toString();
    }

    // Xử lý nút mở rộng để hiển thị chi tiết đơn hàng
    $('#ordersTable').on('click', '.expand-button', function() {
        var targetId = $(this).data('target'); // Lấy ID của phần tử chi tiết dựa trên thuộc tính data-target
        var summaryOrder = $(this).data('summary-order'); // Lấy summaryOrder từ thuộc tính data-summary-order
        $(targetId).toggle(); // Chuyển đổi trạng thái hiển thị của phần tử chi tiết
        // Chỉ gọi hàm loadGroupedProducts khi phần tử chi tiết được hiển thị
        if ($(targetId).is(":visible")) {
            loadGroupedProducts(summaryOrder); // Gọi hàm loadGroupedProducts với ID của summaryOrder
        }
        // Thay đổi nút từ "+" sang "-" và ngược lại
        $(this).text($(this).text() === '+' ? '-' : '+');
    });

    function loadGroupedProducts(summaryOrder) {
        bodyHtml = ``;
        const groupedProducts = {};
        const groupedSpecial = {};
        //console.log(summaryOrder);
        if(summaryOrder.is_recovery == 0 ){
            summaryOrder.group_order.forEach(groupOrder => { // Duyệt qua từng groupOrder trong summaryOrder
                groupOrder.accounting_orders.forEach(accountingOrder => { // Duyệt qua từng accountingOrder trong groupOrder
                    accountingOrder.order_details.forEach(detail => { // Duyệt qua từng orderDetail trong accountingOrder
                        if (!detail.is_special) { // Bỏ qua sản phẩm đặc biệt
                            const key = detail.product_code;
                            //console.log(detail);
                            // Nếu sản phẩm chưa có trong đối tượng, thêm vào
                            if (!groupedProducts[key]) {
                                groupedProducts[key] = {
                                    product_code: detail.product_code,
                                    product_name: detail.product_name,
                                    quantity: 0, // Khởi tạo số lượng là 0
                                    discount: 0,
                                    payable: 0,
                                };
                            }
                            // Cộng dồn số lượng
                            groupedProducts[key].quantity += (detail.packing * detail.thung) + detail.le;
                            groupedProducts[key].discount += detail.discount;
                            groupedProducts[key].payable += detail.payable;
                        }
                        else{ // trường hợp đặt biệt là khuyến mãi, mỗi SP chỉ có 1 hàng
                            const special_key = detail.product_code;
                            if (!groupedSpecial[special_key]) {
                                groupedSpecial[special_key] = {
                                    product_code: detail.product_code,
                                    product_name: detail.product_name,
                                    quantity: 0,
                                };
                            }
                            groupedSpecial[special_key].quantity += detail.le;
                        }
                    });
                });
            });
            //console.log(groupedSpecial);
            // Duyệt qua từng sản phẩm trong đối tượng groupedProducts và tạo hàng mới trong bảng
            Object.values(groupedProducts).forEach((product, index) => {
                bodyHtml += `
                    <tr>
                        <td>${index + 1}</td>    
                        <td>${product.product_code}</td>
                        <td>${product.product_name}</td>
                        <td class="text-right">${product.quantity}</td>
                        <td class="text-right">${product.discount.toLocaleString()}</td>
                        <td class="text-right">${product.payable.toLocaleString()}</td>`;
            });
            if (Object.keys(groupedSpecial).length > 0) {
                Object.values(groupedSpecial).forEach((product, index) => {
                    bodyHtml += `
                        <tr>
                            <td></td>    
                            <td>${product.product_code}</td>
                            <td>${product.product_name}</td>
                            <td class="text-right">${product.quantity}</td>
                        </tr>`;
                });
            }
            bodyHtml += `</tr>`;
        }
        else {// nếu là đơn thu hồi
            summaryOrder.group_order.forEach(groupOrder => { // Duyệt qua từng groupOrder trong summaryOrder
                groupOrder.recovery_orders.forEach(recoveryOrder => { // Duyệt qua từng accountingOrder trong groupOrder
                    recoveryOrder.recovery_details.forEach(detail => { // Duyệt qua từng orderDetail trong accountingOrder
                        const key = detail.product_code;
                        //console.log(detail);
                        // Nếu sản phẩm chưa có trong đối tượng, thêm vào
                        if (!groupedProducts[key]) {
                            groupedProducts[key] = {
                                product_code: detail.product_code,
                                product_name: detail.product_name,
                                //quantity: detail.quantity, //bản cũ
                                quantity: detail.packing * detail.thung + detail.le,
                                discount: detail.discount,
                                payable: detail.payable,
                            };
                        }
                    });
                });
            });
            // Duyệt qua từng sản phẩm trong đối tượng groupedProducts và tạo hàng mới trong bảng
            Object.values(groupedProducts).forEach((product, index) => {
                bodyHtml += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${product.product_code}</td>
                        <td>${product.product_name}</td>
                        <td class="text-right">${product.quantity}</td>
                        <td class="text-right">${product.discount.toLocaleString()}</td>
                        <td class="text-right">${product.payable.toLocaleString()}</td>
                    </tr>`;
            });
        }//end if

        // ID container để cập nhật thông tin chi tiết sản phẩm
        const containerId = 'productDetails' + summaryOrder.id;
        let contentHtml = `<table class="table">
            <thead class="bg-info text-white">
                <tr>
                    <th>STT</th>    
                    <th>Mã SP</th>
                    <th>Tên sản phẩm</th>
                    <th class="text-right">Số lượng</th>
                    <th class="text-right">Chiết khấu</th>
                    <th class="text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody style="background-color: #d6e9ec;">`;
        contentHtml += bodyHtml + `</tbody></table>`;
        // Cập nhật container với thông tin sản phẩm đã gộp
        document.getElementById(containerId).innerHTML = contentHtml;
    }

    $('#showSummaryBtn').click(function() {
        // Khởi tạo và bắt đầu nội dung HTML của bảng
        var tableContent = buildTableContent();
        //$('#summaryModalBody').html(tableContent);
        // Thêm tableContent vào container
        $('#tableContainer').html(tableContent);
        $('#summaryModal').modal('show');
    });
    
    function buildTableContent() {
        var totalDiscount = 0;
        var totalAmount = 0;
        var array_report_date = [];

        var tableContent = `
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NVBH</th>
                        <th>Số HĐ</th>
                        <th>cKhấu</th>
                        <th>tTiền</th>
                        <th>GD</th>
                    </tr>
                </thead>
                <tbody>`;

        var firstCheckedCheckbox = $(".order-checkbox:checked").first();
        if (firstCheckedCheckbox.length > 0) { // Kiểm tra để đảm bảo rằng có ít nhất một checkbox được chọn
            var staffName = firstCheckedCheckbox.closest("tr"); // Lấy giá trị của checkbox thẻ tr
            $('#staff_id').val(staffName.find("td:eq(3)").text().trim());//chọn NV
        }
        if ($(".order-checkbox:checked").length == 1) { // Nếu chỉ chọn 1 đơn
            var customer = $(".order-checkbox:checked").closest("tr"); // Lấy giá trị của checkbox thẻ tr
            $('#customer').val(customer.find("td:eq(9)").text().trim());//chọn NV
        }else{
            $('#customer').val('');
        }
        
        // Duyệt qua mỗi hàng có checkbox được tích
        $(".order-checkbox:checked").each(function(index) {
            var summaryOrderId = $(this).data('id');
            var row = $(this).closest("tr");
            var chietKhau = parseInt(row.find("td:eq(6)").text().replace(/,/g, '')) || 0;
            var type = row.find("td:eq(8)").text().trim();
            if(type.startsWith('TH')){//nếu là thu hồi
                var thanhTien = -1 * parseInt(row.find("td:eq(7)").text().replace(/,/g, '')) || 0;
            }else{
                var thanhTien = parseInt(row.find("td:eq(7)").text().replace(/,/g, '')) || 0;
            }
            array_report_date.push(row.find("td:eq(2)").text());

            totalDiscount += chietKhau;
            totalAmount += thanhTien;

            var transaction = row.find("td:eq(5)").text();
            tableContent += `
                <tr>
                    <td style="display: none;" data-id="${summaryOrderId}"></td>   
                    <td style="display: none;" data-type="${type}"></td>
                    <td>${index + 1}</td>
                    <td><span class="staff-cell">${row.find("td:eq(3)").text()}</span></td>
                    <td>${row.find("td:eq(4)").text()}</td>
                    <td>${chietKhau.toLocaleString()}</td>
                    <td>${thanhTien.toLocaleString()}</td>
                    <td data-transaction="${transaction}">${
                        (transaction !== '' && transaction !== '_') ? '<span style="color: green;">✔</span>' : ''
                    }</td>
                </tr>`;
        });

        // Đóng thẻ tbody và thêm hàng tổng số
        tableContent += `
                <tr>
                    <td colspan="3"><strong>Tổng</strong></td>
                    <td><strong>${totalDiscount.toLocaleString()}</strong></td>
                    <td data-totalAmount="${totalAmount}"><strong>${totalAmount.toLocaleString()}</strong></td>
                </tr>
                </tbody>
            </table>`;

        const firstElement = array_report_date[0];// Lấy giá trị đầu tiên của mảng để so sánh
        // Sử dụng phương thức every để kiểm tra mọi phần tử có giống nhau không
        const dateSame = array_report_date.every(element => element === firstElement);
        if (dateSame) {
            var parts = firstElement.trim().split('/'); // Tách ngày và tháng
            var day = parts[0];
            var month = parts[1];
            var year = parts[2];
            var date = new Date(year, month - 1, day); // month - 1 vì JavaScript đếm tháng từ 0
            date.setHours(date.getHours() + 7); // Thêm 7 giờ cho múi giờ GMT+0700
            // Định dạng lại ngày tháng sang Y-m-d
            var formattedDate = date.toISOString().substring(0, 10); // Cắt chuỗi để lấy định dạng Y-m-d
            //$('#dateInput').val(formattedDate);
            //console.log(formattedDate);
            $('#pay_date').text(formattedDate);
        } else {
            $('#pay_date').text('Không cùng ngày');
        }
        
        return tableContent;
    }


    $('#addSummaryBtn').click(function() {
        // Lấy tất cả các giá trị staff ID từ các cell có class 'staff-cell' trong bảng
        var staffIds = $('#tableContainer .staff-cell').map(function() {
            // Loại bỏ khoảng trắng ở đầu và cuối chuỗi
            var trimmedText = $.trim($(this).text());
            // Thay thế một chuỗi các khoảng trắng bằng một khoảng trắng đơn
            var cleanedText = trimmedText.replace(/\s+/g, ' ');
            return cleanedText;
        }).get();
        // Kiểm tra xem tất cả các staff ID có giống nhau không
        var allSame = staffIds.every(function(staffId) {
            return staffId === staffIds[0];
        });

        var types = $('#tableContainer td[data-type]').map(function() {
            return $.trim($(this).data('type'));  // Sử dụng phương thức .data() của jQuery để lấy giá trị của data attribute
        }).get();  // Chuyển kết quả từ jQuery object thành mảng JavaScript
        // console.log(types);
        var hasImmediate = types.some(function(type) {
            return type === "Giao ngay";
        });
        var hasScheduled = types.some(function(type) {
            return type === "Giao sau";
        });
        
        var allowedStaff = ['Anh Hưng', 'Anh Kiều', 'Anh Hùng']; // Danh sách các nhân viên được phép
 
        if (!allSame && hasImmediate) {//nếu có đơn Giao ngay thì cùng nhân viên mới thực hiện tiếp
            alert("Không cùng nhân viên.");
        } else if(hasScheduled && !allowedStaff.includes($('#staff_id').val())){
                alert("Phải chọn Anh Hưng, Anh Kiều hoặc Anh Hùng!");
            }
            else {
                var notes = $('#notes').val();//lấy giá trị ô nhập notes
                var payDate = $('#pay_date').text();//lấy giá trị ngày báo cáo, cũng là ngày trả
                var staffId = $('#staff_id').val();//lấy tên nhân viên
                var customer = $('#customer').val();//lấy tên khách hàng

                var tdElement = $('#tableContainer table tbody tr:last-child td[data-totalAmount]')[0]; // Lấy phần tử DOM
                var totalAmount = tdElement.dataset.totalamount; // Truy cập dataset và lấy giá trị

                // console.log(totalAmount);
                // Thu thập ID của summary_orders được chọn
                var summaryOrderIds = [];
                var shouldStop = false;  // Cờ để kiểm tra xem có nên dừng toàn bộ sự kiện hay không
                // Duyệt qua mỗi hàng trong tbody của bảng, loại trừ hàng tổng kết
                $("#tableContainer table tbody tr:not(:last-child)").each(function() {
                    // Lấy giá trị data-id từ <td> ẩn đầu tiên trong mỗi hàng
                    var summaryOrderId = $(this).find("td:first-child").data('id');
                    var transaction = $(this).find("td:eq(7)").data('transaction');//tìm cột thứ 8
                    if (transaction !== '' && transaction !== '_' && transaction !== undefined) {
                        alert("Không tạo được do có đơn có phiếu thu rồi.");
                        //alert(transaction);
                        shouldStop = true;  // Đặt cờ thành true để dừng các hành động tiếp theo
                        return false; //thoát each
                    }
                    //console.log(transaction);
                    // Thêm ID vào mảng nếu nó tồn tại
                    if (summaryOrderId) {
                        summaryOrderIds.push(summaryOrderId);
                    }
                });
                if (shouldStop) {
                    event.preventDefault();  // Ngăn không cho bất kỳ hành động mặc định nào, như submit form
                    return;  // Thoát khỏi hàm sự kiện click
                }
                //console.log(staffId); // Mảng chứa các ID thu thập được
                // Tạo một object để chứa tất cả dữ liệu
                var transactionData = {
                    staff_id: staffId,
                    customer: customer,
                    total_amount: totalAmount,
                    notes: notes,
                    pay_date: payDate,
                    summary_order_ids: summaryOrderIds,
                };
                console.log(transactionData);
                // Gọi hàm để gửi dữ liệu
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "save-transaction",
                    contentType: "application/json",
                    data: JSON.stringify(transactionData),
                    success: function(response) {
                        // Xử lý khi dữ liệu được gửi thành công
                        console.log("Transaction saved successfully.", response);
                        notify500();
                        $('#summaryModal').modal('hide'); // Đóng modal
                        // Thay thế checkbox bằng icon tick màu xanh cho các hàng được chọn
                        $('.order-checkbox:checked').each(function() {
                            $(this).closest('.checkbox-container').html('<i class="fas fa-check text-success"></i>');
                        });
                    },
                    error: function(xhr, status, error) {
                        // Xử lý khi có lỗi
                        console.error("Error saving transaction.", error);
                        alert("Error saving transaction.");
                    }
                });//end ajax
            }//end else
        
    });

    $('#showRecovery').click(function() {
        let selectedOrders = [];
        $(".order-checkbox:checked").each(function(index) {
        let orderId = $(this).data('id');
        // Lấy dữ liệu từ summaryOrders dựa trên id được lựa chọn
        selectedOrders.push(summaryOrders.find(order => order.id === orderId));
        });

        // Gộp và hiển thị dữ liệu trong modal
        let products = {};
        let totalDiscount = 0;
        let totalPayable = 0;
        let totalQuantity = 0;
        let stt = 0; // Biến đếm cho số thứ tự
        selectedOrders.forEach(order => {
        order.group_order.forEach(group => {
            group.recovery_orders.forEach(recovery => {
            recovery.recovery_details.forEach(detail => {
                let key = detail.product_code;
                if (!products[key]) {
                products[key] = { ...detail, stt: Object.keys(products).length + 1, quantity: detail.packing * detail.thung + detail.le, totalDiscount: detail.discount, totalPrice: detail.payable };
                } else {
                products[key].quantity += detail.packing * detail.thung + detail.le;
                products[key].totalDiscount += detail.discount;
                products[key].totalPrice += detail.payable;
                }
                // Tính tổng chiết khấu và tổng thành tiền
                totalQuantity += detail.packing * detail.thung + detail.le;
                totalDiscount += detail.discount;
                totalPayable += detail.payable;
            });
            });
        });
        });
        // Hiển thị kết quả trong modal
        const productDetails = $('#productRecoveryDetails');
        productDetails.empty(); // Xóa các hàng hiện có
        Object.values(products).forEach(product => {
        stt++;
        let row = `<tr>
            <td>${stt}</td>
            <td>${product.product_code}</td>
            <td>${product.product_name}</td>
            <td class="text-right">${product.price.toLocaleString()}</td>
            <td class="text-right">${product.quantity}</td>
            <td class="text-right">${product.totalDiscount.toLocaleString()}</td>
            <td class="text-right">${product.totalPrice.toLocaleString()}</td>
        </tr>`;
        productDetails.append(row);
        });
        // Thêm hàng tổng kết
        let totalRow = `<tr class="table-info">
        <td colspan="4"><strong>Tổng cộng</strong></td>
        <td class="text-right"><strong>${totalQuantity.toLocaleString()}</strong></td>
        <td class="text-right"><strong>${totalDiscount.toLocaleString()}</strong></td>
        <td class="text-right"><strong>${totalPayable.toLocaleString()}</strong></td>
        </tr>`;
        productDetails.append(totalRow);

        $('#productModal').modal('show'); // Hiển thị modal
    });

    $('#ordersTable').on('click', '.btn-edit', function() {
        var order = $(this).data('order');
        //console.log(order);
        openEditForm(order);
    });

    $('#ordersTable').on('click', '.btn-enter', function() {
        var $btn = $(this);
        var id = $btn.data('id');
        var isEntered = $btn.data('entered');
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: 'update-is-entered/' + id,
            type: 'PUT',
            success: function(response) {
                setTimeout(function() {
                    location.reload();
                }, 500);
                //$btn.text('Đã nhập').data('entered', true);
            },
            error: function(xhr, status, error) {
                // Xử lý lỗi
                console.error(error);
                alert('Có lỗi xảy ra!');
            }
        });
    });

    function openEditForm(order) {
        // Điền dữ liệu vào form
        $('#edit-id').val(order.id);
        $('#edit-invoice-code').val(order.invoice_code);
        $('#edit-is-entered').val(order.is_entered ? "1" : "0");
        $('#edit-report-date').val(order.report_date);
        $('#edit-notes').val(order.notes);

        // Hiển thị modal
        $('#editModal').modal('show');
    }

    $('#saveChanges').click(function() {
        const editedData = {
            id: $('#edit-id').val(),
            invoice_code: $('#edit-invoice-code').val(),
            is_entered: $('#edit-is-entered').val(),
            report_date: $('#edit-report-date').val(),
            notes: $('#edit-notes').val()
        };
        //console.log(editedData);
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        $.ajax({
            url: 'update-summary-orders',
            method: 'PUT',
            data: editedData,
            success: function(response) {
                notify500();
                $('#editModal').modal('hide');
                setTimeout(function() {
                    location.reload();
                }, 500);
            },
            error: function(error) {
                // Xử lý lỗi
                console.error("Có lỗi khi cập nhật: ", error);
            }
        });
    });

    function notify500(){
        $('#successModal').modal('show');
        setTimeout(function() {
            $('#successModal').modal('hide');
        }, 500);
    }

    function updateCount() {
        var count = $('.checkItem:checked').length;
        $('#selectedCount').text(count);
    }
    $(document).on('click', '.checkItem', function() {
        updateCount();
    });
    updateCount();  



});


</script>
@endpush
