@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="container">
    <h3>Báo cáo thanh toán</h3>
    <div class="filter-section">
        <form id="searchForm">
            <div class="form-group">
                <label for="staff">NV phụ trách:</label>
                <select id="staff" name="staff" class="form-control">
                    <option value="">Chọn nhân viên</option>
                    @foreach($saleStaffs as $staff)
                        <option value="{{ $staff->name }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="pay_date">Ngày báo cáo:</label>
                <input type="date" id="pay_date" name="pay_date" class="form-control">
            </div>
            <button id="showSummaryBtn" class="btn btn-warning">Xem</button>
            <button type="submit" class="btn btn-primary">Tìm Kiếm</button>
        </form>
        
    </div>

    <div class="transactions-section mt-4">
        <table class="table" id="transactionsTable">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="checkAll">
                    </th>  
                    <th></th>  
                    <th>STT</th>
                    <th>Ngày BC</th>
                    <th>NV phụ trách</th>
                    <th>Tổng tiền</th>
                    <th>Chênh lệch</th>
                    <th>Ghi chú</th>
                    <th>Số giao dịch</th>
                </tr>
            </thead>
            <tbody>
                @include('accounting.partials.transactions_table', ['transactions' => $transactions])
            </tbody>
        </table>
        <div id="pagination-links" class="mt-3">

        </div>
    </div>
    <!-- Modal xem đơn-->
    <div class="modal fade" id="summaryModal" tabindex="-1" role="dialog" aria-labelledby="summaryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="summaryModalLabel">Thông tin thanh toán</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="summaryModalBody">
                    <div id="tableContainer"></div>
                    <!-- Đây là nơi hiển thị thông tin tóm tắt các đơn hàng đã chọn -->
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal thanh toán-->
    <div class="modal fade" id="addTransactionModal" tabindex="-1" role="dialog" aria-labelledby="summaryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="summaryModalLabel">Nhập thanh toán</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="container">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="total_amount">Tổng tiền:</label>
                            <div id="total_amount"></div>
                        </div>
                        <div class="col-md-6">
                            <label for="diff_amount">Chênh lệch:</label>
                            <div id="diff_amount"></div>
                        </div>
                    </div>
                </div>

                <!-- Thêm form để nhập thông tin -->
                <form id="addTransactionForm">
                    <div class="container">
                        <!-- Thêm input ẩn để lưu transaction_id -->
                        <input type="hidden" id="transaction_id" name="transaction_id" value="">
                        <input type="hidden" id="hiddenTransferTotal" name="transferTotal" value="">
                        <input type="hidden" id="hiddenCombinedTotal" name="combinedTotal" value="">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="number" name="transfer_amount" id="transfer_amount" class="form-control" placeholder="Số chuyển khoản" />
                            </div>
                            <div class="col-md-6">
                                <select name="staff_id" id="staff_id" class="form-control">
                                    @foreach($saleStaffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Note inputs start -->
                        <div class="row">
                            <div class="col-4 col-md-2">
                                <input type="number" name="note_500" id="note_500" class="form-control form-control-sm note-input" placeholder="500" data-denomination="500000" />
                            </div>
                            <div class="col-4 col-md-2">
                                <input type="number" name="note_200" id="note_200" class="form-control form-control-sm note-input" placeholder="200" data-denomination="200000"/>
                            </div>
                            <div class="col-4 col-md-2">
                                <input type="number" name="note_100" id="note_100" class="form-control form-control-sm note-input" placeholder="100" data-denomination="100000"/>
                            </div>
                            <div class="col-4 col-md-2">
                                <input type="number" name="note_50" id="note_50" class="form-control form-control-sm note-input" placeholder="50" data-denomination="50000"/>
                            </div>
                            <div class="col-4 col-md-2">
                                <input type="number" name="note_20" id="note_20" class="form-control form-control-sm note-input" placeholder="20" data-denomination="20000"/>
                            </div>
                            <div class="col-4 col-md-2">
                                <input type="number" name="note_10" id="note_10" class="form-control form-control-sm note-input" placeholder="10" data-denomination="10000"/>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-4 col-md-2">
                                <input type="number" name="note_5" id="note_5" class="form-control form-control-sm note-input" placeholder="5" data-denomination="5000"/>
                            </div>
                            <div class="col-4 col-md-2">
                                <input type="number" name="note_2" id="note_2" class="form-control form-control-sm note-input" placeholder="2" data-denomination="2000"/>
                            </div>
                            <div class="col-4 col-md-2">
                                <input type="number" name="note_1" id="note_1" class="form-control form-control-sm note-input" placeholder="1" data-denomination="1000"/>
                            </div>
                        </div>
                        <!-- Note inputs end -->

                        <div class="row mt-3">
                            <div class="col">
                                <textarea name="notes" id="notes" rows="1" class="form-control" placeholder="Ghi chú"></textarea>
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

                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="addTransactionBtn">Thu</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showAddTransactionModal(transaction) {
        console.log(transaction);
        $('#staff_id').val(transaction.staff_id);//lựa chọn NV
        $('#total_amount').text(transaction.total_amount ? transaction.total_amount.toLocaleString() : 0);
        $('#diff_amount').text(transaction.diff_amount ? transaction.diff_amount.toLocaleString() : 0);
        // Logic để hiển thị modal và điền dữ liệu vào form
        $('#transaction_id').val(transaction.id);
        $('#addTransactionModal').modal('show');
        
    }

    $('#addTransactionBtn').click(function() {
        var formData = $('#addTransactionForm').serialize();
        console.log(formData);
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        $.ajax({
            url: 'add-transaction-detail',
            type: 'POST',
            data: formData,
            success: function(response) {
                console.log(response);
                $('#addTransactionModal').modal('hide');
                alert('Thêm giao dịch thành công!');
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi thêm giao dịch.');
            }
        });
    });


    $(document).ready(function() {
    function fetchData(url) {
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                $('#transactionsTable tbody').html(response.table);
                $('#pagination-links').html(response.links);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    // Gọi hàm fetchData khi trang được tải để tải dữ liệu ban đầu
    fetchData('{{ route('transactions') }}');

    // Xử lý form tìm kiếm
    let currentSearchParams = "";
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        currentSearchParams = $(this).serialize(); // Lưu trữ các tham số tìm kiếm
        fetchData('{{ route('transactions') }}?' + currentSearchParams);
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
    $('#transactionsTable').on('click', '.expand-button', function() {
        var targetId = $(this).data('target');
        $(targetId).toggle();
        // Thay đổi nút từ "+" sang "-" và ngược lại
        $(this).text($(this).text() === '+' ? '-' : '+');
    });

    $('#showSummaryBtn').click(function() {
        var tableContent = buildTableContent();// Khởi tạo và bắt đầu nội dung HTML của bảng
        $('#tableContainer').html(tableContent);
        $('#summaryModal').modal('show');
        $('#checkAll').prop('checked', false);//bỏ checkall
    });

    function buildTableContent() {
        var totalTongtien = 0;
        var totalChenhLech = 0;

        var tableContent = `
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ngày BC</th>
                        <th>NVBH</th>
                        <th>Tổng tiền</th>
                        <th>Chênh lệch</th>
                    </tr>
                </thead>
                <tbody>`;

        // Duyệt qua mỗi hàng có checkbox được tích
        $(".order-checkbox:checked").each(function(index) {
            var transactionId = $(this).data('id');
            var row = $(this).closest("tr");
            var tongTien = parseInt(row.find("td:eq(5)").text().replace(/,/g, '')) || 0;
            var chenhLech = parseInt(row.find("td:eq(6)").text().replace(/,/g, '')) || 0;

            totalTongtien += tongTien;
            totalChenhLech += chenhLech;

            tableContent += `
                <tr>
                    <td style="display: none;" data-id="${transactionId}"></td>    
                    <td>${index + 1}</td>
                    <td>${row.find("td:eq(3)").text()}</td>
                    <td>${row.find("td:eq(4)").text()}</td>
                    <td>${tongTien.toLocaleString()}</td>
                    <td>${chenhLech.toLocaleString()}</td>
                </tr>`;
        });

        // Đóng thẻ tbody và thêm hàng tổng số
        tableContent += `
                <tr>
                    <td colspan="3"><strong>Tổng</strong></td>
                    <td><strong>${totalTongtien.toLocaleString()}</strong></td>
                    <td><strong>${totalChenhLech.toLocaleString()}</strong></td>
                </tr>
                </tbody>
            </table>`;

        return tableContent;
    }

    function updateTotals() {
        var totalNotes = 0;
        var transferAmount = parseInt($('#transfer_amount').val()) || 0;

        $('#addTransactionForm input.note-input').each(function() {
            var denomination = $(this).data('denomination');
            var quantity = parseInt($(this).val(), 10) || 0;
            totalNotes += denomination * quantity;
        });

        var combinedTotal = transferAmount + totalNotes;

        $('#transferTotal').text(transferAmount.toLocaleString());
        $('#notesTotal').text(totalNotes.toLocaleString());
        $('#combinedTotal').text(combinedTotal.toLocaleString());

        // Cập nhật giá trị vào các trường ẩn
        $('#hiddenTransferTotal').val(transferAmount);
        $('#hiddenCombinedTotal').val(combinedTotal);
    }

    // Gắn sự kiện 'input' vào tất cả các input trong form để cập nhật tổng số tiền mỗi khi giá trị thay đổi
    $('#addTransactionForm input').on('input', updateTotals);


});

</script>
@endpush
