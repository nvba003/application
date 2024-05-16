@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mt-2 mb-3 flex gap-4">
        <form id="searchForm" class="flex flex-wrap gap-4">
            <div class="flex-1">
                <label for="report_date" class="block text-sm font-medium text-gray-700">Ngày Báo Cáo</label>
                <input type="date" id="report_date" name="report_date" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="flex-1">
                <label for="staff" class="block text-sm font-medium text-gray-700">Nhân Viên</label>
                <select id="staff" name="staff" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Chọn nhân viên</option>
                    @foreach($saleStaffs as $staff)
                        <option value="{{ $staff->name }}">{{ $staff->name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <!-- <button id="showSummaryBtn" type="button" class="px-4 py-2 bg-yellow-500 text-white font-medium rounded-md hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50">Xem</button> -->
                <button id="showSummaryOrder" type="button" class="px-4 py-2 bg-yellow-500 text-white font-medium rounded-md hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-opacity-50">Xem tổng hợp</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white font-medium rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Tìm Kiếm</button>
            </div>
        </form>
    </div>
    <div>Đã chọn: <span class="badge badge-primary" id="selectedCount">0</span> hàng</div>
    <div class="bg-white shadow-md rounded my-1 overflow-x-auto">
        <table class="min-w-full leading-normal" id="promotionTable">
            <thead class="text-white bg-green-400">
                <tr>
                    <th class="px-2 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase tracking-wider">
                        <input type="checkbox" id="checkAll">
                    </th>
                    <th class="px-2 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase tracking-wider"></th>
                    <th class="px-3 py-3 border-b-2 border-gray-200 text-center text-sm font-semibold uppercase tracking-wider">
                        Ngày báo cáo
                    </th>
                    <th class="px-3 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase tracking-wider">
                        NVBH
                    </th>
                    <th class="px-3 py-3 border-b-2 border-gray-200 text-right text-sm font-semibold uppercase tracking-wider">
                        Chiết khấu
                    </th>
                    <th class="px-3 py-3 border-b-2 border-gray-200 text-right text-sm font-semibold uppercase tracking-wider">
                        Thanh toán
                    </th>
                    <th class="px-3 py-3 border-b-2 border-gray-200 text-left text-sm font-semibold uppercase tracking-wider">
                        Thao tác
                    </th>
                </tr>
            </thead>
            <tbody>
                @include('accounting.partials.order_temporary_table', ['orders' => $orders])
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
                    <option value="20">20</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    </div>
</div>
 <!-- Modal xem đơn-->
 <div class="modal fade" id="summaryModal" tabindex="-1" role="dialog" aria-labelledby="summaryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="summaryModalLabel">Tóm tắt đơn hàng</h5>
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
<!-- Modal xem tổng hợp đơn-->
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
          <thead class="bg-blue-400">
            <tr>
              <th scope="col" class="px-2 py-3 text-xs font-bold text-gray-900 uppercase tracking-wider">STT</th>
              <th scope="col" class="px-2 py-3 text-xs font-bold text-gray-900 uppercase tracking-wider">Mã SAP</th>
              <th scope="col" class="px-2 py-3 text-xs font-bold text-gray-900 uppercase tracking-wider">Tên sản phẩm</th>
              <th scope="col" class="px-2 py-3 text-right text-xs font-bold text-gray-900 uppercase tracking-wider">Đơn giá</th>
              <th scope="col" class="px-2 py-3 text-right text-xs font-bold text-gray-900 uppercase tracking-wider">Số lượng</th>
              <th scope="col" class="px-2 py-3 text-right text-xs font-bold text-gray-900 uppercase tracking-wider">T.Tiền</th>
              <th scope="col" class="px-2 py-3 text-right text-xs font-bold text-gray-900 uppercase tracking-wider">C.Khấu</th>
              <th scope="col" class="px-2 py-3 text-right text-xs font-bold text-gray-900 uppercase tracking-wider">T.Toán</th>
            </tr>
          </thead>
          <tbody id="productSummaryDetails">
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
@endsection

@push('scripts')
<script>
function removeOrder(id) {
    if (confirm('Bạn chắc muốn xóa?')) {
        const url = `temporary/${id}`;
        fetch(url, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json()) // Parsing the JSON response
        .then(data => {
            console.log(data); // Handling the response data
            if (data.message) {
                alert(data.message); // Optionally display a message to the user
                setTimeout(function() {
                    location.reload();
                }, 500);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi chưa xóa được.'); // Handling any errors
        });
    } else {
        console.log('Deletion cancelled by user.');
    }
}

$(document).ready(function() {
    // Xử lý form tìm kiếm
    let currentSearchParams = "";
    let currentPerPage = "";
    let perPage = $('#perPage').val();
    var orders = @json($orders)['data'];
    console.log(orders);
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

    fetchData('{{ route('orderTemporary') }}');

    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        perPage = $('#perPage').val();
        currentSearchParams = updateSearchParams('per_page', perPage, $(this).serialize());
        fetchData('{{ route('orderTemporary') }}?' + currentSearchParams);
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
        fetchData('{{ route('orderTemporary') }}?' + currentSearchParams);
    });
    function updateSearchParams(key, value, paramsString) {
        var searchParams = new URLSearchParams(paramsString);
        searchParams.set(key, value);
        return searchParams.toString();
    }

    $('#promotionTable').on('click', '.expand-button', function() {
        var targetId = $(this).data('target');
        $(targetId).toggle();
        // Thay đổi nút từ "+" sang "-" và ngược lại
        $(this).text($(this).text() === '+' ? '-' : '+');
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

    $('#showSummaryOrder').click(function() {
        let selectedOrders = [];
        $(".order-checkbox:checked").each(function(index) {
        let orderId = $(this).data('id');
        // Lấy dữ liệu từ summaryOrders dựa trên id được lựa chọn
        selectedOrders.push(orders.find(order => order.id === orderId));
        });

        // Gộp và hiển thị dữ liệu trong modal
        let products = {};
        let productSpecials = {};
        let totalDiscount = 0;
        let totalPayable = 0;
        let totalQuantity = 0;
        let stt = 0; // Biến đếm cho số thứ tự
        selectedOrders.forEach(order => {
            order.details.forEach(detail => {
                if (detail.is_gift == 0) { // Bỏ qua sản phẩm KM
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
                }
                else{ // trường hợp đặt biệt là khuyến mãi, mỗi SP chỉ có 1 hàng
                    const special_key = detail.product_code;
                    if (!productSpecials[special_key]) {
                        productSpecials[special_key] = {
                            product_code: detail.product_code,
                            product_name: detail.product_name,
                            quantity: 0,
                        };
                    }
                    productSpecials[special_key].quantity += detail.quantity;
                    totalQuantity += detail.quantity;
                }

            });
        });
        // Hiển thị kết quả trong modal
        const productDetails = $('#productSummaryDetails');
        productDetails.empty(); // Xóa các hàng hiện có
        Object.values(products).forEach(product => {
            stt++;
            let row = `<tr class="even:bg-gray-200 odd:bg-white">
                <td class="w-1/12 px-3 py-2 text-sm border-b border-grey-light">${stt}</td>
                <td class="w-2/12 px-3 py-2 text-sm border-b border-grey-light">${product.product_code}</td>
                <td class="w-4/12 px-3 py-2 text-sm border-b border-grey-light">${product.product_name}</td>
                <td class="w-1/12 px-3 py-2 text-sm border-b border-grey-light text-right">${product.price.toLocaleString()}</td>
                <td class="w-1/12 px-3 py-2 text-sm border-b border-grey-light text-right">${product.quantity}</td>
                <td class="w-1/12 px-3 py-2 text-sm border-b border-grey-light text-right">${product.subtotal.toLocaleString()}</td>
                <td class="w-1/12 px-3 py-2 text-sm border-b border-grey-light text-right">${product.totalDiscount.toLocaleString()}</td>
                <td class="w-1/12 px-3 py-2 text-sm border-b border-grey-light text-right">${product.totalPrice.toLocaleString()}</td>
            </tr>`;
            productDetails.append(row);
        });
        if (Object.keys(productSpecials).length > 0) {
            Object.values(productSpecials).forEach((product, index) => {
                let row = `
                    <tr class="bg-gray-100">
                        <td class="w-1/12 px-3 py-2 text-sm border-b border-grey-light">${index + 1}</td>    
                        <td class="w-2/12 px-3 py-2 text-sm border-b border-grey-light">${product.product_code}</td>
                        <td class="w-4/12 px-3 py-2 text-sm border-b border-grey-light">${product.product_name}</td>
                        <td></td>  
                        <td class="w-1/12 px-3 py-2 text-sm border-b border-grey-light text-right">${product.quantity}</td>
                        <td></td> 
                        <td></td> 
                        <td></td>
                    </tr>`;
                productDetails.append(row);
            });
        }
        // Thêm hàng tổng kết
        let totalRow = `<tr class="bg-blue-200">
        <td colspan="5" class="text-sm"><strong>Tổng cộng</strong></td>
        <td class="text-sm text-right"><strong>${totalQuantity.toLocaleString()}</strong></td>
        <td class="text-sm text-right"><strong>${totalDiscount.toLocaleString()}</strong></td>
        <td class="text-sm text-right"><strong>${totalPayable.toLocaleString()}</strong></td>
        </tr>`;
        productDetails.append(totalRow);

        $('#productModal').modal('show'); // Hiển thị modal
    });


});
</script>
@endpush