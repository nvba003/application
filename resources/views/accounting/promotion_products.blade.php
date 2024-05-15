@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Danh sách sản phẩm khuyến mãi</h2>
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addNewModal">Thêm Mới</button>
    </div>

    <div class="overflow-x-auto bg-white shadow-md rounded my-6">
        <table class="table-fixed text-left w-full border-collapse" id="promotionTable">
            <thead>
                <tr>
                    <th class="w-1/12 px-2 py-3 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">ID</th>    
                    <th class="w-1/12 px-3 py-3 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">Mã SAP</th>
                    <th class="w-4/12 px-4 py-3 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">Tên sản phẩm</th>
                    <th class="w-1/12 px-3 py-3 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">Nhóm</th>
                    <th class="w-2/12 px-4 py-3 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">Tên CTKM</th>
                    <th class="w-1/12 px-4 py-3 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">P.Bản</th>
                    <th class="w-2/12 px-4 py-3 bg-grey-lightest font-bold uppercase text-sm text-grey-dark border-b border-grey-light">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @include('accounting.partials.promotion_products_table', ['promotions' => $promotions])
            </tbody>
        </table>

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

<!-- Modal thêm mới-->
<div class="modal fade" id="addNewModal" tabindex="-1" aria-labelledby="addNewModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addNewModalLabel">Thêm Mới Sản Phẩm Khuyến Mãi</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Form thêm mới -->
        <form id="addForm">
          <div class="mb-3">
            <label for="newSapCode" class="form-label">Mã SAP</label>
            <input type="text" class="form-control" id="newSapCode" name="sap_code">
          </div>
          <div class="mb-3">
            <label for="newProductName" class="form-label">Tên sản phẩm</label>
            <input type="text" class="form-control" id="newProductName" name="product_name">
          </div>
          <div class="mb-3">
            <label for="newGroup" class="form-label">Nhóm</label>
            <input type="text" class="form-control" id="newGroup" name="group_promotion_id">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary" onclick="submitNewProduct()">Thêm</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal chỉnh sửa-->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Chỉnh sửa sản phẩm khuyến mãi</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Form chỉnh sửa -->
        <form id="editForm">
          <input type="hidden" id="edit-id">
          <div class="mb-3">
            <label for="editSapCode" class="form-label">Mã SAP</label>
            <input type="text" class="form-control" id="editSapCode" name="sap_code">
          </div>
          <div class="mb-3">
            <label for="editProductName" class="form-label">Tên sản phẩm</label>
            <input type="text" class="form-control" id="editProductName" name="product_name">
          </div>
          <div class="mb-3">
            <label for="editGroup" class="form-label">Nhóm</label>
            <input type="text" class="form-control" id="editGroup" name="group_promotion_id">
          </div>
          <div class="mb-3">
            <label for="editParent" class="form-label">Phiên bản</label>
            <input type="text" class="form-control" id="editParent" name="parent_id">
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
function confirmDelete(productId) {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')) {
        document.getElementById('delete-form-' + productId).submit();
    }
}
function submitNewProduct() {
    const data = {
        sap_code: $('#newSapCode').val(),
        product_name: $('#newProductName').val(),
        group_promotion_id: $('#newGroup').val(),
        _token: '{{ csrf_token() }}' // CSRF token để xác thực Laravel
    };

    $.ajax({
        url: '{{ route('promotion_products.create') }}',
        type: 'POST',
        data: data,
        success: function(response) {
            notify500();
            $('#addNewModal').modal('hide');
            setTimeout(function() {
                location.reload();
            }, 500);
        },
        error: function(error) {
            console.log('Error:', error);
        }
    });
}
function notify500(){
    $('#successModal').modal('show');
    setTimeout(function() {
        $('#successModal').modal('hide');
    }, 500);
}

$(document).ready(function() {
    // Xử lý form tìm kiếm
    let currentSearchParams = "";
    let currentPerPage = "";
    let perPage = $('#perPage').val();
    var promotions = @json($promotions)['data'];
    console.log(promotions);
    var products = @json($products);
    console.log(products);
    let sapCodes = products.map(product => {
        return {
            label: product.sap_code + " - " + product.product_name,
            value: product.sap_code
        };
    });
    $("#newSapCode").autocomplete({
        source: sapCodes,
        select: function(event, ui) {
            $('#newSapCode').val(ui.item.value); // Thiết lập giá trị cho mã SAP
            $('#newProductName').val(ui.item.label.split(" - ")[1]); // Cập nhật tên sản phẩm tương ứng
            return false; // Ngăn không cho autocomplete thay đổi giá trị của input
        }
    });
    $("#editSapCode").autocomplete({
        source: sapCodes,
        select: function(event, ui) {
            $('#editSapCode').val(ui.item.value); // Thiết lập giá trị cho mã SAP
            $('#editProductName').val(ui.item.label.split(" - ")[1]); // Cập nhật tên sản phẩm tương ứng
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

    fetchData('{{ route('promotionProducts') }}');

    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        perPage = $('#perPage').val();
        currentSearchParams = updateSearchParams('per_page', perPage, $(this).serialize());
        fetchData('{{ route('promotionProducts') }}?' + currentSearchParams);
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
        fetchData('{{ route('promotionProducts') }}?' + currentSearchParams);
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

    function updateCount() {
        var count = $('.checkItem:checked').length;
        $('#selectedCount').text(count);
    }
    $(document).on('click', '.checkItem', function() {
        updateCount();
    });
    updateCount();  

    $('#promotionTable').on('click', '.btn-edit', function() {
        var product = $(this).data('product');
        openEditForm(product);
    });

    function openEditForm(product) {
        $('#edit-id').val(product.id);
        $('#editSapCode').val(product.sap_code);
        $('#editProductName').val(product.product_name);
        $('#editGroup').val(product.group_promotion_id);
        $('#editParent').val(product.parent_id);
        // Hiển thị modal
        $('#editModal').modal('show');
    }

    $('#saveChanges').click(function() {
        const editedData = {
            id: $('#edit-id').val(),
            sap_code: $('#editSapCode').val(),
            product_name: $('#editProductName').val(),
            group_promotion_id: $('#editGroup').val(),
            parent_id: $('#editParent').val(),
        };
        console.log(editedData);
        $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        $.ajax({
            url: 'update-promotion-products',
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


});

</script>
@endpush
