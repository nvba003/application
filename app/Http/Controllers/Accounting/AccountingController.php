<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Accounting\AccountingOrder; 
use App\Models\Accounting\AccountingOrderDetail; 
use Carbon\Carbon;

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

        $order_date_string = $generalData['Ngày đặt:'] ?? null;
        $delivery_date_string = $generalData['Ngày giao thực tế:'] ?? null;
        $format = 'd/m/Y H:i';
        // Tạo một đối tượng Carbon từ chuỗi ngày giờ với định dạng cụ thể
        $order_date = Carbon::createFromFormat($format, $order_date_string, 'UTC');
        $delivery_date = Carbon::createFromFormat($format, $delivery_date_string, 'UTC');

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
                $this->handleSpecialData($item);// Xử lý dữ liệu đặc biệt ở đây
            } else {
                $this->handleRegularData($item);// Xử lý dữ liệu bình thường ở đây
            }
        }

        return response()->json(['message' => 'Dữ liệu đã được cập nhật thành công']);
    }

    private function convertCurrencyToNumber($currency)
    {
        // Xử lý chuyển đổi chuỗi tiền tệ sang số nguyên
        return (int) filter_var($currency, FILTER_SANITIZE_NUMBER_INT);
    }

    protected function handleSpecialData($item)
    {
        $detailAttributes = ['order_id' => $accountingOrder->id, 'product_code' => $item['maSanPham'] ?? null];
        $detailValues = [
            'stt' => $item['stt'] ?? null,
            'product_name' => $item['tenSanPham'] ?? null,
            'thung' => $item['quyCach'] ?? null,//dữ liệu số thùng ở item quyCach
            'le' => $item['gia'] ?? null,//dữ liệu số lẻ ở item gia
            'is_special' => true,
            'notes' => $item['thung'] ?? null//dữ liệu ghi chú ở item thung
        ];
        AccountingOrderDetail::updateOrCreate($detailAttributes, $detailValues);
    }

    protected function handleRegularData($item)
    {
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

}
