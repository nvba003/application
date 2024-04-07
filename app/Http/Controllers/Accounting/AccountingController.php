<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
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
        // Giả sử dữ liệu JSON đã được gửi với Content-Type là application/json
        // Laravel sẽ tự động chuyển đổi nó thành một array PHP

        // Trích xuất thông tin từ các object con
        $generalData = $request->input('generalData');
        $customerData = $request->input('customerData');
        $priceData = $request->input('priceData');
        
        // Xử lý generalData
        $accountingOrder = new AccountingOrder();
        $accountingOrder->order_code = $generalData['Mã ĐH'] ?? null;
        $accountingOrder->staff = $generalData['NVBH'] ?? null;
        $accountingOrder->source = $generalData['Nguồn đặt'] ?? null;
        $accountingOrder->status = $generalData['Trạng thái'] ?? null;
        $accountingOrder->type = $generalData['Loại đơn'] ?? null;

        // Xử lý customerData
        $accountingOrder->customer_name = $customerData['Khách hàng'] ?? null;
        $accountingOrder->customer_phone = $customerData['Điện thoại'] ?? null;
        $accountingOrder->customer_address = $customerData['Địa chỉ'] ?? null;

        // Xử lý priceData
        $accountingOrder->discount = $this->convertCurrencyToNumber($priceData['chietKhau'] ?? '0 ₫');
        $accountingOrder->total_amount = $this->convertCurrencyToNumber($priceData['thanhTien'] ?? '0 ₫');

        // Lưu đơn hàng
        $accountingOrder->save();

        // Sau khi lưu đơn hàng, lấy ID của đơn hàng vừa lưu
        $orderId = $accountingOrder->id;
        $tableData = $request->input('tableData', []);
        // Ví dụ: lưu thông tin chi tiết đơn hàng vào bảng OrderDetail
        foreach ($tableData as $item) {
            $orderDetail = new AccountingOrderDetail();
            $orderDetail->order_id = $orderId; // ID của đơn hàng vừa được lưu
            $orderDetail->stt = $item['stt'] ?? null;
            $orderDetail->product_code = $item['product_code'] ?? null;
            $orderDetail->product_name = $item['product_name'] ?? null;
            $orderDetail->packing = $item['packing'] ?? null; // Quy cách đóng gói
            $orderDetail->price = $this->convertCurrencyToNumber($item['price'] ?? null);
            $orderDetail->thung = $item['thung'] ?? null;
            $orderDetail->le = $item['le'] ?? null;
            $orderDetail->subtotal = $this->convertCurrencyToNumber($item['subtotal'] ?? null); // Thành tiền
            $orderDetail->discount = $this->convertCurrencyToNumber($item['discount'] ?? null); // Giảm tiền
            $orderDetail->payable = $this->convertCurrencyToNumber($item['payable'] ?? null); // Thanh toán
            $orderDetail->save();
        }

        return response()->json(['message' => 'Dữ liệu đã được lưu thành công']);
    }

    private function convertCurrencyToNumber($currency)
    {
        // Loại bỏ các ký tự không phải số và chuyển đổi chuỗi kết quả thành số nguyên
        return (int) filter_var($currency, FILTER_SANITIZE_NUMBER_INT);
    }


}
