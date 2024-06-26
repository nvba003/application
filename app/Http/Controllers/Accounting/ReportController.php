<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Accounting\Transaction;
use App\Models\Accounting\TransactionDetail;
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
        $perPage = $request->input('per_page', 10);
        $saleStaffs = SaleStaff::all();
        $query = Transaction::with(['staff', 'details']);
        
        $query->when($request->filled('pay_date'), function ($q) use ($request) {
            return $q->whereDate('pay_date', $request->pay_date);
        });
        
        $query->when($request->filled('staff'), function ($q) use ($request) {
            return $q->whereHas('staff', function ($subQuery) use ($request) {
                $subQuery->where('name', 'like', '%' . $request->staff . '%');
            });
        });
        
        $query->when($request->filled('difference_amount') && in_array($request->difference_amount, [1, 0]), function ($q) use ($request) {
            if ($request->difference_amount == 1) {
                return $q->where('diff_amount', '>=', 1000);
            } else {
                return $q->where('diff_amount', '>=', 0);
            }
        });
        
        $query->orderBy('pay_date', 'desc');
        $transactions = $query->paginate($perPage); // Hoặc số lượng bạn muốn hiển thị trên mỗi trang
        if ($request->ajax()) {
            //dd($request->all());
            $view = view('accounting.partials.transactions_table', compact('transactions'))->render();
            $links = $transactions->links()->toHtml(); // Lấy HTML của links phân trang
            return response()->json(['table' => $view, 'links' => $links]);
        }
        $header = 'Thông tin thanh toán';
        return view('accounting.transactions', compact('transactions', 'header', 'saleStaffs'));
    }

    public function transactionDetails(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $saleStaffs = SaleStaff::all();
        $query = Transaction::with(['staff', 'details']);
        
        $query->when($request->filled('pay_date'), function ($q) use ($request) {
            return $q->whereDate('pay_date', $request->pay_date);
        });
        
        $query->when($request->filled('staff'), function ($q) use ($request) {
            return $q->whereHas('staff', function ($subQuery) use ($request) {
                $subQuery->where('name', 'like', '%' . $request->staff . '%');
            });
        });

        $query->when($request->filled('transactionDetail_id'), function ($q) use ($request) {
            $q->whereHas('details', function ($subQuery) use ($request) {
                $subQuery->where('id', $request->transactionDetail_id);
            });
        });

        $query->orderBy('pay_date', 'desc');

        $transactions = $query->paginate($perPage); // Hoặc số lượng bạn muốn hiển thị trên mỗi trang
        if ($request->ajax()) {
            //dd($request->all());
            $view = view('accounting.partials.transaction_details_table', compact('transactions'))->render();
            $links = $transactions->links()->toHtml(); // Lấy HTML của links phân trang
            return response()->json(['table' => $view, 'links' => $links]);
        }
        $header = 'Chi tiết thanh toán';
        return view('accounting.transaction_details', compact('transactions', 'header', 'saleStaffs'));
    }


    public function summaryOrders(Request $request)
    {
        $perPage = $request->input('per_page',20); // Số lượng mặc định là 3 nếu không có tham số per_page
        $saleStaffs = SaleStaff::all();
        $query = SummaryOrder::query()
            ->with(['groupOrder.accountingOrders'])
            ->when($request->filled('report_date'), function ($q) use ($request) {
                $q->whereDate('report_date', $request->report_date);
            })
            ->when($request->filled('staff'), function ($q) use ($request) {
                // Tìm kiếm dựa trên trường staff trong cả AccountingOrder và RecoveryOrder thông qua GroupOrder
                $q->where(function ($q) use ($request) {
                    $q->whereHas('groupOrder.accountingOrders', function ($q) use ($request) {
                        $q->where('staff', 'like', '%' . $request->staff . '%');
                    })
                    ->orWhereHas('groupOrder.recoveryOrders', function ($q) use ($request) {
                        $q->where('staff', 'like', '%' . $request->staff . '%');
                    });
                });
            })            
            ->when($request->filled('transaction_id'), function ($q) use ($request) {
                // Giả sử transaction_id là một trường trong bảng summary_orders
                $q->where('transaction_id', $request->transaction_id);
            })
            ->when($request->filled('is_group'), function ($q) use ($request) {
                switch ($request->is_group) {
                    case '1':
                        $q->where('is_group', 1); // 'is_group = 1' đại diện cho "giao ngay"
                        break;
                    case '2':
                        $q->where('is_group', 0)->where('is_recovery', 0); // 'is_group = 0' và 'is_recovery = 0' đại diện cho "giao sau"
                        break;
                    case '3':
                        $q->where('is_recovery', 1); // 'is_recovery = 1' đại diện cho "thu hồi"
                        break;
                    case '4':
                        $q->where(function ($query) {
                            $query->where('is_group', 1)
                                  ->orWhere('is_recovery', 1);
                        });
                        break;
                    case '5':
                        $q->where(function ($query) {
                            $query->where('is_group', 1)
                                    ->orWhere('is_recovery', 1)
                                    ->where('recovery_type', 1);
                        });
                        break;
                    case '6':
                        $q->where(function ($query) {
                            $query->where('is_group', 0)
                                    ->where('recovery_type', 0);
                        });
                        break;
                }
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

        $summaryOrders = $query->paginate($perPage);
        // Lấy thông tin chi tiết cho mỗi summaryOrder có group_id
        $summaryOrders->load([
            'groupOrder.accountingOrders.orderDetails',
            'groupOrder.recoveryOrders.recoveryDetails'
        ]);        
        //$summaryOrders->load(['groupOrder.accountingOrders.orderDetails']);//lấy các accounting_order trong groupOrder, nhưng chỉ lấy được 1 sản phẩm trong đơn

        if ($request->ajax()) {
            $view = view('accounting.partials.summary_orders_table', compact('summaryOrders'))->render();
            $links = $summaryOrders->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);//, 'summaryOrders' => $summaryOrders
        }

        $header = 'Danh sách đơn hàng tổng hợp';
        return view('accounting.summary_orders', compact('summaryOrders', 'header', 'saleStaffs'));
    }

    public function saveTransaction(Request $request)
    {
        $staff = SaleStaff::where('name', $request->input('staff_id'))->first();
        $transactionData = Transaction::create([//nếu thêm đơn vào giao dịch thì cập nhật mã giao dịch vào đơn và sửa lại total_amount transaction
            'staff_id' => $staff->id,
            'customer_name' => $request->input('customer'),
            'total_amount' => $request->input('total_amount'),
            'diff_amount' => $request->input('total_amount'),
            'pay_date' => $request->input('pay_date'),
            'notes' => $request->input('notes'),
        ]);

        $summaryOrderIds = $request->input('summary_order_ids');
        // Cập nhật các hàng trong summary_orders với transaction_id mới
        SummaryOrder::whereIn('id', $summaryOrderIds)
                    ->update(['transaction_id' => $transactionData->id]);

        // Phản hồi cho client rằng giao dịch đã được lưu thành công
        return response()->json(['message' => 'Success'], 200);
    }

    public function addTransactionDetail(Request $request)
    {
        $now = Carbon::now();
        // Tạo ID với định dạng thời gian mdYHis
        $id = $now->format('mdYHis');

        // Tạo giao dịch mới với ID nhân viên và người nộp
        $transactionData = TransactionDetail::create([
            'id' => $id,
            'transaction_id' => $request->input('transaction_id'),
            'staff_id' => $request->input('staff_id'),
            'transfer_amount' => $request->input('transferTotal'),
            'note_500' => $request->input('note_500'),
            'note_200' => $request->input('note_200'),
            'note_100' => $request->input('note_100'),
            'note_50' => $request->input('note_50'),
            'note_20' => $request->input('note_20'),
            'note_10' => $request->input('note_10'),
            'note_5' => $request->input('note_5'),
            'note_2' => $request->input('note_2'),
            'note_1' => $request->input('note_1'),
            'cash' => $request->input('cashTotal'),
            'total_amount' => $request->input('combinedTotal'),
            'notes' => $request->input('notes'),
        ]);
        
        $transaction = Transaction::find($request->input('transaction_id'));
        if ($transaction) {
            $diff_amount = $transaction->diff_amount - $transactionData->total_amount;
            $transaction->update(['diff_amount' => $diff_amount]);
        }

        // Phản hồi cho client rằng giao dịch đã được lưu thành công
        return response()->json(['message' => 'Success'], 200);
    }
    
    public function updateTransaction(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|gt:0',
            'total_amount' => 'nullable',
            'diff_amount' => 'nullable',
            'pay_date' => 'nullable|date',
            'customer_name' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $id = $validatedData['id'];
        $transaction = Transaction::findOrFail($id);
        $transaction->update($validatedData);
        return response()->json(['message' => 'Cập nhật đơn hàng thành công'], 200);
    }

    public function updateTransactionDetail(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|integer|gt:0',
            'transfer_amount' => 'nullable',
            'cash' => 'nullable',
            'total_amount' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);
        $transactionId = $request->transaction_id;
        $id = $validatedData['id'];
        $transactionDetail = TransactionDetail::findOrFail($id);
        $transactionDetailBefore = $transactionDetail->total_amount;//tổng tiền chi tiết trước đó
        $transactionDetail->update($validatedData);

        $transaction = Transaction::findOrFail($transactionId);
        $change = $transactionDetailBefore - $validatedData['total_amount'];//số tiền thay đổi
        $changeDiff = $transaction->diff_amount + $change;
        $transaction->update([
            'diff_amount' => $changeDiff
        ]);

        return response()->json(['message' => 'Cập nhật đơn hàng thành công'], 200);
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
