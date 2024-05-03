<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Accounting\ProductPrice;
use App\Models\Accounting\Promotion;
use App\Models\Accounting\PromotionGroup;
use App\Models\Accounting\PromotionProduct;
use Carbon\Carbon;

class PromotionController extends Controller
{
    public function promotions(Request $request)
    {
        $perPage = $request->input('per_page',50);
        $products = ProductPrice::all();
        $query = Promotion::query()
            ->with(['promotionGroup', 'promotionProducts']) // Ensure relationships are eager loaded
            ->when($request->filled('promotion_name'), function ($q) use ($request) {
                $q->whereHas('promotionGroup', function ($subQuery) use ($request) {
                    $subQuery->where('promotion_name', 'like', '%' . $request->promotion_name . '%');
                });
            })
            ->when($request->filled('promotion_type'), function ($q) use ($request) {
                $q->where('promotion_type', $request->promotion_type);
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->whereHas('promotionGroup', function ($subQuery) use ($request) {
                    $subQuery->where('status', $request->status);
                });
            })    
            ->orderBy('group_promotion_id', 'asc');
        $promotions = $query->paginate($perPage);

        if ($request->ajax()) {
            $view = view('accounting.partials.promotions_table', compact('promotions'))->render();
            $links = $promotions->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }

        $header = 'Danh sách CTKM';
        return view('accounting.promotions', compact('promotions', 'header', 'products'));
    }

    public function updatePromotion(Request $request)
    {
        $promotion = Promotion::findOrFail($request->id);
        //$promotion->group_promotion_id = $request->group_promotion_id;
        $promotion->promotion_serial = $request->promotion_serial;
        $promotion->promotion_type = $request->promotion_type;
        $promotion->minimum_quantity = $request->minimum_quantity;
        $promotion->minimum_amount = $request->minimum_amount;
        $promotion->discount_percentage = $request->discount_percentage;
        $promotion->bonus_product_id = $request->bonus_product_id;
        $promotion->bonus_quantity = $request->bonus_quantity;
        $promotion->bonus_ratio = $request->bonus_ratio;
        $promotion->description = $request->description;
        $promotion->save();

        $promotionGroup = PromotionGroup::findOrFail($request->group_promotion_id);
        $promotionGroup->promotion_name = $request->promotion_name;
        $promotionGroup->status = $request->promotion_status;
        $promotionGroup->color_code = $request->color_code;
        $promotionGroup->start_date = $request->start_date;
        $promotionGroup->end_date = $request->end_date;
        $promotionGroup->save();
        return response()->json(['message' => 'Cập nhật khuyến mãi thành công'], 200);
    }

    public function promotionProducts(Request $request)
    {
        $perPage = $request->input('per_page',50);
        $products = ProductPrice::all();
        $query = PromotionProduct::query()
            ->with(['promotionGroup', 'productPrice'])
            ->orderBy('group_promotion_id', 'asc');

        $promotions = $query->paginate($perPage);
        if ($request->ajax()) {
            $view = view('accounting.partials.promotion_products_table', compact('promotions'))->render();
            $links = $promotions->links()->toHtml();
            return response()->json(['table' => $view, 'links' => $links]);
        }
        $header = 'Danh sách CTKM';
        return view('accounting.promotion_products', compact('promotions', 'header', 'products'));
    }

    public function updatePromotionProduct(Request $request)
    {
        // Tìm sản phẩm khuyến mãi dựa trên ID và cập nhật nó
        $promotionProduct = PromotionProduct::findOrFail($request->id);
        $promotionProduct->group_promotion_id = $request->group_promotion_id;
        $promotionProduct->sap_code = $request->sap_code;
        $promotionProduct->product_name = $request->product_name;
        $promotionProduct->group_promotion_id = $request->group_promotion_id;
        $promotionProduct->save(); // Lưu các thay đổi

        return response()->json(['message' => 'Cập nhật đơn hàng thành công'], 200);
    }

    public function destroy($id)
    {
        $product = PromotionProduct::find($id);
        if ($product) {
            $product->delete();
            return redirect()->route('promotionProducts')->with('success', 'Sản phẩm đã được xóa thành công.');
        }
        return back()->with('error', 'Sản phẩm không tồn tại.');
    }

    public function create(Request $request)
    {
        $product = new PromotionProduct();
        $product->sap_code = $request->sap_code;
        $product->product_name = $request->product_name;
        $product->group_promotion_id = $request->group_promotion_id;
        $product->save();
        return response()->json(['message' => 'Sản phẩm khuyến mãi đã được thêm thành công!']);
    }



}
