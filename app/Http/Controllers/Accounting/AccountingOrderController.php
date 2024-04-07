<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Accounting\AccountingOrder; 
use App\Models\Accounting\AccountingOrderDetail; 

class AccountingOrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $this->searchOrders($request);

        // Kiểm tra xem đây có phải là một yêu cầu AJAX không
        if ($request->ajax()) {
            $view = view('accounting.partials.orders_table_body', compact('orders'))->render();
            $links = $orders->links()->toHtml(); // Lấy HTML của links phân trang

            // Trả về dữ liệu dưới dạng JSON với cấu trúc cho bảng và links phân trang
            return response()->json(['table' => $view, 'links' => $links]);
        }

        // Khi trang được tải lần đầu (không phải AJAX), hiển thị view chính với dữ liệu ban đầu
        $header = 'Đơn hàng';
        return view('accounting.orders', compact('orders', 'header'));
    }


    protected function searchOrders(Request $request)
    {
        $query = AccountingOrder::query();

        if ($request->filled('order_code')) {
            $query->where('order_code', 'like', '%' . $request->order_code . '%');
        }

        if ($request->filled('staff')) {
            $query->where('staff', 'like', '%' . $request->staff . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('order_date')) {
            $query->whereDate('order_date', $request->order_date);
        }

        return $query->paginate(3); // Số "10" là số lượng bản ghi trên mỗi trang
    }
}
