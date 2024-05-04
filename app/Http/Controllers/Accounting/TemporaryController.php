<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request; 
use App\Models\Accounting\SaleStaff;
use App\Models\Accounting\ProductPrice;
use App\Models\Accounting\OrderTemporary;
use App\Models\Accounting\OrderTemporaryDetail;
use App\Models\Accounting\Promotion;
use App\Models\Accounting\PromotionProduct;
use App\Models\Accounting\Temporary;
use App\Models\Accounting\TemporaryDetail;
use Carbon\Carbon;

class TemporaryController extends Controller
{
    public function orderTemporary(Request $request)
    {
        $perPage = $request->input('per_page',20);
        $products = ProductPrice::all();
        $saleStaffs = SaleStaff::all();
        $query = OrderTemporary::query()
            ->with(['details', 'staff']) // Tải mối quan hệ 'staff' và 'details'
            ->when($request->filled('report_date'), function ($q) use ($request) {
                // Lọc theo 'report_date'
                $q->whereDate('report_date', $request->input('report_date'));
            })
            ->when($request->filled('staff'), function ($q) use ($request) {
                // Lọc theo tên nhân viên thông qua mối quan hệ 'staff'
                $q->whereHas('staff', function ($query) use ($request) {
                    $query->where('name', $request->staff);
                });
            })
            ->orderBy('report_date', 'desc');

        $orders = $query->paginate($perPage);

        if ($request->ajax()) {
            $view = view('accounting.partials.order_temporary_table', compact('orders'))->render();
            $links = $orders->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }

        $header = 'Đơn tạm ứng';
        return view('accounting.order_temporary', compact('orders', 'header', 'saleStaffs', 'products'));
    }

    public function createOrderTemporary()
    {
        $products = ProductPrice::all();
        $saleStaffs = SaleStaff::all();
        $promotions = PromotionProduct::with(['promotionGroup.promotion', 'productPrice'])->get();
        $header = 'Tạo đơn tạm ứng';
        return view('accounting.create_order_temporary', compact('header', 'products', 'saleStaffs', 'promotions'));
    }

    // Lưu đơn hàng mới vào cơ sở dữ liệu
    public function storeOrderTemporary(Request $request)
    {
        //dd($request);
        $staff = SaleStaff::where('name', $request->staff)->first();
        $orderTemporary = new OrderTemporary();
        $orderTemporary->staff_id = $staff->id;
        $orderTemporary->discount = $request->input('totalDiscount');
        $orderTemporary->total_amount = $request->input('totalPayable');
        $orderTemporary->report_date = $request->report_date; // Ngày báo cáo
        $orderTemporary->save();

        $productCodes = $request->sap_code;
        $productNames = $request->product_name;
        $packings = $request->packing;
        $thungs = $request->thung;
        $les = $request->le;
        $prices = $request->price;
        $discountPercentages = $request->discount_percentage;
        $discountedPrices = $request->discounted_price;
        $notes = $request->notes;
        $isGifts = $request->is_gift;
        $promotionIds = $request->promotion_ids;

        foreach ($productCodes as $index => $code) {
            $detail = new OrderTemporaryDetail();
            $detail->order_temporary_id = $orderTemporary->id;
            $detail->product_code = $code;
            $detail->sap_code = $code;
            $detail->product_name = $productNames[$index];
            $detail->packing = $packings[$index] ?? 0;
            $detail->price = $prices[$index];
            $detail->thung = $thungs[$index] ?? 0;
            $detail->le = $les[$index] ?? 0;
            $detail->quantity = ($packings[$index] ?? 0) * ($thungs[$index] ?? 0) + ($les[$index] ?? 0);
            $detail->subtotal = $detail->price * $detail->quantity;
            $detail->discount_percentage = $discountPercentages[$index] ?? 0;
            $detail->discounted_price = $discountedPrices[$index];
            $detail->discount = $detail->subtotal - ($detail->discounted_price * $detail->quantity);
            $detail->payable = $detail->discounted_price * $detail->quantity;
            $detail->is_gift = !empty($isGifts[$index]);
            $detail->promotion_id = $promotionIds[$index] ?? null;
            $detail->notes = $notes[$index] ?? null;
            $detail->save();
        }
        //return response()->json(['message' => 'Success'], 200);
        return redirect()->route('orderTemporary.create')->with('success', 'Đơn hàng đã được tạo thành công.');
    }

    public function searchTemporary(Request $request)
    {
        $code = $request->input('temporary_code');
        $temporary = Temporary::with('details', 'staff')->where('temporary_code', $code)->first();
        if (!$temporary) {
            return response()->json(['message' => 'Chưa có mã đơn này'], 404);
        }
        // Tạo mảng dữ liệu để trả về
        $data = [
            'details' => $temporary->details,
            'staff' => $temporary->staff ? $temporary->staff : ''
        ];
        return response()->json($data);
    }

}
