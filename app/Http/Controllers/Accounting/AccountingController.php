<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Accounting\AccountingOrder;
use App\Models\Accounting\AccountingOrderDetail; 
use App\Models\Accounting\AccountingRecovery;
use App\Models\Accounting\AccountingRecoveryDetail;
use App\Models\Accounting\AccountingRecoveryStaff;
use App\Models\Accounting\RecoveryOrder; 
use App\Models\Accounting\RecoveryOrderDetail;
use App\Models\Accounting\ProductPrice; 
use App\Models\Accounting\ProductDiscount;
use App\Models\Accounting\Temporary;
use App\Models\Accounting\TemporaryDetail;
use Carbon\Carbon;

class AccountingController extends Controller
{
    public function productList()//hiển thị danh sách sản phẩm
    {
        $products = ProductPrice::all();
        $header = 'Danh sách sản phẩm';
        return view('accounting.product_list', compact('products', 'header'));
    }

    public function store(Request $request)//lưu order từ extension
    {
        $generalData = $request->input('generalData');
        $customerData = $request->input('customerData');
        $priceData = $request->input('priceData');
        $tableData = $request->input('tableData', []);

        $order_date_string = $generalData['Ngày đặt:'] ?? null;
        $delivery_date_string = $generalData['Ngày giao thực tế:'] ?? null;
        $format = 'd/m/Y H:i';
        // Tạo một đối tượng Carbon từ chuỗi ngày giờ với định dạng cụ thể
        if (!empty($order_date_string) && $order_date_string !== '--') {
            $order_date = Carbon::createFromFormat($format, $order_date_string, 'Asia/Ho_Chi_Minh');
        }
        
        if (!empty($delivery_date_string) && $delivery_date_string !== '--') {
            $delivery_date = Carbon::createFromFormat($format, $delivery_date_string, 'Asia/Ho_Chi_Minh');
        }        

        // Sử dụng updateOrCreate để cập nhật hoặc tạo mới AccountingOrder
        $attributes = ['order_code' => $generalData['Mã ĐH:'] ?? null]; // Điều kiện tìm kiếm dựa trên order_code
        $values = [
            'staff' => $generalData['NVBH:'] ?? null,
            'source' => $generalData['Nguồn đặt:'] ?? null,
            'status' => $generalData['Trạng thái:'] ?? null,
            'type' => $generalData['Loại đơn:'] ?? null,
            'order_date' => $order_date ?? null,
            'delivery_date' => $delivery_date ?? null,
            'customer_name' => $customerData['Khách hàng'] ?? null,
            'customer_phone' => $customerData['Điện thoại'] ?? null,
            'customer_address' => $customerData['Địa chỉ'] ?? null,
            'discount' => $this->convertCurrencyToNumber($priceData['chietKhau'] ?? '0 ₫'),
            'total_amount' => $this->convertCurrencyToNumber($priceData['thanhTien'] ?? '0 ₫')
        ];
        $accountingOrder = AccountingOrder::updateOrCreate($attributes, $values);

        // Xử lý tableData
        foreach ($tableData as $item) {
            if (is_null($item['le']) && is_null($item['thanhTien']) && is_null($item['giamTien']) && is_null($item['thanhToan'])) {
                $this->handleSpecialData($item,$accountingOrder);// Xử lý dữ liệu đặc biệt ở đây
            } else {
                $this->handleRegularData($item,$accountingOrder);// Xử lý dữ liệu bình thường ở đây
            }
        }

        return response()->json(['message' => 'Dữ liệu đã được cập nhật thành công']);
    }

    private function convertCurrencyToNumber($currency)
    {
        // Xử lý chuyển đổi chuỗi tiền tệ sang số nguyên
        return (int) filter_var($currency, FILTER_SANITIZE_NUMBER_INT);
    }

    protected function handleSpecialData($item,$accountingOrder)
    {
        $detailAttributes = ['order_id' => $accountingOrder->id, 'product_code' => $item['maSanPham'] ?? null, 'is_special' => true];
        $detailValues = [
            'stt' => $item['stt'] ?? null,
            'product_name' => $item['tenSanPham'] ?? null,
            'product_code' => $item['maSanPham'] ?? null,
            'thung' => $item['quyCach'] ?? null,//dữ liệu số thùng ở item quyCach
            'le' => $item['gia'] ?? null,//dữ liệu số lẻ ở item gia
            'is_special' => true,
            'notes' => $item['thung'] ?? null//dữ liệu ghi chú ở item thung
        ];
        AccountingOrderDetail::updateOrCreate($detailAttributes, $detailValues);
    }

    protected function handleRegularData($item,$accountingOrder)
    {
        // Sử dụng updateOrCreate để cập nhật hoặc tạo mới AccountingOrderDetail dựa trên order_id và stt
        $detailAttributes = ['order_id' => $accountingOrder->id, 'product_code' => $item['maSanPham'] ?? null, 'is_special' => false];
        $detailValues = [
            'stt' => $item['stt'] ?? null,
            'product_name' => $item['tenSanPham'] ?? null,
            'packing' => $item['quyCach'] ?? null,
            'price' => $this->convertCurrencyToNumber($item['gia'] ?? '0 ₫'),
            'thung' => $item['thung'] ?? null,
            'le' => $item['le'] ?? null,
            'subtotal' => $this->convertCurrencyToNumber($item['thanhTien'] ?? '0 ₫'),
            'discount' => $this->convertCurrencyToNumber($item['giamTien'] ?? '0 ₫'),
            'payable' => $this->convertCurrencyToNumber($item['thanhToan'] ?? '0 ₫')
        ];
        AccountingOrderDetail::updateOrCreate($detailAttributes, $detailValues);
    }

    //==================================================================
    public function recovery(Request $request)//lưu thu hồi từ NVBH từ extension
    {
        $generalData = $request->input('generalData');
        $tableData = $request->input('tableData', []);

        $approval_date_string = $generalData['Ngày Duyệt'] ?? null;
        // Tạo một đối tượng Carbon từ chuỗi ngày giờ với định dạng cụ thể

        if (!empty($approval_date_string)) {
            $approval_date = Carbon::createFromFormat('d/m/Y H:i', $approval_date_string, 'Asia/Ho_Chi_Minh');
        }
        
        if (!empty($generalData['Ngày thu hồi'])) {
            $recovery_date = Carbon::createFromFormat('d/m/Y', $generalData['Ngày thu hồi'], 'Asia/Ho_Chi_Minh');
        }
        
        if (!empty($generalData['Ngày tạo phiếu'])) {
            $recovery_creation_date = Carbon::createFromFormat('d/m/Y', $generalData['Ngày tạo phiếu'], 'Asia/Ho_Chi_Minh');
        }

        $attributes = ['recovery_code' => $generalData['Mã phiếu'] ?? null];
        $values = [
            'staff' => $generalData['NVBH'] ?? null,
            'status' => $generalData['Trạng thái'] ?? null,
            'approval_date' => $approval_date ?? null,
            'recovery_date' => $recovery_date ?? null,
            'recovery_creation_date' => $recovery_creation_date ?? null
        ];
        $recoveryOrder = RecoveryOrder::updateOrCreate($attributes, $values);

        // Xử lý tableData
        foreach ($tableData as $item) {
            $detailAttributes = ['recovery_order_id' => $recoveryOrder->id, 'product_code' => $item['maSanPham'] ?? null];
            $detailValues = [
                'stt' => $item['stt'] ?? null,
                'product_name' => $item['tenSanPham'] ?? null,
                'quantity' => $item['quyCach'] ?? null,//số lượng lẻ ở cột quyCach
                'recovery_reason' => $item['gia'] ?? null //lý do thu hồi ở cột gia
            ];
            RecoveryOrderDetail::updateOrCreate($detailAttributes, $detailValues);
        }

        return response()->json(['message' => 'Dữ liệu đã được cập nhật thành công']);
    }

    public function accountingRecovery(Request $request)//lưu đơn hàng thu hồi từ extension
    {
        $generalData = $request->input('generalData');
        $tableData = $request->input('tableData', []);

        $recovery_date_string = $generalData['ngayTraHang'] ?? null;
        // Tạo một đối tượng Carbon từ chuỗi ngày giờ với định dạng cụ thể

        if (!empty($recovery_date_string)) {
            $recovery_date = Carbon::createFromFormat('d/m/Y', $recovery_date_string, 'Asia/Ho_Chi_Minh');
        }

        $attributes = ['recovery_code' => $generalData['maDonHang'] ?? null];
        $values = [
            'customer_name' => $generalData['tenKhachHang'] ?? null,
            'phone' => $generalData['phone'] ?? null,
            'shop_name' => $generalData['shopName'] ?? null,
            'type' => $generalData['returnType'] ?? null,
            'recovery_reason' => $generalData['recoveryReason'] ?? null,
            'address' => $generalData['address'] ?? null,
            'recovery_date' => $recovery_date ?? null
        ];
        $recoveryOrder = AccountingRecovery::updateOrCreate($attributes, $values);

        // Xử lý tableData
        foreach ($tableData as $item) {
            $detailAttributes = ['recovery_order_id' => $recoveryOrder->id, 'product_code' => $item['maSanPham'] ?? null];
            $detailValues = [
                'stt' => $item['stt'] ?? null,
                'product_name' => $item['tenSanPham'] ?? null,
                'packing' => $item['quyCach'] ?? null,
                'thung' => $item['gia'] ?? null, //thùng ở cột gia
                'le' => $item['thung'] ?? null,//lẻ ở cột thung
            ];
            AccountingRecoveryDetail::updateOrCreate($detailAttributes, $detailValues);
        }

        return response()->json(['message' => 'Dữ liệu đã được cập nhật thành công']);
    }
    
    public function saveInfoRecoveryStaff(Request $request)
    {
        $datas = $request->all(); // Lấy tất cả dữ liệu gửi đến

        foreach ($datas as $data) {
            // Kiểm tra đơn giản các trường có dữ liệu hay không
            if (empty($data['maDonHang']) || empty($data['staff'])) {
                return response()->json(['error' => 'Mã đơn hàng và nhân viên là bắt buộc'], 422);
            }

            // updateOrCreate sử dụng để thêm mới hoặc cập nhật nếu đã tồn tại
            $recoveryStaff = AccountingRecoveryStaff::updateOrCreate(
                ['recovery_code' => $data['maDonHang']], // Tìm kiếm theo recovery_code
                ['staff' => $data['staff']] // Cập nhật staff_id
            );
        }

        return response()->json([
            'message' => 'Thông tin đã được lưu thành công'
        ]);
    }
    
    public function updateProductPrice(Request $request)//cập nhật data từ extension vào product_prices
    {
        $tableData = $request->input('data');
        foreach ($tableData as $item) {
            $product = ProductPrice::updateOrCreate(
                ['sap_code' => $item['sap_code']],
                [
                    'product_code' => $item['product_code'],
                    'product_name' => $item['product_name'] ?? null,
                    'status' => $item['status'] ?? null,
                    'packaging' => $item['packaging'] ?? null,
                    'price_sellin_per_pack' => $this->convertCurrencyToNumber($item['price_sellin_per_pack'] ?? '0 ₫'),
                    'price_sellin_per_unit' => $this->convertCurrencyToNumber($item['price_sellin_per_unit'] ?? '0 ₫'),
                    'price_sellout_per_pack' => $this->convertCurrencyToNumber($item['price_sellout_per_pack'] ?? '0 ₫'),
                    'price_sellout_per_unit' => $this->convertCurrencyToNumber($item['price_sellout_per_unit'] ?? '0 ₫')
                ]
            );
            if ($product) {
                $discountPercentage = ProductDiscount::where('sap_code', $product->sap_code)->value('discount_percentage');
                $discountedPrice = round($product->price_sellout_per_unit * (1 - ($discountPercentage / 100)));
                ProductDiscount::updateOrCreate(
                    ['sap_code' => $item['sap_code']],
                    [
                        'product_code' => $product->product_code,
                        'product_name' => $product->product_name,
                        'price' => $product->price_sellout_per_unit,
                        'discount_percentage' => $discountPercentage,
                        'discounted_price' => $discountedPrice
                    ]
                );
            }
        }
        return response()->json(['message' => 'Cập nhật giá và chiết khấu thành công']);
    }

    //==================================================================
    public function productDiscounts()//hiển thị bảng giảm giá
    {
        $productDiscounts = ProductDiscount::all();
        $header = 'Danh sách sản phẩm chiết khấu';
        return view('accounting.product_discounts', compact('productDiscounts', 'header'));
    }

    public function updateProductDiscount(Request $request)//cập nhật giảm giá hàng loạt
    {
        $discounts = $request->input('discounts', []);
        foreach ($discounts as $productCode => $discountPercentage) {
            $productDiscount = ProductDiscount::where('sap_code', $productCode)->first();
            if ($productDiscount) {
                $discountedPrice = round($productDiscount->price * (1 - $discountPercentage / 100));
                $productDiscount->update([
                    'discount_percentage' => $discountPercentage,
                    'discounted_price' => $discountedPrice
                ]);
            }
        }
        return response()->json(['message' => 'Chiết khấu sản phẩm đã được cập nhật thành công']);
    }

    //==================================================================
    public function exportTemporary(Request $request)
    {
        $generalData = $request->input('generalData');
        $tableData = collect($request->input('tableData'))->slice(1); // Loại bỏ object đầu tiên
        $creationDateInput = $generalData['ngayTao'];
        $approvalDateInput = $generalData['ngayDuyet'];
        $creation_date = !empty($creationDateInput) ? Carbon::createFromFormat('d/m/Y H:i', $creationDateInput)->format('Y-m-d H:i:s') : null;
        $approval_date = !empty($approvalDateInput) ? Carbon::createFromFormat('d/m/Y H:i', $approvalDateInput)->format('Y-m-d H:i:s') : null;
        $temporary = Temporary::updateOrCreate(
            ['temporary_code' => $generalData['soPhieu']],
            [
                'staff' => $generalData['nvbh'],
                'creation_date' => $creation_date,
                'approval_date' => $approval_date,
                'type' => 0 //export là 0
            ]
        );
        foreach ($tableData as $data) {
            // Tìm sản phẩm trong bảng product_prices để lấy sap_code
            $product = ProductPrice::where('product_code', $data['stt'])->first();
            // Kiểm tra nếu sản phẩm tồn tại
            if ($product) {
                TemporaryDetail::updateOrCreate(
                    [
                        'temporary_id' => $temporary->id,
                        'product_code' => $data['stt'],
                    ],
                    [
                        'product_name' => $data['maSanPham'],
                        'sap_code' => $product->sap_code, // Thêm sap_code vào TemporaryDetail
                        'quantity' => intval(preg_replace('/\D/', '', $data['tenSanPham'])), // Chỉ lấy phần số từ tenSanPham
                        'type' => 0
                    ]
                );
            } else {
                // Xử lý trường hợp không tìm thấy sản phẩm, ví dụ lưu log hoặc tạo mục với sap_code mặc định
                TemporaryDetail::updateOrCreate(
                    [
                        'temporary_id' => $temporary->id,
                        'product_code' => $data['stt'],
                    ],
                    [
                        'product_name' => $data['maSanPham'],
                        'sap_code' => null,
                        'quantity' => intval(preg_replace('/\D/', '', $data['tenSanPham'])), // Chỉ lấy phần số từ tenSanPham
                        'type' => 0
                    ]
                );
            }
        }
        return response()->json(['message' => 'Data saved successfully']);
    }

    public function importTemporary(Request $request)
    {
        $generalData = $request->input('generalData');
        $tableData = collect($request->input('tableData'))->slice(1); // Loại bỏ object đầu tiên
        $creationDateInput = $generalData['ngayTao'];
        $approvalDateInput = $generalData['ngayDuyet'];
        $creation_date = !empty($creationDateInput) ? Carbon::createFromFormat('d/m/Y H:i', $creationDateInput)->format('Y-m-d H:i:s') : null;
        $approval_date = !empty($approvalDateInput) ? Carbon::createFromFormat('d/m/Y H:i', $approvalDateInput)->format('Y-m-d H:i:s') : null;
        $temporary = Temporary::updateOrCreate(
            ['temporary_code' => $generalData['soPhieu']],
            [
                'staff' => $generalData['nvbh'],
                'creation_date' => $creation_date,
                'approval_date' => $approval_date,
                'type' => 1 //export là 1
            ]
        );
        foreach ($tableData as $data) {
            // Tìm sản phẩm trong bảng product_prices để lấy sap_code
            $product = ProductPrice::where('product_code', $data['stt'])->first();
            // Kiểm tra nếu sản phẩm tồn tại
            if ($product) {
                TemporaryDetail::updateOrCreate(
                    [
                        'temporary_id' => $temporary->id,
                        'product_code' => $data['stt'],
                    ],
                    [
                        'product_name' => $data['maSanPham'],
                        'sap_code' => $product->sap_code, // Thêm sap_code vào TemporaryDetail
                        'quantity' => intval(preg_replace('/\D/', '', $data['tenSanPham'])), // Chỉ lấy phần số từ tenSanPham
                        'type' => 1
                    ]
                );
            } else {
                // Xử lý trường hợp không tìm thấy sản phẩm, ví dụ lưu log hoặc tạo mục với sap_code mặc định
                TemporaryDetail::updateOrCreate(
                    [
                        'temporary_id' => $temporary->id,
                        'product_code' => $data['stt'],
                    ],
                    [
                        'product_name' => $data['maSanPham'],
                        'sap_code' => null,
                        'quantity' => intval(preg_replace('/\D/', '', $data['tenSanPham'])), // Chỉ lấy phần số từ tenSanPham
                        'type' => 1
                    ]
                );
            }
        }
        return response()->json(['message' => 'Data saved successfully']);
    }
    //==================================================================
    public function getOrderCodes()
    {
        $orderCodes = AccountingOrder::pluck('order_code');
        return response()->json($orderCodes);
    }

    public function getRecoveryCodes()
    {
        $recoveryCodes = RecoveryOrder::pluck('recovery_code');
        return response()->json($recoveryCodes);
    }

    public function getOrderRecovery()
    {
        $recoveryCodes = AccountingRecovery::pluck('recovery_code');
        return response()->json($recoveryCodes);
    }
    
    public function getExportTemporary()
    {
        $temporaryCodes = Temporary::where('type', 0)->pluck('temporary_code');
        return response()->json($temporaryCodes);
    }

    public function getImportTemporary()
    {
        $temporaryCodes = Temporary::where('type', 1)->pluck('temporary_code');
        return response()->json($temporaryCodes);
    }

}
