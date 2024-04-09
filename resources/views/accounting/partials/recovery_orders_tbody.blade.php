@foreach($recoveryOrders as $order)
<tr>
    <td class="expand-button" data-target="#orderDetails{{ $order->id }}">+</td>
    <td>{{ $order->recovery_code }}</td>
    <td>{{ $order->staff }}</td>
    <td>{{ $order->status }}</td>
    <td>{{ $order->approval_date }}</td>
    <td>{{ $order->recovery_creation_date }}</td>
    <td>{{ $order->recovery_date }}</td>
</tr>
<!-- Chi tiết đơn hàng thu hồi -->
<tr id="orderDetails{{ $order->id }}" style="display: none;">
    <td colspan="7">
        <p>Chi Tiết Đơn Hàng Thu Hồi:</p>
        <table class="table">
            <thead>
                <tr>
                    <th>Mã Sản Phẩm</th>
                    <th>Tên Sản Phẩm</th>
                    <th>Số Lượng</th>
                    <th>Lý Do Thu Hồi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->recoveryDetails as $detail)
                <tr>
                    <td>{{ $detail->product_code }}</td>
                    <td>{{ $detail->product_name }}</td>
                    <td>{{ $detail->quantity }}</td>
                    <td>{{ $detail->recovery_reason }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </td>
</tr>
@endforeach
