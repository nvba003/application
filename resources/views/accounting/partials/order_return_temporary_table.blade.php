@foreach($orders as $order)
    <tr>
        <td class="px-2 py-2 border-b border-gray-200 bg-white text-base">
            <input type="checkbox" class="order-checkbox checkItem" value="{{ $order->id }}" data-id="{{ $order->id }}" data-staff-name="{{ $order->staff->name }}">
        </td>
        <td class="px-2 py-2 border-b border-gray-200 bg-white text-base">
            <button class="btn btn-info btn-sm expand-button"data-target="#details{{ $order->id }}">+</button>
        </td>
        <td class="px-2 py-2 border-b border-gray-200 bg-white text-base text-center">
            {{ \Carbon\Carbon::parse($order->report_date)->format('d/m/Y') }}
        </td>
        <td class="px-2 py-2 border-b border-gray-200 bg-white text-base text-left">
            {{ $order->staff->name }}
        </td>
        <td class="px-2 py-2 border-b border-gray-200 bg-white text-base text-right">
            {{ $order->discount }}
        </td>
        <td class="px-2 py-2 border-b border-gray-200 bg-white text-base text-right">
            {{ $order->total_amount }}
        </td>
        <td class="px-2 py-2 border-b border-gray-200 bg-white text-base text-left">
            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded" onclick="removeOrder({{ $order->id }})">Xóa</button>
        </td>
    </tr>
    <!-- Chi tiết đơn hàng -->
    <tr id="details{{ $order->id }}" class="details-row" style="display: none;">
        <td colspan="100%" class="p-0">
            <table class="w-full text-sm text-left text-gray-700 bg-white">
                <thead class="text-xs text-gray-700 uppercase bg-gray-400">
                    <tr>
                        <th class="px-2 py-2">Mã SAP</th>
                        <th class="px-2 py-2">Tên Sản Phẩm</th>
                        <th class="px-2 py-2">Giá</th>
                        <th class="px-2 py-2">Thùng</th>
                        <th class="px-2 py-2">Lẻ</th>
                        <th class="px-2 py-2">S.Lượng</th>
                        <th class="px-2 py-2">% CK</th>
                        <th class="px-2 py-2">Giảm Tiền</th>
                        <th class="px-2 py-2">T.Toán</th>
                        <th class="px-2 py-2">Loại</th>
                        <th class="px-2 py-2">CTKM</th>
                        <th class="px-2 py-2">Ghi Chú</th>
                    </tr>
                </thead>
                <tbody class="bg-gray-200 divide-y divide-gray-200">
                    @foreach ($order->details as $detail)
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-2">{{ $detail->sap_code }}</td>
                            <td class="px-2 py-2">{{ $detail->product_name }}</td>
                            <td class="px-2 py-2">{{ $detail->price }}</td>
                            <td class="px-2 py-2">{{ $detail->thung }}</td>
                            <td class="px-2 py-2">{{ $detail->le }}</td>
                            <td class="px-2 py-2">{{ $detail->quantity }}</td>
                            <td class="px-2 py-2">{{ $detail->discount_percentage }}</td>
                            <td class="px-2 py-2">{{ $detail->discount }}</td>
                            <td class="px-2 py-2">{{ $detail->payable }}</td>
                            <td class="px-2 py-2">{{ $detail->is_gift ? 'KM' : '' }}</td>
                            <td class="px-2 py-2">{{ $detail->promotion_id }}</td>
                            <td class="px-2 py-2">{{ $detail->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </td>
    </tr>

@endforeach
