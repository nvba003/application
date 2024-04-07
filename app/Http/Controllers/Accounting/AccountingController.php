<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Accounting\AccountingOrder; 
use App\Models\Accounting\AccountingOrderDetail; 

class AccountingController extends Controller
{
    public function index()
    {
        return view('accounting/index');
    }

    public function store(Request $request)
    {
        $generalData = $request->input('generalData');
        $customerData = $request->input('customerData');
        $priceData = $request->input('priceData');
        $tableData = $request->input('tableData', []);

        // Sử dụng updateOrCreate để cập nhật hoặc tạo mới AccountingOrder
        $attributes = ['order_code' => $generalData['Mã ĐH:'] ?? null]; // Điều kiện tìm kiếm dựa trên order_code
        $values = [
            'staff' => $generalData['NVBH:'] ?? null,
            'source' => $generalData['Nguồn đặt:'] ?? null,
            'status' => $generalData['Trạng thái:'] ?? null,
            'type' => $generalData['Loại đơn:'] ?? null,
            'order_date' => $generalData['Ngày đặt:'] ?? null,
            'delivery_date' => $generalData['Ngày giao thực tế:'] ?? null,
            'customer_name' => $customerData['Khách hàng'] ?? null,
            'customer_phone' => $customerData['Điện thoại'] ?? null,
            'customer_address' => $customerData['Địa chỉ'] ?? null,
            'discount' => $this->convertCurrencyToNumber($priceData['chietKhau'] ?? '0 ₫'),
            'total_amount' => $this->convertCurrencyToNumber($priceData['thanhTien'] ?? '0 ₫')
        ];
        $accountingOrder = AccountingOrder::updateOrCreate($attributes, $values);

        // Xử lý tableData
        foreach ($tableData as $item) {
            // Sử dụng updateOrCreate để cập nhật hoặc tạo mới AccountingOrderDetail dựa trên order_id và product_code
            $detailAttributes = ['order_id' => $accountingOrder->id, 'product_code' => $item['maSanPham'] ?? null];
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

        return response()->json(['message' => 'Dữ liệu đã được cập nhật thành công']);
    }

    private function convertCurrencyToNumber($currency)
    {
        // Xử lý chuyển đổi chuỗi tiền tệ sang số nguyên
        return (int) filter_var($currency, FILTER_SANITIZE_NUMBER_INT);
    }

}
