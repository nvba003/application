@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="container">
    <h3>Chi tiết thanh toán</h3>
    <div class="filter-section">
        <form id="searchForm">
            <div class="row">
                <!-- NV phụ trách -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="staff" class="mr-2">NV phụ trách:</label>
                        <select id="staff" name="staff" class="form-control">
                            <option value="">Chọn nhân viên</option>
                            @foreach($saleStaffs as $staff)
                                <option value="{{ $staff->name }}">{{ $staff->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Ngày báo cáo -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="pay_date" class="mr-2">Ngày báo cáo:</label>
                        <input type="date" id="pay_date" name="pay_date" class="form-control">
                    </div>
                </div>

                <!-- Chênh lệch -->
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="difference" class="mr-2">Chênh lệch:</label>
                        <select id="difference" name="difference_amount" class="form-control">
                            <option value="">Chọn</option>
                            <option value="0">Nợ chưa trả</option>
                            <option value="1">Nợ trên 1k</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- Các nút -->
            <div class="d-flex">
                <button id="showSummaryBtn" class="btn btn-warning mr-2">Xem</button>
                <button type="submit" class="btn btn-primary">Tìm Kiếm</button>
            </div>
        </form>
    </div>



    <div class="transactions-section mt-4">
        <table class="table" id="transactionsTable">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="checkAll">
                    </th>  
                    <th>STT</th>
                    <th>Ngày BC</th>
                    <th>NV phụ trách</th>
                    <th>Số GD</th>
                    <th>Chuyển Khoản</th>
                    <th>Tiền Mặt</th>
                    <th>Tổng Số Tiền</th>
                    <th>Ghi Chú</th>
                </tr>
            </thead>
            <tbody>
                @include('accounting.partials.transaction_details_table', ['transactions' => $transactions])
            </tbody>
        </table>
        <div id="pagination-links" class="mt-3">

        </div>
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

</div>
@endsection

@push('scripts')
<script>
    function notify500(){
        $('#successModal').modal('show');
        setTimeout(function() {
            $('#successModal').modal('hide');
        }, 1000);
    }

$(document).ready(function() {
    let currentSearchParams = "";
    let currentPerPage = "";
    let perPage = $('#perPage').val();
    var transactions = @json($transactions)['data'];
    console.log(transactions);

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
    fetchData('{{ route('transactionDetails') }}');

    // Xử lý form tìm kiếm
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        perPage = $('#perPage').val();
        //currentSearchParams = $(this).serialize(); // Lưu trữ các tham số tìm kiếm
        currentSearchParams = updateSearchParams('per_page', perPage, $(this).serialize());
        fetchData('{{ route('transactionDetails') }}?' + currentSearchParams);
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
        fetchData('{{ route('transactionDetails') }}?' + currentSearchParams);
    });
    function updateSearchParams(key, value, paramsString) {
        var searchParams = new URLSearchParams(paramsString);
        searchParams.set(key, value);
        return searchParams.toString();
    }

    $('#showSummaryBtn').click(function() {
        var tableContent = buildTableContent();// Khởi tạo và bắt đầu nội dung HTML của bảng
        $('#tableContainer').html(tableContent);
        $('#summaryModal').modal('show');
        $('#checkAll').prop('checked', false);//bỏ checkall
    });

    function buildTableContent() {
        var totalTransfer = 0;
        var totalCash = 0;
        var totalAmount = 0;

        var tableContent = `
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th style="display: none;">Ngày BC</th>
                        <th>NV</th>
                        <th>CK</th>
                        <th>TM</th>
                        <th>Tổng tiền</th>
                    </tr>
                </thead>
                <tbody>`;

        // Duyệt qua mỗi hàng có checkbox được tích
        $(".order-checkbox:checked").each(function(index) {
            var transactionId = $(this).data('id');
            var row = $(this).closest("tr");
            var transfer = parseInt(row.find("td:eq(5)").text().replace(/,/g, '')) || 0;
            var cash = parseInt(row.find("td:eq(6)").text().replace(/,/g, '')) || 0;
            var total = parseInt(row.find("td:eq(7)").text().replace(/,/g, '')) || 0;
            console.log(transfer);
            console.log(cash);

            totalTransfer += transfer;
            totalCash += cash;
            totalAmount += total;

            tableContent += `
                <tr>
                    <td style="display: none;" data-id="${transactionId}"></td>    
                    <td>${index + 1}</td>
                    <td>${row.find("td:eq(2)").text()}</td>
                    <td>${row.find("td:eq(3)").text()}</td>
                    <td class="text-right">${transfer.toLocaleString()}</td>
                    <td class="text-right">${cash.toLocaleString()}</td>
                    <td class="text-right">${total.toLocaleString()}</td>
                </tr>`;
        });

        // Đóng thẻ tbody và thêm hàng tổng số
        tableContent += `
                <tr>
                    <td colspan="3"><strong>Tổng</strong></td>
                    <td class="text-right"><strong>${totalTransfer.toLocaleString()}</strong></td>
                    <td class="text-right"><strong>${totalCash.toLocaleString()}</strong></td>
                    <td class="text-right"><strong>${totalAmount.toLocaleString()}</strong></td>
                </tr>
                </tbody>
            </table>`;

        return tableContent;
    }


});

</script>
@endpush
