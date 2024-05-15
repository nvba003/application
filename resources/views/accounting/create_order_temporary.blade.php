@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="max-w-screen-xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mt-2 mb-3 flex gap-4">
        <div>
            <label for="productSearch" class="form-label text-sm font-bold">Tìm Kiếm Sản Phẩm</label>
            <input type="text" id="productSearch" class="form-control form-control-sm" placeholder="Nhập SAP code hoặc tên">
        </div>
        <div>
            <label for="orderSearch" class="form-label text-sm font-bold">Tìm Kiếm Đơn Hàng</label>
            <input type="text" id="orderSearch" class="form-control form-control-sm" placeholder="Nhập số phiếu tạm ứng">
        </div>
        <button id="searchButton" class="bg-blue-500 hover:bg-blue-700 text-white text-sm mt-4 py-1 px-2 rounded focus:outline-none focus:shadow-outline">Tìm Kiếm</button>
    </div>
    <form method="POST" id="orderForm">
    <div class="mt-2 mb-3 flex gap-4">
    <div class="flex flex-row items-right w-1/4">
        <div class="w-1/4 mr-1 mt-1">
            <label for="report_date" class="form-label text-sm">Ngày BC:</label>
        </div>
        <div class="w-3/4">
            <input type="date" id="report_date" name="report_date" class="form-control form-control-sm">
        </div>
    </div>
    <div class="flex flex-row items-right w-1/4">
        <div class="w-1/4 mr-1 mt-1">
            <label for="staff" class="form-label text-sm">NVBH:</label>
        </div>
        <div class="w-3/4">
            <select id="staff" name="staff" class="form-control form-control-sm" required>
                <option value="">Chọn nhân viên</option>
                @foreach($saleStaffs as $staff)
                    <option value="{{ $staff->name }}">{{ $staff->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

        @csrf
        <table class="min-w-full divide-y divide-gray-200" id="productList">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-2 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"># | KM</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mã SAP</th>    
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên Sản Phẩm</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thùng</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lẻ</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">S.Lượng</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">T.Tính</th>
                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% CK</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">C.Khấu</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">T.Toán</th>
                    <th scope="col" class="px-1 py-1 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- Các sản phẩm sẽ được thêm vào đây -->
            </tbody>
        </table>
        <div class="flex items-center justify-between mt-2">
            <button type="submit" class="bg-green-500 text-white font-bold py-2 px-4 rounded hover:bg-green-700 focus:outline-none focus:shadow-outline">Tạo Đơn Tạm Ứng</button>
            <div class="flex flex-grow items-center justify-end space-x-4">
                <div class="totals flex space-x-2">
                    <div class="total-quantity">
                        <label class="font-semibold">Tổng số lượng:</label>
                        <input type="hidden" id="inputTotalQuantity" name="totalQuantity" value="0">
                        <span id="totalQuantity">0</span>
                    </div>
                    <div class="total-discount">
                        <label class="font-semibold">Chiết khấu:</label>
                        <input type="hidden" id="inputTotalDiscount" name="totalDiscount" value="0">
                        <span id="totalDiscount">0</span>
                    </div>
                    <div class="total-payable">
                        <label class="font-semibold">Thanh toán:</label>
                        <input type="hidden" id="inputTotalPayable" name="totalPayable" value="0">
                        <span id="totalPayable">0</span>
                    </div>
                </div>
            </div>
        </div>

    </form>
@if(session('success'))
    <div class="alert alert-success" id="success-alert">
        {{ session('success') }}
    </div>
@endif
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
function notify500(){
        $('#successModal').modal('show');
        setTimeout(function() {
            $('#successModal').modal('hide');
        }, 500);
    }

function updateGiftValue(checkbox) {
    var hiddenInput = checkbox.previousElementSibling; // Truy cập input ẩn
    hiddenInput.value = checkbox.checked ? 1 : 0; // Cập nhật giá trị dựa trên trạng thái của checkbox
}

$(document).ready(function() {
    var today = new Date();
    //today.setHours(today.getHours() + 7);
    var yyyy = today.getFullYear();
    var mm = String(today.getMonth() + 1).padStart(2, '0');
    var dd = String(today.getDate()).padStart(2, '0');
    document.getElementById("report_date").value = yyyy + '-' + mm + '-' + dd;

    let products = @json($products);
    console.log(products);
    let promotions = @json($promotions);
    console.log(promotions);
    $('#productSearch').autocomplete({
        source: products.map(product => ({
            label: product.sap_code + " - " + product.product_name,
            value: product.sap_code
        })),
        select: function(event, ui) {
            addProductToOrder(ui.item.value);
            $('#productSearch').val(''); // Clear the search field
            return false;
        }
    });

    //$('#orderSearch').on('input', function() {
    $('#searchButton').on('click', function() {
        var orderSearchValue = $('#orderSearch').val();
        if (orderSearchValue.trim() !== '') {
            $.ajax({
                url: 'search-temporary',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                contentType: 'application/json',
                data: JSON.stringify({ temporary_code: orderSearchValue }),
                success: function(data) {
                    if (data.details.length === 0) {
                        alert("Đơn hàng không có chi tiết");
                    } else {
                        console.log(data);
                        $('#staff').val(data.staff);
                        $('#productList tbody').empty();
                        data.details.forEach(function(detail) {
                            addProductToOrderWithQty(detail);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    var errorMessage = xhr.status + ': ' + xhr.statusText;
                    // alert('Error - ' + errorMessage);
                    alert("Chưa có mã đơn này");
                }
            });
        } else {
            alert("Vui lòng nhập mã đơn hàng cần tìm.");
        }
    });

    function addProductToOrder(sapCode) {
        var product = promotions.find(p => p.sap_code === sapCode);
        let promotionDetails = '';
        if (product) { // nếu sản phẩm có khuyến mãi
            let promotionGroup = product.promotion_group;
            console.log(promotionGroup);
            // Bắt đầu một hàng mới
            promotionDetails += '<div class="flex flex-wrap">';
            // Lặp qua từng promotion trong promotionGroup
            promotionGroup.promotion.forEach(promo => {
                // Common details for each promotion
                let details = `
                    <div class="selectPromotion promotion-item p-2 border rounded-lg shadow my-2 mx-2 cursor-pointer" 
                        id="promo_${promo.id}" data-promotion-id="${promo.id}" data-product-id="${product.id}">
                        <strong>Tên:</strong> ${promotionGroup.promotion_name}
                        <br>
                        <strong>Loại KM:</strong> ${promo.promotion_type}
                        <br>
                        <strong>SL tối thiểu:</strong> ${promo.minimum_quantity || ''}
                        <br>
                        ${promo.minimum_amount ? `<strong>Số tiền tối thiểu:</strong> ${promo.minimum_amount}<br>` : ''}
                `;
                // Conditional details based on promotion type
                if (promo.promotion_type === "Sản phẩm") {
                    details += `
                        <strong>Sản phẩm tặng:</strong> ${promo.bonus_product_id || ''}
                        <br>
                        <strong>SL tặng cố định:</strong> ${promo.bonus_quantity || '0'}
                        <br>
                        <strong>Tỉ lệ tặng:</strong> ${promo.bonus_ratio || '0'}
                        <br>
                        <strong>Mô tả:</strong> ${promo.description || ''}
                    `;
                } else if (promo.promotion_type === "Chiết khấu") {
                    details += `
                        <strong>Chiết khấu:</strong> ${promo.discount_percentage ? promo.discount_percentage + '%' : ''}
                        <br>
                        <strong>Mô tả:</strong> ${promo.description || ''}
                    `;
                }

                // Close the div for this promotion
                details += `</div>`;

                // Append details to promotionDetails
                promotionDetails += details;
            });
            // Kết thúc hàng
            promotionDetails += '</div>';
            //console.log(promotionDetails);
        } else {
            var product = products.find(p => p.sap_code === sapCode);
            var product_price = { ...product };// Tạo một bản sao của đối tượng sản phẩm
            product.product_price = product_price;// Thêm trường product_price vào đối tượng sản phẩm
        }
        // console.log(product);

        let html = `
            <tr class="productItem" data-row-id="${product.id}" data-group-id="${product.promotion_group ? product.promotion_group.id : 'none'}">
                <input type="hidden" id="selectedPromotionId_${product.id}" name="promotion_ids[]" value="">
                <td class="w-1/12 text-center py-1 px-1">
                    <button type="button" class="text-white px-2 py-1 rounded text-sm mt-1 hideExpand" data-row-id="${product.id}" style="background-color: ${product.promotion_group && product.promotion_group.color_code ? product.promotion_group.color_code : 'grey'}">-</button>
                    <span class="product-index"></span>
                    <input type="hidden" name="is_gift[]" value="0" class="gift-input">
                    <input type="checkbox" class="is-gift-checkbox" onchange="updateGiftValue(this);">
                </td>
                <td class="w-1/12">
                    <span class="form-control-plaintext">${product.sap_code}</span>
                    <input type="hidden" name="sap_code[]" value="${product.sap_code}">
                </td>
                <td class="w-5/12">
                    <span class="form-control-plaintext">${product.product_name}</span>
                    <input type="hidden" name="product_name[]" value="${product.product_name}">
                </td>
                <td class="hidden">
                    <input type="number" class="form-input mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" name="packing[]" value="${product.product_price.packaging.match(/\d+/)[0] || ''}" readonly>
                </td>
                <td class="w-1/12">
                    <input type="number" class="form-input mt-1 py-1 px-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" name="thung[]" min="0">
                </td>
                <td class="w-1/12">
                    <input type="number" class="form-input mt-1 py-1 px-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" name="le[]" min="0">
                </td>
                <td class="w-1/12">
                    <input type="text" disabled class="form-input mt-1 py-1 px-1 block w-full appearance-none border-none shadow-sm bg-gray-200 rounded-sm" name="quantity[]" required>
                </td>
                <td class="hidden">
                    <input type="number" class="form-control" name="price[]" value="${product.product_price.price_sellout_per_unit}">
                </td> 
                <td class="w-1/12">
                    <input type="text" disabled class="form-input mt-1 py-1 px-1 block w-full appearance-none border-none shadow-sm bg-gray-200 rounded-sm" name="subtotal[]">
                </td>
                <td class="w-1/12">
                    <input type="number" class="form-input mt-1 py-1 px-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" name="discount_percentage[]" step="0.01">
                </td>
                <td class="hidden">
                    <input type="hidden" class="form-control" name="discounted_price[]">
                </td> 
                <td class="w-1/12">
                    <input type="text" disabled class="form-input mt-1 py-1 px-1 block w-full appearance-none border-none shadow-sm bg-gray-200 rounded-sm" name="discount[]">
                </td>
                <td class="w-1/12">
                    <input type="text" disabled class="form-input mt-1 py-1 px-1 block w-full appearance-none border-none shadow-sm bg-gray-200 rounded-sm" name="payable[]">
                </td>
                <td class="w-1/12">
                    <button type="button" class="btn btn-danger btn-sm mt-1 removeProduct" data-row-id="${product.id}">Xóa</button>
                </td>
            </tr>
            
            <tr class="productItemExpand" data-row-id="${product.id}" data-group-id="${product.promotion_group ? product.promotion_group.id : 'none'}"
                style="background-color: ${product.promotion_group && product.promotion_group.color_code ? product.promotion_group.color_code : ''}">
                <td colspan="100%">
                    <div class="flex flex-wrap items-center">
                        <div class="w-full md:w-1/2 lg:w-1/3">
                            <div class="flex flex-wrap -mx-2">
                                <div class="w-1/4 px-2">
                                    <span class="block mb-2 mt-2 ml-2 text-sm">QC: ${product.product_price.packaging}</span>
                                </div>
                                <div class="w-1/4 px-2">
                                    <span class="block mb-2 mt-2 ml-2 text-sm">Đ.giá: ${product.product_price.price_sellout_per_unit}</span>
                                </div>
                                <div class="w-2/4 px-2">
                                    <textarea class="form-control w-full ml-2 text-sm" rows="1" name="notes[]" placeholder="Ghi chú"></textarea>
                                </div>
                            </div>
                            <div class="flex flex-wrap -mx-2 ml-1">
                                <div class="w-1/3 px-2 mt-1">
                                    <label for="total_quantity" class="block text-sm font-medium text-gray-700">SL tổng</label>
                                    <input type="number" id="total_quantity" class="px-2 py-2 form-control w-full" name="total_quantity[]" />
                                </div>
                                <div class="w-1/3 px-2 mt-1">
                                    <label for="purchase" class="block text-sm font-medium text-gray-700">CTKM mua</label>
                                    <input type="number" id="purchase" class="px-2 py-2 form-control w-full" name="purchase[]" />
                                </div>
                                <div class="w-1/3 px-2 mt-1">
                                    <label for="reward" class="block text-sm font-medium text-gray-700">CTKM tặng</label>
                                    <input type="number" id="reward" class="px-2 py-2 form-control w-full" name="reward[]" />
                                </div>
                            </div>
                            <div class="flex flex-wrap -mx-2 ml-1">
                                <div class="w-1/3 px-2 mt-1">
                                    <label for="quantity_without_promotion" class="block text-sm font-medium text-gray-900">SL gốc</label>
                                    <input type="number" id="quantity_without_promotion" class="px-2 py-1 form-control w-full" name="quantity_without_promotion[]" readonly />
                                </div>
                                <div class="w-1/3 px-2 mt-1">
                                    <label for="gift_quantity" class="block text-sm font-medium text-gray-900">SL tặng</label>
                                    <input type="number" id="gift_quantity" class="px-2 py-1 form-control w-full" name="gift_quantity[]" readonly />
                                </div>
                                <div class="w-1/3 px-2 mt-1">
                                    <label for="total_including_gifts" class="block text-sm font-medium text-gray-900">Tổng SL</label>
                                    <input type="number" id="total_including_gifts" class="px-2 py-1 form-control w-full" name="total_including_gifts[]" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="w-full md:w-1/2 lg:w-2/3 lg:pl-4">
                            <div class="border-t border-gray-200 mt-2 bg-gray-100">
                                ${promotionDetails !== '' ? promotionDetails : 'Không có CTKM'}
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

        `;
        $('#productList tbody').append(html);
        
        sortProductsByPromotionGroup();// Sắp xếp lại danh sách dựa trên group_promotion_id
        updateProductIndices(); // Cập nhật số thứ tự sau khi thêm hoặc sắp xếp
    }

    function addProductToOrderWithQty(detail) {
        let sapCode = detail.sap_code;
        var product = promotions.find(p => p.sap_code === sapCode);
        let promotionDetails = '';
        if (product) { // nếu sản phẩm có khuyến mãi
            let promotionGroup = product.promotion_group;
            //console.log(promotionGroup);
            // Bắt đầu một hàng mới
            promotionDetails += '<div class="flex flex-wrap">';
            // Lặp qua từng promotion trong promotionGroup
            promotionGroup.promotion.forEach(promo => {
                // Common details for each promotion
                let details = `
                    <div class="selectPromotion promotion-item p-2 border rounded-lg shadow my-2 mx-2 cursor-pointer" 
                        id="promo_${promo.id}" data-promotion-id="${promo.id}" data-product-id="${product.id}">
                        <strong>Tên:</strong> ${promotionGroup.promotion_name}
                        <br>
                        <strong>Loại KM:</strong> ${promo.promotion_type}
                        <br>
                        <strong>SL tối thiểu:</strong> ${promo.minimum_quantity || ''}
                        <br>
                        ${promo.minimum_amount ? `<strong>Số tiền tối thiểu:</strong> ${promo.minimum_amount}<br>` : ''}
                `;
                // Conditional details based on promotion type
                if (promo.promotion_type === "Sản phẩm") {
                    details += `
                        <strong>Sản phẩm tặng:</strong> ${promo.bonus_product_id || ''}
                        <br>
                        <strong>SL tặng cố định:</strong> ${promo.bonus_quantity || '0'}
                        <br>
                        <strong>Tỉ lệ tặng:</strong> ${promo.bonus_ratio || '0'}
                        <br>
                        <strong>Mô tả:</strong> ${promo.description || ''}
                    `;
                } else if (promo.promotion_type === "Chiết khấu") {
                    details += `
                        <strong>Chiết khấu:</strong> ${promo.discount_percentage ? promo.discount_percentage + '%' : ''}
                        <br>
                        <strong>Mô tả:</strong> ${promo.description || ''}
                    `;
                }

                // Close the div for this promotion
                details += `</div>`;

                // Append details to promotionDetails
                promotionDetails += details;
            });
            // Kết thúc hàng
            promotionDetails += '</div>';
            //console.log(promotionDetails);
        } else {
            var product = products.find(p => p.sap_code === sapCode);
            var product_price = { ...product };// Tạo một bản sao của đối tượng sản phẩm
            product.product_price = product_price;// Thêm trường product_price vào đối tượng sản phẩm
        }
        // console.log(product);

        let html = `
            <tr class="productItem" data-row-id="${product.id}" data-group-id="${product.promotion_group ? product.promotion_group.id : 'none'}">
                <input type="hidden" id="selectedPromotionId_${product.id}" name="promotion_ids[]" value="">
                <td class="w-1/12 text-center py-1 px-1">
                    <button type="button" class="text-white px-2 py-1 rounded text-sm mt-1 hideExpand" data-row-id="${product.id}" style="background-color: ${product.promotion_group && product.promotion_group.color_code ? product.promotion_group.color_code : 'grey'}">-</button>
                    <span class="product-index"></span>
                    <input type="hidden" name="is_gift[]" value="0" class="gift-input">
                    <input type="checkbox" class="is-gift-checkbox" onchange="updateGiftValue(this);">
                </td>
                <td class="w-1/12">
                    <span class="form-control-plaintext">${product.sap_code}</span>
                    <input type="hidden" name="sap_code[]" value="${product.sap_code}">
                </td>
                <td class="w-5/12">
                    <span class="form-control-plaintext">${product.product_name}</span>
                    <input type="hidden" name="product_name[]" value="${product.product_name}">
                </td>
                <td class="hidden">
                    <input type="number" class="form-input mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" name="packing[]" value="${product.product_price.packaging.match(/\d+/)[0] || ''}" readonly>
                </td>
                <td class="w-1/12">
                    <input type="number" class="form-input mt-1 py-1 px-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" name="thung[]" min="0">
                </td>
                <td class="w-1/12">
                    <input type="number" class="form-input mt-1 py-1 px-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" name="le[]" min="0" value="${detail.quantity}">
                </td>
                <td class="w-1/12">
                    <input type="text" disabled class="form-input mt-1 py-1 px-1 block w-full appearance-none border-none shadow-sm bg-gray-200 rounded-sm" name="quantity[]" value="${detail.quantity}" required>
                </td>
                <td class="hidden">
                    <input type="number" class="form-control" name="price[]" value="${product.product_price.price_sellout_per_unit}">
                </td> 
                <td class="w-1/12">
                    <input type="text" disabled class="form-input mt-1 py-1 px-1 block w-full appearance-none border-none shadow-sm bg-gray-200 rounded-sm" name="subtotal[]" value="${detail.quantity * product.product_price.price_sellout_per_unit}">
                </td>
                <td class="w-1/12">
                    <input type="number" class="form-input mt-1 py-1 px-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50" name="discount_percentage[]" step="0.01">
                </td>
                <td class="hidden">
                    <input type="hidden" class="form-control" name="discounted_price[]" value="${product.product_price.price_sellout_per_unit}">
                </td> 
                <td class="w-1/12">
                    <input type="text" disabled class="form-input mt-1 py-1 px-1 block w-full appearance-none border-none shadow-sm bg-gray-200 rounded-sm" name="discount[]">
                </td>
                <td class="w-1/12">
                    <input type="text" disabled class="form-input mt-1 py-1 px-1 block w-full appearance-none border-none shadow-sm bg-gray-200 rounded-sm" name="payable[]" value="${detail.quantity * product.product_price.price_sellout_per_unit}">
                </td>
                <td class="w-1/12">
                    <button type="button" class="btn btn-danger btn-sm mt-1 removeProduct" data-row-id="${product.id}">Xóa</button>
                </td>
            </tr>
            
            <tr class="productItemExpand" data-row-id="${product.id}" data-group-id="${product.promotion_group ? product.promotion_group.id : 'none'}"
                style="background-color: ${product.promotion_group && product.promotion_group.color_code ? product.promotion_group.color_code : ''}">
                <td colspan="100%">
                    <div class="flex flex-wrap items-center">
                        <div class="w-full md:w-1/2 lg:w-1/3">
                            <div class="flex flex-wrap -mx-2">
                                <div class="w-1/4 px-2">
                                    <span class="block mb-2 mt-2 ml-2 text-sm">QC: ${product.product_price.packaging}</span>
                                </div>
                                <div class="w-1/4 px-2">
                                    <span class="block mb-2 mt-2 ml-2 text-sm">Đ.giá: ${product.product_price.price_sellout_per_unit}</span>
                                </div>
                                <div class="w-2/4 px-2">
                                    <textarea class="form-control w-full ml-2 text-sm" rows="1" name="notes[]" placeholder="Ghi chú"></textarea>
                                </div>
                            </div>
                            <div class="flex flex-wrap -mx-2 ml-1">
                                <div class="w-1/3 px-2 mt-1">
                                    <label for="total_quantity" class="block text-sm font-medium text-gray-700">SL tổng</label>
                                    <input type="number" id="total_quantity" class="px-2 py-2 form-control w-full" name="total_quantity[]" />
                                </div>
                                <div class="w-1/3 px-2 mt-1">
                                    <label for="purchase" class="block text-sm font-medium text-gray-700">CTKM mua</label>
                                    <input type="number" id="purchase" class="px-2 py-2 form-control w-full" name="purchase[]" />
                                </div>
                                <div class="w-1/3 px-2 mt-1">
                                    <label for="reward" class="block text-sm font-medium text-gray-700">CTKM tặng</label>
                                    <input type="number" id="reward" class="px-2 py-2 form-control w-full" name="reward[]" />
                                </div>
                            </div>
                            <div class="flex flex-wrap -mx-2 ml-1">
                                <div class="w-1/3 px-2 mt-1">
                                    <label for="quantity_without_promotion" class="block text-sm font-medium text-gray-900">SL gốc</label>
                                    <input type="number" id="quantity_without_promotion" class="px-2 py-1 form-control w-full" name="quantity_without_promotion[]" readonly />
                                </div>
                                <div class="w-1/3 px-2 mt-1">
                                    <label for="gift_quantity" class="block text-sm font-medium text-gray-900">SL tặng</label>
                                    <input type="number" id="gift_quantity" class="px-2 py-1 form-control w-full" name="gift_quantity[]" readonly />
                                </div>
                                <div class="w-1/3 px-2 mt-1">
                                    <label for="total_including_gifts" class="block text-sm font-medium text-gray-900">Tổng SL</label>
                                    <input type="number" id="total_including_gifts" class="px-2 py-1 form-control w-full" name="total_including_gifts[]" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="w-full md:w-1/2 lg:w-2/3 lg:pl-4">
                            <div class="border-t border-gray-200 mt-2 bg-gray-100">
                                ${promotionDetails !== '' ? promotionDetails : 'Không có CTKM'}
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

        `;
        $('#productList tbody').append(html);
        
        sortProductsByPromotionGroup();// Sắp xếp lại danh sách dựa trên group_promotion_id
        updateProductIndices(); // Cập nhật số thứ tự sau khi thêm hoặc sắp xếp
        updateTotals();
    }

    $('#productList').on('input', '[name="total_quantity[]"], [name="purchase[]"], [name="reward[]"]', function() {
        var $row = $(this).closest('.productItemExpand');
        var totalQuantity = parseFloat($row.find('[name="total_quantity[]"]').val()) || 0;
        var purchase = parseFloat($row.find('[name="purchase[]"]').val()) || 0;
        var reward = parseFloat($row.find('[name="reward[]"]').val()) || 0;
        // Tính toán số lượng không khuyến mãi
        var quantityWithoutPromotion = totalQuantity / (1 + reward/purchase);
        $row.find('[name="quantity_without_promotion[]"]').val(quantityWithoutPromotion.toFixed(2));
        // Tính toán số lượng tặng
        var giftQuantity = totalQuantity - quantityWithoutPromotion;
        $row.find('[name="gift_quantity[]"]').val(giftQuantity.toFixed(2));
        // Tính toán tổng số lượng bao gồm tặng
        var totalIncludingGifts = totalQuantity; // Đây có thể chỉ đơn giản là totalQuantity nếu đã bao gồm số lượng tặng
        $row.find('[name="total_including_gifts[]"]').val(totalIncludingGifts.toFixed(2));
    });


    $('#productList').on('click', '.selectPromotion', function() {
        const productId = $(this).data('product-id');// Lấy productId từ data attribute của element được click
        const allPromotions = $(`[data-product-id='${productId}'].promotion-item`);// Lấy tất cả các elements promotion cho sản phẩm này
        const isSelected = $(this).hasClass('bg-green-500');// Kiểm tra xem promotion hiện tại có được chọn hay không
        if (!isSelected) { // Nếu không được chọn, bỏ chọn tất cả và chọn cái hiện tại
            allPromotions.removeClass('bg-green-500');// Bỏ chọn tất cả các promotions của sản phẩm này
            $(this).addClass('bg-green-500');// Chọn promotion hiện tại
            const selectedPromotionId = $(this).data('promotion-id');// Cập nhật trường ẩn với ID của promotion được chọn
            //console.log(selectedPromotionId);
            $(`#selectedPromotionId_${productId}`).val(selectedPromotionId);
        } else {
            $(this).removeClass('bg-green-500');// Nếu đã được chọn và click lại, bỏ chọn
            $(`#selectedPromotionId_${productId}`).val('');// Xóa trường ẩn vì không có promotion nào được chọn
        }
    });

    $('#productList').on('click', '.hideExpand', function() {
        var $button = $(this);  // Lưu trữ tham chiếu đến nút được nhấn
        var $row = $(this).closest('.productItem');// Tìm dòng sản phẩm gần nhất với nút được nhấn
        var $expandRow = $row.next('.productItemExpand');// Từ dòng sản phẩm, tìm dòng mở rộng ngay sau nó
        // Chỉ ẩn/hiện dòng mở rộng tương ứng
        $expandRow.toggle();
        // Kiểm tra trạng thái hiển thị của dòng mở rộng và cập nhật văn bản trên nút
        if ($expandRow.is(':visible')) {
            $button.text('-');  // Đặt văn bản thành '-' nếu dòng mở rộng đang hiển thị
        } else {
            $button.text('+');  // Đặt văn bản thành '+' nếu dòng mở rộng không hiển thị
        }
    });

    $('#productList').on('click', '.removeProduct', function() {
        var rowId = $(this).data('row-id'); 
        $('.productItem[data-row-id="' + rowId + '"]').remove(); 
        $('.productItemExpand[data-row-id="' + rowId + '"]').remove(); 
    });

    $('#productList').on('change', '.is-gift-checkbox', function() {
        var isChecked = $(this).prop('checked');
        var $row = $(this).closest('.productItem');
        var $expandRow = $row.next('.productItemExpand'); // Lấy phần mở rộng tương ứng
        // Nếu là quà tặng, đặt giá trị null cho các ô input khác và vô hiệu hóa chúng
        if (isChecked) {
            $row.find('input[type="number"]:not([name="le[]"])').val(0);
            $row.find('input[type="text"]:not([name="quantity[]"])').val(0);
            //$expandRow.toggle(!isChecked); // Sử dụng toggle với điều kiện ngược lại của isChecked
            //$expandRow.remove();
            $expandRow.hide();
        }
    });
    function sortProductsByPromotionGroup() {
        const rows = Array.from(document.querySelectorAll('.productItem, .productItemExpand')); // Lấy cả sản phẩm và chi tiết mở rộng
        rows.sort((a, b) => {
            const groupIdA = a.dataset.groupId !== 'none' ? parseInt(a.dataset.groupId) : Infinity; // Sử dụng Infinity cho các sản phẩm không có nhóm
            const groupIdB = b.dataset.groupId !== 'none' ? parseInt(b.dataset.groupId) : Infinity;
            return groupIdA - groupIdB; // Sắp xếp tăng dần theo ID nhóm
        });
        const tbody = document.querySelector('#productList tbody');
        rows.forEach(row => {
            tbody.appendChild(row); // Đưa các hàng đã sắp xếp trở lại vào tbody
        });
    }
    function updateProductIndices() {
        const productItems = document.querySelectorAll('.productItem'); // Chỉ lấy những hàng chứa sản phẩm
        productItems.forEach((item, index) => {
            const indexElement = item.querySelector('.product-index'); // Tìm thẻ span chứa số thứ tự
            if (indexElement) {
                indexElement.textContent = index + 1; // Cập nhật số thứ tự
            }
        });
    }

    // Hàm cập nhật số lượng dựa vào số thùng và lẻ, chỉ nếu không có chỉnh sửa trực tiếp
    function updateQuantity(row) {
        let isGiftCheckbox = row.querySelector('.is-gift-checkbox');
        let isChecked = isGiftCheckbox.checked;
        if (!isChecked) {
            let packing = parseInt(row.querySelector('[name="packing[]"]').value) || 0;
            let thung = parseInt(row.querySelector('[name="thung[]"]').value) || 0;
            let le = parseInt(row.querySelector('[name="le[]"]').value) || 0;
            let price = parseFloat(row.querySelector('[name="price[]"]').value) || 0;
            let subtotal = row.querySelector('[name="subtotal[]"]');
            let discount_percentage = parseFloat(row.querySelector('[name="discount_percentage[]"]').value) || 0;
            let discountFactor = discount_percentage / 100;
            let discounted_price = row.querySelector('[name="discounted_price[]"]');

            let quantityInput = row.querySelector('[name="quantity[]"]');
            let calculatedQuantity = (packing * thung) + le;
            quantityInput.value = calculatedQuantity;  // Cập nhật số lượng dựa trên thung và lẻ
            subtotal.value = price * calculatedQuantity;

            discounted_price.value = price * (1 - discountFactor);
            let payable = row.querySelector('[name="payable[]"]');
            payable.value = discounted_price.value * calculatedQuantity;
            let discount = row.querySelector('[name="discount[]"]');
            discount.value = subtotal.value - payable.value;
            updateTotals();
        } else{
            // let thung = row.querySelector('[name="thung[]"]');
            // thung.value = 0;
            let le = parseInt(row.querySelector('[name="le[]"]').value) || 0;
            let quantityInput = row.querySelector('[name="quantity[]"]');
            quantityInput.value = le;
            updateTotals();
        }
    }

    // Bộ nghe sự kiện khi nhập số thùng hoặc lẻ
    document.getElementById('productList').addEventListener('input', function(e) {
        if (e.target.name === 'thung[]' || e.target.name === 'le[]' || e.target.name === 'discount_percentage[]') {
            let productItem = e.target.closest('.productItem');
            updateQuantity(productItem);
        }
    });

    function updateTotals() {
        var totalQuantity = 0;
        var totalDiscount = 0;
        var totalPayable = 0;
        document.querySelectorAll('[name="quantity[]"]').forEach(function(quantityInput) {
            totalQuantity += parseFloat(quantityInput.value) || 0;
        });
        document.querySelectorAll('[name="discount[]"]').forEach(function(discountInput) {
            totalDiscount += parseFloat(discountInput.value) || 0;
        });
        document.querySelectorAll('[name="payable[]"]').forEach(function(payableInput) {
            totalPayable += parseFloat(payableInput.value) || 0;
        });
        // Cập nhật nội dung text
        document.getElementById('totalQuantity').textContent = totalQuantity;
        document.getElementById('totalDiscount').textContent = totalDiscount;
        document.getElementById('totalPayable').textContent = totalPayable;
        // Cập nhật giá trị cho input hidden để gửi qua form
        document.getElementById('inputTotalQuantity').value = totalQuantity;
        document.getElementById('inputTotalDiscount').value = totalDiscount;
        document.getElementById('inputTotalPayable').value = totalPayable;
    }

    if ($("#success-alert").length) {
        // Ẩn thông báo sau 1000 milliseconds (1 giây)
        $("#success-alert").fadeTo(1000, 500).slideUp(500, function() {
            $("#success-alert").slideUp(500);
        });
    }

    $('#orderForm').on('submit', function(e) {
        e.preventDefault(); // Ngăn chặn hành vi gửi form mặc định
        $.ajax({
            type: 'POST', // Phương thức gửi dữ liệu
            url: '{{ route('orderTemporary.store') }}', // Đường dẫn tới hàm xử lý trong Laravel
            data: $(this).serialize(), // Lấy dữ liệu từ form
            success: function(response) {
                notify500();
                //console.log('Response:', response); // Hiển thị phản hồi trong console
                sessionStorage.setItem('serverResponse', JSON.stringify(response));
                setTimeout(function() {
                    location.reload();
                }, 1000); // Trì hoãn 10 giây
            },
            error: function(xhr, status, error) {
                console.error('Error:', error); // Hiển thị lỗi nếu có
            }
        });
    });

});

</script>
@endpush