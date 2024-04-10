<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Accounting\Transaction;
use App\Models\Accounting\AccountingOrder; 
use App\Models\Accounting\AccountingOrderDetail; 
use App\Models\Accounting\RecoveryOrder;
use App\Models\Accounting\SummaryOrder;
use App\Models\Accounting\GroupOrder;
use App\Models\Accounting\SaleStaff;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function transactions(Request $request)
    {
        $saleStaffs = SaleStaff::all();
        // Đảm bảo rằng bạn tải mối quan hệ 'staff' và 'submitter'
        $query = Transaction::with(['staff', 'submitter']);
        // Sửa lại điều kiện để sử dụng 'staff_id' và 'submitter_id' (giả sử đó là tên trường)
        if ($request->filled('submitter')) {
            $query->whereHas('submitter', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->submitter . '%');
            });
        }
        
        if ($request->filled('submit_date')) {
            $query->whereDate('created_at', $request->submit_date);
        }
        
        if ($request->filled('staff')) {
            $query->whereHas('staff', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->staff . '%');
            });
        }
        
        $query->orderBy('created_at', 'desc');
        $transactions = $query->paginate(3); // Hoặc số lượng bạn muốn hiển thị trên mỗi trang
        if ($request->ajax()) {
            $view = view('accounting.partials.transactions_table', compact('transactions'))->render();
            $links = $transactions->links()->toHtml(); // Lấy HTML của links phân trang
            return response()->json(['table' => $view, 'links' => $links]);
        }
        $header = 'Giao dịch';
        return view('accounting.transactions', compact('transactions', 'header', 'saleStaffs'));
    }


    public function summaryOrders(Request $request)
    {
        $saleStaffs = SaleStaff::all();
        $query = SummaryOrder::query()
            ->with(['groupOrder.accountingOrders'])
            ->when($request->filled('report_date'), function ($q) use ($request) {
                $q->whereDate('report_date', $request->report_date);
            })
            ->when($request->filled('staff'), function ($q) use ($request) {
                // Tìm kiếm dựa trên trường staff trong AccountingOrder thông qua GroupOrder
                $q->whereHas('groupOrder.accountingOrders', function ($q) use ($request) {
                    $q->where('staff', 'like', '%' . $request->staff . '%');
                    //$q->where('staff_id', $request->staff);
                });
            })
            ->when($request->filled('transaction_id'), function ($q) use ($request) {
                // Giả sử transaction_id là một trường trong bảng summary_orders
                $q->where('transaction_id', $request->transaction_id);
            })
            ->when($request->filled('is_group'), function ($q) use ($request) {
                $q->where('is_group', $request->is_group);
            })
            // Lọc theo có transaction_id hay không
            ->when($request->filled('has_transaction_id'), function ($q) use ($request) {
                if ($request->has_transaction_id == '1') {
                    $q->whereNotNull('transaction_id'); // Có transaction_id
                } elseif ($request->has_transaction_id == '0') {
                    $q->whereNull('transaction_id'); // Không có transaction_id
                }
            })
            ->orderBy('report_date', 'desc'); // Sắp xếp theo report_date giảm dần

        $summaryOrders = $query->paginate(3);
        // Lấy thông tin chi tiết cho mỗi summaryOrder có group_id
        $summaryOrders->load(['groupOrder.accountingOrders.orderDetails']);//lấy các accounting_order trong groupOrder, nhưng chỉ lấy được 1 sản phẩm trong đơn

        if ($request->ajax()) {
            $view = view('accounting.partials.summary_orders_table', compact('summaryOrders'))->render();
            $links = $summaryOrders->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }

        $header = 'Tổng hợp';
        return view('accounting.summary_orders', compact('summaryOrders', 'header', 'saleStaffs'));
    }

    public function saveTransaction(Request $request)
    {
        //$transaction = $request->all(); // Lấy toàn bộ dữ liệu yêu cầu
        // Tìm ID nhân viên dựa trên tên
        $staff = SaleStaff::where('name', $request->input('staff_id'))->first();
        $submitter = SaleStaff::where('name', $request->input('submitter_id'))->first();

        // Kiểm tra xem nhân viên và người nộp có tồn tại không
        if (!$staff || !$submitter) {
            //return response()->json(['message' => 'Không tìm thấy tên trong bảng nhân viên'], 404);
        }

        $now = Carbon::now();
        // Tạo ID với định dạng thời gian mdYHis
        $id = $now->format('mdYHis');

        // Tạo giao dịch mới với ID nhân viên và người nộp
        $transactionData = Transaction::create([
            'id' => $id,
            'staff_id' => $staff->id,
            'transfer_amount' => $request->input('transfer_amount'),
            'note_500' => $request->input('note_500000'),
            'note_200' => $request->input('note_200000'),
            'note_100' => $request->input('note_100000'),
            'note_50' => $request->input('note_50000'),
            'note_20' => $request->input('note_20000'),
            'note_10' => $request->input('note_10000'),
            'note_5' => $request->input('note_5000'),
            'note_2' => $request->input('note_2000'),
            'note_1' => $request->input('note_1000'),
            'total_amount' => $request->input('total_amount'),
            'submitter_id' => 1,//$submitter->id,
            'notes' => $request->input('notes'),
        ]);

        $summaryOrderIds = $request->input('summary_order_ids');
        // Cập nhật các hàng trong summary_orders với transaction_id mới
        SummaryOrder::whereIn('id', $summaryOrderIds)
                    ->update(['transaction_id' => $transactionData->id]);

        // Phản hồi cho client rằng giao dịch đã được lưu thành công
        return response()->json(['message' => 'Success'], 200);
    }

    public function updateSummary(Request $request)
    {
        // Validate dữ liệu đầu vào
        $validatedData = $request->validate([
            'id' => 'required|integer|gt:0',
            'invoice_code' => 'nullable|string',
            'is_entered' => 'nullable',
            'report_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validatedData['is_entered'] = $validatedData['is_entered'] == 1 ? true : false;

        $id = $validatedData['id'];
        // Tìm đơn hàng cần cập nhật
        $summaryOrder = SummaryOrder::findOrFail($id);
        // Cập nhật thông tin đơn hàng
        $summaryOrder->update($validatedData);
        return response()->json(['message' => 'Cập nhật đơn hàng thành công'], 200);
    }

    public function updateIsEntered(Request $request, $id)
    {
        $summaryOrder = SummaryOrder::findOrFail($id);
        // Đặt giá trị của is_entered thành true
        $summaryOrder->is_entered = true;
        $summaryOrder->save();
        return response()->json(['message' => 'Cập nhật thành công'], 200);
    }



}
