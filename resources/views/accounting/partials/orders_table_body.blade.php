@foreach ($orders as $order)
<tr>
    <td class="expand-button" data-target="#details{{ $order->id }}">+</td>
    <td>{{ $order->order_code }}</td>
    <td>{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</td>
    <td>{{ $order->staff }}</td>
    <td>{{ $order->status }}</td>
    <td>{{ $order->discount }}</td>
    <td>{{ $order->total_amount }}</td>
</tr>

<tr id="details{{ $order->id }}" class="details-row" style="display: none;">
    <td colspan="7"> {{-- Thay 'X' bằng số lượng cột của bảng --}}
        <h5>Chi Tiết Đơn Hàng</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã SP</th>
                    <th>Tên SP</th>
                    <th>Quy Cách</th>
                    <th>Giá</th>
                    <th>Số Lượng</th>
                    <th>Thành Tiền</th>
                    {{-- Thêm tiêu đề cho các cột chi tiết đơn hàng khác tại đây --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($order->orderDetails as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->product_code }}</td>
                    <td>{{ $detail->product_name }}</td>
                    <td>{{ $detail->packing }}</td>
                    <td>{{ number_format($detail->price) }}</td>
                    <td>{{ $detail->packing * $detail->thung + $detail->le }}</td>
                    <td>{{ number_format($detail->subtotal) }}</td>
                    {{-- Thêm dữ liệu cho các cột chi tiết đơn hàng khác tại đây --}}
                </tr>
                @endforeach
            </tbody>
        </table>
    </td>
</tr>
@endforeach
