@foreach($promotions as $promotion)
    <tr class="border-b border-gray-200 hover:bg-gray-100">
        <td class="btn btn-info btn-sm m-2 expand-button" data-target="#promotionDetails{{ $promotion->id }}">+</td>
        <td class="table-cell-style-pro" style="background-color: {{ $promotion->promotionGroup->color_code }};">
            {{ $promotion->group_promotion_id }}
        </td>
        <td class="table-cell-style-pro">
            {{ $promotion->promotion_serial }}
        </td>
        <td class="table-cell-style-pro">
            {{ $promotion->promotionGroup->promotion_name }}
        </td>
        <td class="table-cell-style-pro">
            {{ $promotion->promotion_type }}
        </td>
        <td class="table-cell-style-pro">
            {{ $promotion->minimum_quantity }}
        </td>
        <td class="table-cell-style-pro">
            {{ $promotion->minimum_amount }}
        </td>
        <td class="table-cell-style-pro">
            {{ $promotion->discount_percentage }}%
        </td>
        <td class="table-cell-style-pro">
            {{ $promotion->bonus_product_id }}
        </td>
        <td class="table-cell-style-pro">
            {{ $promotion->bonus_quantity }}
        </td>
        <td class="table-cell-style-pro">
            {{ $promotion->bonus_ratio }}
        </td>
        <td class="table-cell-style-pro">
            {{ $promotion->promotionGroup->status }}
        </td>
        <td class="table-cell-style-pro hidden">
            {{ $promotion->promotionGroup->color_code }}
        </td>
        <td class="table-cell-style-pro">
            <button class="btn btn-primary btn-sm btn-edit" data-promotion="{{ $promotion }}">Sửa</button>
        </td>
    </tr>

    <tr id="promotionDetails{{ $promotion->id }}" style="display: none;">
        <td colspan="100%" class="p-4 bg-slate-200 border-t border-gray-200">
            <div class="flex flex-col space-y-1">
                <div class="flex justify-between items-center">
                    <div class="w-1/3">
                        <p class="text-sm text-gray-700">
                            Ngày bắt đầu: {{ \Carbon\Carbon::parse($promotion->promotionGroup->start_date)->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="w-1/3">
                        <p class="text-sm text-gray-700">
                            Ngày kết thúc: {{ \Carbon\Carbon::parse($promotion->promotionGroup->end_date)->format('d/m/Y') }}
                        </p>
                    </div>
                    <div class="w-1/3">
                        <p class="text-sm font-medium text-gray-900">
                            Ghi chú: {{ $promotion->description }}
                        </p>
                    </div>
                </div>
                <p class="text-sm font-bold text-gray-900">Danh sách sản phẩm khuyến mãi:</p>
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                        <tr>
                            <th class="px-6 py-3">Mã SAP</th>
                            <th class="px-6 py-3">Tên Sản Phẩm</th>
                            <th class="px-6 py-3">Nhóm</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($promotion->promotionProducts as $detail)
                        <tr class="bg-gray-50 border-b">
                            <td class="px-6 py-2 text-gray-900">{{ $detail->sap_code }}</td>
                            <td class="px-6 py-2 text-gray-900">{{ $detail->product_name }}</td>
                            <td class="px-6 py-2 text-gray-900">{{ $detail->group_promotion_id }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </td>
    </tr>

@endforeach
