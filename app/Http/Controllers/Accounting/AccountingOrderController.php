<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Models\Accounting\AccountingOrder; 
use App\Models\Accounting\AccountingOrderDetail; 
use App\Models\Accounting\RecoveryOrder;
use App\Models\Accounting\RecoveryOrderDetail;
use App\Models\Accounting\AccountingRecovery;
use App\Models\Accounting\AccountingRecoveryDetail;
use App\Models\Accounting\SummaryOrder;
use App\Models\Accounting\GroupOrder;
use App\Models\Accounting\SaleStaff;

class AccountingOrderController extends Controller
{
    public function index(Request $request)
    {
        $saleStaffs = SaleStaff::all();
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
        return view('accounting.orders', compact('orders', 'header','saleStaffs'));
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

        if ($request->filled('order_type')) {
            $query->where('type', $request->order_type);
        }        

        $query->orderBy('order_date', 'desc');
        return $query->paginate(10); // Số "10" là số lượng bản ghi trên mỗi trang
    }

    //==================================================================
    public function recovery(Request $request)
    {
        $saleStaffs = SaleStaff::all();
        // Gọi phương thức searchOrders để lấy danh sách đơn hàng được lọc và tìm kiếm
        $recoveryOrders = $this->searchRecoveryOrders($request);

        // Kiểm tra xem đây có phải là một yêu cầu AJAX không
        if ($request->ajax()) {
            // Sử dụng Paginator để phân trang với số lượng mục trên mỗi trang là 10 (có thể thay đổi tùy ý)
            // $currentPage = Paginator::resolveCurrentPage() ?: 1;
            // $perPage = 10; // Số lượng mục trên mỗi trang
            // $pagedData = $recoveryOrders->slice(($currentPage - 1) * $perPage, $perPage)->all();
            // $recoveryOrders = new Paginator($pagedData, $recoveryOrders->count(), $perPage, $currentPage, ['path' => Paginator::resolveCurrentPath()]);

            $view = view('accounting.partials.recovery_orders_tbody', compact('recoveryOrders'))->render();
            $links = $recoveryOrders->links()->toHtml(); // Lấy HTML của links phân trang

            // Trả về dữ liệu dưới dạng JSON với cấu trúc cho bảng và links phân trang
            return response()->json(['table' => $view, 'links' => $links]);
        }

        // Khi trang được tải lần đầu (không phải AJAX), hiển thị view chính với dữ liệu ban đầu
        $header = 'Đơn thu hồi';
        return view('accounting.recovery', compact('recoveryOrders', 'header', 'saleStaffs'));
    } 

    public function searchRecoveryOrders(Request $request)
    {
        $query = RecoveryOrder::query();

        // Lọc theo mã phiếu
        if ($request->filled('recovery_code')) {
            $query->where('recovery_code', 'like', '%' . $request->input('recovery_code') . '%');
        }

        // Lọc theo nhân viên bán hàng
        if ($request->filled('staff')) {
            $query->where('staff', 'like', '%' . $request->input('staff') . '%');
        }

        // Lọc theo ngày tạo phiếu
        if ($request->filled('recovery_date')) {
            $query->whereDate('recovery_date', $request->input('recovery_date'));
        }

        $query->orderBy('recovery_date', 'desc');
        return $query->paginate(10);
    }

//==================================================================

    public function immediateNotSummarized(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Số lượng mặc định là 3 nếu không có tham số per_page
        $saleStaffs = SaleStaff::all();
        $query = AccountingOrder::where('type', 'Đơn bán / Giao ngay')->whereDoesntHave('groupOrder');

        if ($request->filled('order_code')) {
            $query->where('order_code', 'like', '%' . $request->order_code . '%');
        }

        if ($request->filled('order_date')) {
            $query->whereDate('order_date', $request->order_date);
        }

        if ($request->filled('staff')) {
            $query->where('staff', 'like', '%' . $request->staff . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $query->orderBy('order_date', 'desc');
        $orders = $query->paginate($perPage); // Hoặc số lượng bạn muốn hiển thị trên mỗi trang

        if ($request->ajax()) {
            $view = view('accounting.partials.immediate_not_summarized_tbody', compact('orders'))->render();
            $links = $orders->links()->toHtml(); // Lấy HTML của links phân trang
            return response()->json(['table' => $view, 'links' => $links]);
        }

        $header = 'Đơn giao ngay chưa tổng hợp';
        return view('accounting.immediate_not_summarized', compact('orders','header','saleStaffs'));
    }
    
    public function scheduledNotSummarized(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Số lượng mặc định là 3 nếu không có tham số per_page
        $saleStaffs = SaleStaff::all();
        // Xây dựng truy vấn dựa trên các bộ lọc
        $query = AccountingOrder::where('type', 'Đơn bán / Giao sau')->whereDoesntHave('groupOrder');

        if ($request->filled('order_code')) {
            $query->where('order_code', 'like', '%' . $request->order_code . '%');
        }

        if ($request->filled('order_date')) {
            $query->whereDate('order_date', $request->order_date);
        }

        if ($request->filled('staff')) {
            $query->where('staff', 'like', '%' . $request->staff . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $query->orderBy('order_date', 'desc');
        $orders = $query->paginate($perPage); // Hoặc số lượng bạn muốn hiển thị trên mỗi trang

        if ($request->ajax()) {
            $view = view('accounting.partials.scheduled_not_summarized_tbody', compact('orders'))->render();
            $links = $orders->links()->toHtml(); // Lấy HTML của links phân trang
            return response()->json(['table' => $view, 'links' => $links]);
        }

        $header = 'Đơn giao sau chưa tổng hợp';
        return view('accounting.scheduled_not_summarized', compact('orders','header','saleStaffs'));
    }

    public function recoveryNotSummarized(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Số lượng mặc định là 3 nếu không có tham số per_page
        $saleStaffs = SaleStaff::all();
        $query = RecoveryOrder::whereDoesntHave('groupOrder')
                            ->with(['recoveryDetails.productDiscount']);  // Eager load ở đây

        // Lọc theo mã phiếu
        if ($request->filled('recovery_code')) {
            $query->where('recovery_code', 'like', '%' . $request->input('recovery_code') . '%');
        }

        // Lọc theo nhân viên bán hàng
        if ($request->filled('staff')) {
            $query->where('staff', 'like', '%' . $request->input('staff') . '%');
        }

        // Lọc theo ngày tạo phiếu
        if ($request->filled('recovery_date')) {
            $query->whereDate('recovery_date', $request->input('recovery_date'));
        }

        $query->orderBy('recovery_date', 'desc');
        $recoveryOrders = $query->paginate($perPage); // Hoặc số lượng bạn muốn hiển thị trên mỗi trang

        // Tính toán tổng số tiền sau khi đã phân trang
        $recoveryOrders->map(function ($order) {
            $totalAmount = 0;
            $totalDiscountedAmount = 0;
            $orderDetail = [];
            foreach ($order->recoveryDetails as $detail) {
                if ($detail->productDiscount) {
                    $price = $detail->productDiscount->price;
                    $subtotal = $detail->quantity * $price;
                    $payable = $detail->quantity * $detail->productDiscount->discounted_price;
                    $discount = $subtotal - $payable;
                    $orderDetail[] = [
                        'id' => $detail->id,
                        'price' => $price, 
                        'subtotal' => $subtotal, 
                        'discount' => $discount, 
                        'payable' => $payable
                    ];

                    $totalAmount += $subtotal;
                    $totalDiscountedAmount += $payable;
                }
            }

            $order->orderDetail = $orderDetail;
            $order->total_amount = $totalAmount;
            $order->total_discounted_amount = $totalDiscountedAmount;
            $order->total_discount = $totalAmount - $totalDiscountedAmount;

            return $order;
        });

        if ($request->ajax()) {
            $view = view('accounting.partials.recovery_not_summarized_tbody', compact('recoveryOrders'))->render();
            $links = $recoveryOrders->links()->toHtml(); // Lấy HTML của links phân trang
            return response()->json(['table' => $view, 'links' => $links]);
        }

        $header = 'Đơn thu hồi chưa tổng hợp';
        return view('accounting.recovery_not_summarized', compact('recoveryOrders', 'header', 'saleStaffs'));
    }

    public function orderRecoveryNotSummarized(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Số lượng mặc định là 3 nếu không có tham số per_page
        $saleStaffs = SaleStaff::all();
        $query = AccountingRecovery::whereDoesntHave('groupOrder')
                            ->with(['recoveryDetails.productDiscount', 'staffs']);  // Eager load ở đây

        // Lọc theo mã phiếu
        if ($request->filled('recovery_code')) {
            $query->where('recovery_code', 'like', '%' . $request->input('recovery_code') . '%');
        }

        // Lọc theo nhân viên bán hàng
        if ($request->filled('staff')) {
            $query->where('staff', 'like', '%' . $request->input('staff') . '%');
        }

        // Lọc theo ngày tạo phiếu
        if ($request->filled('recovery_date')) {
            $query->whereDate('recovery_date', $request->input('recovery_date'));
        }

        $query->orderBy('recovery_date', 'desc');
        $recoveryOrders = $query->paginate($perPage); // Hoặc số lượng bạn muốn hiển thị trên mỗi trang

        // Tính toán tổng số tiền sau khi đã phân trang
        $recoveryOrders->map(function ($order) {
            $totalAmount = 0;
            $totalDiscountedAmount = 0;
            $orderDetail = [];
            foreach ($order->recoveryDetails as $detail) {
                if ($detail->productDiscount) {
                    $quantity = $detail->packing * $detail->thung + $detail->le;
                    $price = $detail->productDiscount->price;
                    $subtotal = $quantity * $price;
                    $payable = $quantity * $detail->productDiscount->discounted_price;
                    $discount = $subtotal - $payable;
                    $orderDetail[] = [
                        'id' => $detail->id,
                        'price' => $price, 
                        'subtotal' => $subtotal, 
                        'discount' => $discount, 
                        'payable' => $payable
                    ];

                    $totalAmount += $subtotal;
                    $totalDiscountedAmount += $payable;
                }
            }

            $order->orderDetail = $orderDetail;
            $order->total_amount = $totalAmount;
            $order->total_discounted_amount = $totalDiscountedAmount;
            $order->total_discount = $totalAmount - $totalDiscountedAmount;

            return $order;
        });

        if ($request->ajax()) {
            $view = view('accounting.partials.order_recovery_not_summarized_tbody', compact('recoveryOrders'))->render();
            $links = $recoveryOrders->links()->toHtml(); // Lấy HTML của links phân trang
            return response()->json(['table' => $view, 'links' => $links]);
        }

        $header = 'Đơn thu hồi chưa tổng hợp';
        return view('accounting.order_recovery_not_summarized', compact('recoveryOrders', 'header', 'saleStaffs'));
    }
    //--------------------------------------------------

    public function addSummaryOrderForScheduled(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:accounting_orders,id',
            'invoice_code' => 'nullable|string',
            'report_date' => 'nullable|date',
        ]);
        $summaryOrder = new SummaryOrder();
        $summaryOrder->invoice_code = $validated['invoice_code'];
        $summaryOrder->report_date = $validated['report_date'];
        // Gán giá trị trực tiếp từ $request: $summaryOrder->order_id = $request->order_id;
        $summaryOrder->save();
        
        GroupOrder::create([
            'group_id' => $summaryOrder->id,
            'order_id' => $validated['order_id'],
        ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Summary Order đã được thêm thành công.',
            'summaryOrder' => $summaryOrder // Trả lại thông tin SummaryOrder mới nếu cần
        ]);
    }
    
    public function addSummaryOrderForImmediate(Request $request)
    {
        $validated = $request->validate([
            'invoice_code' => 'nullable|string',
            'report_date' => 'nullable|date',
        ]);
        // Kiểm tra và lấy dữ liệu từ request
        $invoiceCode = $validated['invoice_code'];
        $reportDate = $validated['report_date'];
        $orders = $request->input('orders');

        try {
            // Tạo một SummaryOrder cho tất cả các orderIds
            $summaryOrder = SummaryOrder::create([
                'is_group' => true,
                'invoice_code' => $invoiceCode,
                'report_date' => $reportDate,
            ]);
            foreach ($orders as $order) {
                GroupOrder::create([
                    'group_id' => $summaryOrder->id,
                    'order_id' => $order['order_id'], // Sử dụng order_id từ mảng $order
                ]);
            }

            return response()->json(['message' => 'Thêm thành công!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Có lỗi xảy ra trong quá trình xử lý'], 500);
        }
    }

    public function addSummaryOrderForRecovery(Request $request)
    {
        $validated = $request->validate([
            'invoice_code' => 'nullable|string',
            'report_date' => 'nullable|date',
        ]);
        // Kiểm tra và lấy dữ liệu từ request
        $invoiceCode = $validated['invoice_code'];
        $reportDate = $validated['report_date'];
        $orders = $request->input('orders');

        try {
            // Tạo một SummaryOrder cho tất cả các orderIds
            $summaryOrder = SummaryOrder::create([
                'is_recovery' => true,
                'invoice_code' => $invoiceCode,
                'report_date' => $reportDate,
            ]);
            
            foreach ($orders as $order) {
                GroupOrder::create([
                    'group_id' => $summaryOrder->id,
                    'recovery_id' => $order['order_id'],
                ]);
                // Cập nhật discount và total_amount vào recovery_orders sau khi tổng hợp
                $recoveryOrder = RecoveryOrder::find($order['order_id']);
                if ($recoveryOrder) {
                    $recoveryOrder->update([
                        'discount' => $order['discount'],
                        'total_amount' => $order['total_amount']
                    ]);
                }

                $orderDetails = $order['order_detail'];
                if ($orderDetails) {
                    foreach ($orderDetails as $detail) {
                        $recoveryOrderDetail = RecoveryOrderDetail::find($detail['id']);// Cập nhật các con số vào recovery_order_details sau khi tổng hợp
                        if ($recoveryOrderDetail) {
                            $recoveryOrderDetail->update([
                                'price' => $detail['price'],
                                'subtotal' => $detail['subtotal'],
                                'discount' => $detail['discount'],
                                'payable' => $detail['payable'],
                            ]);
                        }
                    }
                }
            }

            return response()->json(['message' => 'Thêm thành công!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Có lỗi xảy ra'], 500);
        }
    }

    public function addSummaryOrderForOrderRecovery(Request $request)
    {
        $validated = $request->validate([
            'invoice_code' => 'nullable|string',
            'report_date' => 'nullable|date',
            'recovery_type' => 'nullable|string',
        ]);
        // Kiểm tra và lấy dữ liệu từ request
        $invoiceCode = $validated['invoice_code'];
        $reportDate = $validated['report_date'];
        $recoveryType = $validated['recovery_type'];
        $orders = $request->input('orders');

        try {
            // Tạo một SummaryOrder cho tất cả các orderIds
            $summaryOrder = SummaryOrder::create([
                'is_recovery' => true,
                'invoice_code' => $invoiceCode,
                'report_date' => $reportDate,
                'recovery_type' => $recoveryType,
            ]);
            
            foreach ($orders as $order) {
                GroupOrder::create([
                    'group_id' => $summaryOrder->id,
                    'recovery_id' => $order['order_id'],
                ]);
                // Cập nhật discount và total_amount vào recovery_orders sau khi tổng hợp
                $recoveryOrder = AccountingRecovery::find($order['order_id']);
                if ($recoveryOrder) {
                    $recoveryOrder->update([
                        'staff' => $order['staff'],
                        'discount' => $order['discount'],
                        'total_amount' => $order['total_amount']
                    ]);
                }

                $orderDetails = $order['order_detail'];
                if ($orderDetails) {
                    foreach ($orderDetails as $detail) {
                        $recoveryOrderDetail = AccountingRecoveryDetail::find($detail['id']);// Cập nhật các con số vào recovery_order_details sau khi tổng hợp
                        if ($recoveryOrderDetail) {
                            $recoveryOrderDetail->update([
                                'price' => $detail['price'],
                                'subtotal' => $detail['subtotal'],
                                'discount' => $detail['discount'],
                                'payable' => $detail['payable'],
                            ]);
                        }
                    }
                }
            }

            return response()->json(['message' => 'Thêm thành công!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

}
