@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Lịch Sử Giao Dịch</h3>
    <div class="filter-section">
        <form id="searchForm">
            <div class="form-group">
                <label for="staff">Nhân Viên:</label>
                <select id="staff" name="staff" class="form-control">
                    <option value="">Chọn nhân viên</option>
                    @foreach($saleStaffs as $staff)
                        <option value="{{ $staff->name }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="submitter">Người Nộp:</label>
                <select id="submitter" name="submitter" class="form-control">
                    <option value="">Chọn Người Nộp</option>
                    @foreach($saleStaffs as $staff)
                        <option value="{{ $staff->name }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="submit_date">Ngày Nộp:</label>
                <input type="date" id="submit_date" name="submit_date" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Tìm Kiếm</button>
        </form>
    </div>

    <div class="transactions-section mt-4">
        <table class="table" id="transactionsTable">
            <thead>
                <tr>
                    <th></th>    
                    <th>STT</th>
                    <th>Ngày Nộp</th>
                    <th>Nhân Viên</th>
                    <th>Người Nộp</th>
                    <th>Chuyển Khoản</th>
                    <th>Tổng Số Tiền</th>
                    <th>Số giao dịch</th>
                </tr>
            </thead>
            <tbody>
                @include('accounting.partials.transactions_table', ['transactions' => $transactions])
            </tbody>
        </table>
        <div id="pagination-links" class="mt-3">
            {{-- Links phân trang cũng sẽ được thêm vào đây bằng AJAX --}}
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

    // Xử lý nút mở rộng để hiển thị chi tiết đơn hàng
    $('#transactionsTable').on('click', '.expand-button', function() {
        var targetId = $(this).data('target');
        $(targetId).toggle();
        // Thay đổi nút từ "+" sang "-" và ngược lại
        $(this).text($(this).text() === '+' ? '-' : '+');
    });


});

</script>
@endpush
