@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="container mx-auto px-4 py-4">
    <form id="searchForm" method="GET" class="flex flex-wrap items-center gap-4 mb-2">
        <div class="form-group">
            <input type="text" class="form-control border border-gray-300 rounded-md p-2" id="promotion_name" name="promotion_name" placeholder="Tên Khuyến Mãi">
        </div>
        <div class="form-group">
            <select id="promotion_type" name="promotion_type" class="form-control border border-gray-300 rounded-md p-2">
                <option value="">Chọn loại khuyến mãi</option>
                <option value="Chiết khấu">Chiết khấu</option>
                <option value="Sản phẩm">Sản phẩm</option>
            </select>
        </div>
        <div class="form-group">
            <select id="status" name="status" class="form-control border border-gray-300 rounded-md p-2">
                <option value="">Chọn trạng thái</option>
                <option value="Hoạt động">Hoạt động</option>
                <option value="Tạm ngưng">Tạm ngưng</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded mb-3">Tìm Kiếm</button>
    </form>

    <div class="flex flex-col">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="py-2 inline-block min-w-full sm:px-6 lg:px-8">
                <div class="overflow-hidden">
                    <table class="min-w-full bg-white" id="promotionTable">
                        <thead class="border-b bg-gray-800 text-white">
                            <tr>
                                <th scope="col" class="text-sm font-medium px-1 py-1">
                                </th>
                                <th scope="col" class="text-sm font-medium px-2 py-2">
                                    Nhóm
                                </th>
                                <th scope="col" class="text-sm font-medium px-2 py-2">
                                    Serial
                                </th>
                                <th scope="col" class="text-sm font-medium px-2 py-2">
                                    Tên Khuyến Mãi
                                </th>
                                <th scope="col" class="text-sm font-medium px-2 py-2">
                                    Loại
                                </th>
                                <th scope="col" class="text-sm font-medium px-2 py-2">
                                    SL từ
                                </th>
                                <th scope="col" class="text-sm font-medium px-2 py-2">
                                    Số tiền từ
                                </th>
                                <th scope="col" class="text-sm font-medium px-2 py-2">
                                    % Giảm
                                </th>
                                <th scope="col" class="text-sm font-medium px-2 py-2">
                                    Sản Phẩm Tặng
                                </th>
                                <th scope="col" class="text-sm font-medium px-2 py-2">
                                    SL Tặng
                                </th>
                                <th scope="col" class="text-sm font-medium px-2 py-2">
                                    TL Tặng
                                </th>
                                <th scope="col" class="text-sm font-medium px-2 py-2">
                                    Trạng Thái
                                </th>
                                <th scope="col" class="text-sm font-medium px-2 py-2">

                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @include('accounting.partials.promotions_table', ['promotions' => $promotions])
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-10 gap-4 m-2">
            <div class="col-span-8" id="pagination-links">
                <!-- Pagination links here -->
            </div>
            <div class="col-span-2">
                <div class="flex items-center space-x-2 w-full">
                    <label for="perPage" class="text-sm flex-1 text-right pr-2">Số hàng:</label>
                    <select id="perPage" class="form-control form-control-sm text-sm flex-1">
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal chỉnh sửa-->
<div class="modal fade" id="editPromotionModal" tabindex="-1" aria-labelledby="editPromotionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editPromotionModalLabel">Chỉnh sửa Khuyến Mãi</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <form id="editPromotionForm">
        <input type="hidden" id="edit-id">
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label for="groupPromotionId" class="form-label">Nhóm</label>
                    <input type="number" class="form-control" id="groupPromotionId" name="group_promotion_id" readonly>
                </div>
                <div class="mb-3">
                    <label for="promotionSerial" class="form-label">Serial</label>
                    <input type="number" class="form-control" id="promotionSerial" name="promotion_serial">
                </div>
                <div class="mb-3">
                    <label for="promotionName" class="form-label">Tên Chương Trình</label>
                    <input type="text" class="form-control" id="promotionName" name="promotion_name">
                </div>
                <div class="mb-3">
                    <label for="promotionType" class="form-label">Loại Khuyến Mãi</label>
                    <select class="form-control" id="promotionType" name="promotion_type">
                        <option value="Chiết khấu">Chiết khấu</option>
                        <option value="Sản phẩm">Sản phẩm</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="promotionStatus" class="form-label">Trạng thái</label>
                    <select class="form-control" id="promotionStatus" name="promotion_status">
                        <option value="Hoạt động">Hoạt động</option>
                        <option value="Tạm ngưng">Tạm ngưng</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="colorCode" class="form-label">Mã màu</label>
                    <input type="text" class="form-control" id="colorCode" name="color_code">
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label for="minimumQuantity" class="form-label">Số lượng tối thiểu</label>
                    <input type="number" class="form-control" id="minimumQuantity" name="minimum_quantity">
                </div>
                <div class="mb-3">
                    <label for="minimumAmount" class="form-label">Số tiền tối thiểu</label>
                    <input type="number" class="form-control" id="minimumAmount" name="minimum_amount">
                </div>
                <div class="mb-3">
                    <label for="discountPercentage" class="form-label">% Giảm giá</label>
                    <input type="number" class="form-control" id="discountPercentage" name="discount_percentage" step="0.01">
                </div>
                <div class="mb-3">
                    <label for="bonusProductId" class="form-label">Sản phẩm tặng</label>
                    <input type="text" class="form-control" id="bonusProductId" name="bonus_product_id">
                </div>
                <div class="mb-3">
                    <label for="bonusQuantity" class="form-label">Số lượng tặng</label>
                    <input type="number" class="form-control" id="bonusQuantity" name="bonus_quantity">
                </div>
                <div class="mb-3">
                    <label for="bonusRatio" class="form-label">Tỉ lệ tặng</label>
                    <input type="number" class="form-control" id="bonusRatio" name="bonus_ratio">
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label for="startDate" class="form-label">Ngày bắt đầu</label>
                    <input type="date" class="form-control" id="startDate" name="start_date">
                </div>
                <div class="mb-3">
                    <label for="endDate" class="form-label">Ngày kết thúc</label>
                    <input type="date" class="form-control" id="endDate" name="end_date">
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả</label>
                    <textarea class="form-control" id="description" name="description"></textarea>
                </div>
            </div>
        </div>
      </form>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary" id="saveChanges">Lưu thay đổi</button>
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
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
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
    var promotions = @json($promotions)['data'];
    console.log(promotions);
    var products = @json($products);
    //console.log(products);
    let sapCodes = products.map(product => {
        return {
            label: product.sap_code + " - " + product.product_name,
            value: product.sap_code
        };
    });
    $("#bonusProductId").autocomplete({
        source: sapCodes,
        select: function(event, ui) {
            $('#bonusProductId').val(ui.item.value); // Thiết lập giá trị cho mã SAP
            return false; // Ngăn không cho autocomplete thay đổi giá trị của input
        }
    });

    function fetchData(url) {
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                $('#promotionTable tbody').html(response.table);
                $('#pagination-links').html(response.links);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    fetchData('{{ route('promotions') }}');

    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        perPage = $('#perPage').val();
        currentSearchParams = updateSearchParams('per_page', perPage, $(this).serialize());
        fetchData('{{ route('promotions') }}?' + currentSearchParams);
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
        perPage = $(this).val();
        currentSearchParams = updateSearchParams('per_page', perPage, currentSearchParams);
        fetchData('{{ route('promotions') }}?' + currentSearchParams);
    });
    function updateSearchParams(key, value, paramsString) {
        var searchParams = new URLSearchParams(paramsString);
        searchParams.set(key, value);
        return searchParams.toString();
    }

    $('#promotionTable').on('click', '.expand-button', function() {
        var targetId = $(this).data('target');
        $(targetId).toggle();
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

    $('#promotionTable').on('click', '.btn-edit', function() {
        var promotion = $(this).data('promotion');
        openEditForm(promotion);
    });
    function openEditForm(promotion) {
        // Điền dữ liệu cơ bản
        $('#edit-id').val(promotion.id);
        $('#groupPromotionId').val(promotion.group_promotion_id);
        $('#promotionSerial').val(promotion.promotion_serial);
        $('#promotionName').val(promotion.promotion_group.promotion_name);
        $('#promotionType').val(promotion.promotion_type);
        $('#promotionStatus').val(promotion.promotion_group.status);
        $('#colorCode').val(promotion.promotion_group.color_code);
        // Điền dữ liệu số lượng và số tiền
        $('#minimumQuantity').val(promotion.minimum_quantity);
        $('#minimumAmount').val(promotion.minimum_amount);
        $('#discountPercentage').val(promotion.discount_percentage);
        $('#bonusProductId').val(promotion.bonus_product_id);
        $('#bonusQuantity').val(promotion.bonus_quantity);
        $('#bonusRatio').val(promotion.bonus_ratio);
        // Điền dữ liệu ngày tháng
        $('#startDate').val(promotion.promotion_group.start_date);
        $('#endDate').val(promotion.promotion_group.end_date);
        // Điền mô tả
        $('#description').val(promotion.description);
        // Hiển thị modal
        $('#editPromotionModal').modal('show');
    }

    $('#saveChanges').click(function() {
        const editedData = {
            id: $('#edit-id').val(),
            group_promotion_id: $('#groupPromotionId').val(),
            promotion_serial: $('#promotionSerial').val(),
            promotion_name: $('#promotionName').val(),
            promotion_type: $('#promotionType').val(),
            promotion_status: $('#promotionStatus').val(),
            color_code: $('#colorCode').val(),
            minimum_quantity: $('#minimumQuantity').val(),
            minimum_amount: $('#minimumAmount').val(),
            discount_percentage: $('#discountPercentage').val(),
            bonus_product_id: $('#bonusProductId').val(),
            bonus_quantity: $('#bonusQuantity').val(),
            bonus_ratio: $('#bonusRatio').val(),
            start_date: $('#startDate').val(),
            end_date: $('#endDate').val(),
            description: $('#description').val()
        };
        console.log(editedData);
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        $.ajax({
            url: 'update-promotions',
            method: 'PUT',
            data: editedData,
            success: function(response) {
                notify500();
                $('#editPromotionModal').modal('hide');
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

});
</script>
@endpush
