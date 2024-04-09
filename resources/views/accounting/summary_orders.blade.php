@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="container mt-3">
    <h4>Danh sách đơn hàng tổng hợp</h4>
    <div class="filter-section mb-3">
        <form id="searchForm" class="form-inline">
            <div class="form-group mb-2">
                <input type="date" class="form-control" id="reportDate" name="report_date" placeholder="Ngày báo cáo">
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
                <input type="number" class="form-control" id="transaction_id" name="transaction_id" placeholder="Số giao dịch" min="1">
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <select class="form-control" id="is_group" name="is_group">
                    <option value="">Loại đơn</option>
                    <option value="1">Giao Ngay</option>
                    <option value="0">Giao Sau</option>
                </select>
            </div>
            <div class="form-group mx-sm-3 mb-2">
                <select class="form-control" id="has_transaction_id" name="has_transaction_id">
                    <option value="">Trạng thái</option>
                    <option value="1">Đã thanh toán</option>
                    <option value="0">Chưa thanh toán</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary mb-2">Tìm kiếm</button>
        </form>
        <button id="showSummaryBtn" class="btn btn-warning">Thu tiền</button>
    </div>

    <div id="ordersTable">
        <table class="table">
            <thead>
                <tr>
                    <th></th> <!-- Thêm cột cho checkbox -->    
                    <th></th>
                    <th>Ngày BC</th>
                    <th>NVBH</th>
                    <th>Số HĐ</th>
                    <th>Số giao dịch</th>
                    <th>Chiết khấu</th>
                    <th>Thành tiền</th>
                    <th>Loại</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @include('accounting.partials.summary_orders_table', ['summaryOrders' => $summaryOrders])
            </tbody>
        </table>
    </div>

    <div id="pagination-links" class="mt-3">
        <!-- Pagination links loaded here by AJAX -->
    </div>
</div>

<!-- Modal -->
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
                            <input type="number" id="transfer_amount" class="form-control" placeholder="Số chuyển khoản" />
                        </div>
                        <div class="col-md-6">
                            <select id="submitter_id" class="form-control">
                                @foreach($saleStaffs as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Note inputs start -->
                    <div class="row">
                        <div class="col-4 col-md-2">
                            <input type="number" id="note_500" class="form-control form-control-sm note-input" placeholder="500" data-denomination="500000" />
                        </div>
                        <div class="col-4 col-md-2">
                            <input type="number" id="note_200" class="form-control form-control-sm note-input" placeholder="200" data-denomination="200000"/>
                        </div>
                        <div class="col-4 col-md-2">
                            <input type="number" id="note_100" class="form-control form-control-sm note-input" placeholder="100" data-denomination="100000"/>
                        </div>
                        <div class="col-4 col-md-2">
                            <input type="number" id="note_50" class="form-control form-control-sm note-input" placeholder="50" data-denomination="50000"/>
                        </div>
                        <div class="col-4 col-md-2">
                            <input type="number" id="note_20" class="form-control form-control-sm note-input" placeholder="20" data-denomination="20000"/>
                        </div>
                        <div class="col-4 col-md-2">
                            <input type="number" id="note_10" class="form-control form-control-sm note-input" placeholder="10" data-denomination="10000"/>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-4 col-md-2">
                            <input type="number" id="note_5" class="form-control form-control-sm note-input" placeholder="5" data-denomination="5000"/>
                        </div>
                        <div class="col-4 col-md-2">
                            <input type="number" id="note_2" class="form-control form-control-sm note-input" placeholder="2" data-denomination="2000"/>
                        </div>
                        <div class="col-4 col-md-2">
                            <input type="number" id="note_1" class="form-control form-control-sm note-input" placeholder="1" data-denomination="1000"/>
                        </div>
                    </div>
                    <!-- Note inputs end -->

                    <div class="row mt-3">
                        <div class="col">
                            <textarea id="notes" rows=1 class="form-control" placeholder="Ghi chú"></textarea>
                        </div>
                    </div>
                    <!-- Thêm phần tử để hiển thị tổng số tiền -->
                    <div id="totalsDisplay" class="mb-3">
                        <p><strong>Chuyển khoản:</strong> <span id="transferTotal">0</span></p>
                        <p><strong>Tiền mặt:</strong> <span id="notesTotal">0</span></p>
                        <p><strong>Tổng nhận:</strong> <span id="combinedTotal">0</span></p>
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

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function fetchData(url) {
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                //console.log(response.summaryOrders);
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

    // Xử lý form tìm kiếm
    let currentSearchParams = "";
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        currentSearchParams = $(this).serialize(); // Lưu trữ các tham số tìm kiếm
        fetchData('{{ route('summary_orders') }}?' + currentSearchParams);
    });

    // Xử lý sự kiện click trên links phân trang
    $('#pagination-links').on('click', 'a.relative', function(e) {
        e.preventDefault();
        var href = $(this).attr('href');
        fetchData(href + '&' + currentSearchParams); // Thêm tham số tìm kiếm vào URL phân trang
    });

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
        const groupedProducts = {};
        //console.log(summaryOrder);
        // Duyệt qua từng groupOrder trong summaryOrder
        summaryOrder.group_order.forEach(groupOrder => {
            // Duyệt qua từng accountingOrder trong groupOrder
            groupOrder.accounting_orders.forEach(accountingOrder => {
                // Duyệt qua từng orderDetail trong accountingOrder
                accountingOrder.order_details.forEach(detail => {
                    // Bỏ qua sản phẩm đặc biệt
                    if (!detail.is_special) {
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
                });
            });
        });
        // ID container để cập nhật thông tin chi tiết sản phẩm
        const containerId = 'productDetails' + summaryOrder.id;
        let contentHtml = `<table class="table">
            <thead>
                <tr>
                    <th>Mã SP</th>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Chiếu khấu</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>`;
        // Duyệt qua từng sản phẩm trong đối tượng groupedProducts và tạo hàng mới trong bảng
        Object.values(groupedProducts).forEach(product => {
            contentHtml += `
                <tr>
                    <td>${product.product_code}</td>
                    <td>${product.product_name}</td>
                    <td>${product.quantity}</td>
                    <td>${product.discount}</td>
                    <td>${product.payable}</td>
                </tr>`;
        });
        contentHtml += `</tbody></table>`;
        // Cập nhật container với thông tin sản phẩm đã gộp
        document.getElementById(containerId).innerHTML = contentHtml;
    }

    $('#showSummaryBtn').click(function() {
        // Khởi tạo và bắt đầu nội dung HTML của bảng
        var tableContent = buildTableContent();
        
        // Cập nhật nội dung cho modal và hiển thị modal
        //$('#summaryModalBody').html(tableContent);
        // Thêm tableContent vào container
        $('#tableContainer').html(tableContent);
        $('#summaryModal').modal('show');

        // Cập nhật tổng số khi hiển thị modal
        updateTotals(); 
    });
    
    function buildTableContent() {
        var totalDiscount = 0;
        var totalAmount = 0;

        var tableContent = `
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>NVBH</th>
                        <th>Số HĐ</th>
                        <th>Chiết khấu</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>`;

        // Duyệt qua mỗi hàng có checkbox được tích
        $(".order-checkbox:checked").each(function(index) {
            var summaryOrderId = $(this).data('id');
            var row = $(this).closest("tr");
            var chietKhau = parseInt(row.find("td:eq(6)").text().replace(/,/g, '')) || 0;
            var thanhTien = parseInt(row.find("td:eq(7)").text().replace(/,/g, '')) || 0;

            totalDiscount += chietKhau;
            totalAmount += thanhTien;

            tableContent += `
                <tr>
                    <td style="display: none;" data-id="${summaryOrderId}"></td>    
                    <td>${index + 1}</td>
                    <td><span class="staff-cell">${row.find("td:eq(3)").text()}</span></td>
                    <td>${row.find("td:eq(4)").text()}</td>
                    <td>${chietKhau.toLocaleString()}</td>
                    <td>${thanhTien.toLocaleString()}</td>
                </tr>`;
        });

        // Đóng thẻ tbody và thêm hàng tổng số
        tableContent += `
                <tr>
                    <td colspan="3"><strong>Tổng</strong></td>
                    <td><strong>${totalDiscount.toLocaleString()}</strong></td>
                    <td><strong>${totalAmount.toLocaleString()}</strong></td>
                </tr>
                </tbody>
            </table>`;

        return tableContent;
    }

    function updateTotals() {
        var totalNotes = 0;
        var transferAmount = parseInt($('#transfer_amount').val()) || 0;

        // Chỉ lựa chọn các input có class="note-input"
        $('#summaryForm input.note-input').each(function() {
            var denomination = $(this).data('denomination');
            var quantity = parseInt($(this).val(), 10) || 0;
            totalNotes += denomination * quantity;
        });

        var combinedTotal = transferAmount + totalNotes;

        $('#transferTotal').text(transferAmount.toLocaleString());
        $('#notesTotal').text(totalNotes.toLocaleString());
        $('#combinedTotal').text(combinedTotal.toLocaleString());
    }
    // Gắn sự kiện 'input' vào tất cả các input trong form để cập nhật tổng số tiền mỗi khi giá trị thay đổi
    $('#summaryForm input').on('input', updateTotals);


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

        if (!allSame) {
            alert("Không cùng nhân viên.");
        } else {
            // Thu thập dữ liệu cơ bản
            var transferAmount = parseInt($('#transfer_amount').val()) || 0;
            var submitterId = $('#submitter_id').val();
            var notes = $('#notes').val();

            // Thu thập dữ liệu về các note
            var notesData = {};
            $('#summaryForm input.note-input').each(function() {
                var denomination = $(this).data('denomination');
                var quantity = parseInt($(this).val(), 10) || 0;
                notesData['note_' + denomination] = quantity; // Tạo một key dựa trên mệnh giá và lưu số lượng
            });

            // Tính tổng số tiền
            var totalAmount = transferAmount;
            for (var denomination in notesData) {
                totalAmount += parseInt(denomination.split('_')[1]) * notesData[denomination];
            }

            // Thu thập ID của summary_orders được chọn
            var summaryOrderIds = [];
            // Duyệt qua mỗi hàng trong tbody của bảng, loại trừ hàng tổng kết
            $("#tableContainer table tbody tr:not(:last-child)").each(function() {
                // Lấy giá trị data-id từ <td> ẩn đầu tiên trong mỗi hàng
                var summaryOrderId = $(this).find("td:first-child").data('id');
                // Thêm ID vào mảng nếu nó tồn tại
                if (summaryOrderId) {
                    summaryOrderIds.push(summaryOrderId);
                }
            });
            //console.log(summaryOrderIds); // Mảng chứa các ID thu thập được

            // Tạo một object để chứa tất cả dữ liệu
            var transactionData = {
                staff_id: staffIds[0],
                transfer_amount: transferAmount,
                total_amount: totalAmount,
                submitter_id: submitterId,
                notes: notes,
                summary_order_ids: summaryOrderIds,
                ...notesData // Sử dụng spread syntax để thêm các key của notesData vào object này
            };
            //console.log(transactionData);
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
                    alert("Giao dịch thành công.");
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
        }
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
            url: 'update-is-entered/' + id, // Thay thế đường dẫn bằng đường dẫn thực tế của bạn
            type: 'PUT',
            success: function(response) {
                location.reload();
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
                alert("Cập nhật thành công.");
                $('#editModal').modal('hide');
                location.reload();
            },
            error: function(error) {
                // Xử lý lỗi
                console.error("Có lỗi khi cập nhật: ", error);
            }
        });
    });







});


</script>
@endpush
