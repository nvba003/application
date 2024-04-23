@foreach($recoveryOrders as $order)
<tr data-order='{{ json_encode($order) }}'>
    <td><div class="checkbox-container">
        <input type="checkbox" class="order-checkbox checkItem" value="{{ $order->id }}">
        </div>
    </td>
    <td>
        <button class="btn btn-info btn-sm expand-button" data-target="#details{{ $order->id }}" data-order="{{ $order }}">+</button>
    </td>
    <td>{{ $loop->iteration }}</td>
    <td>{{ \Carbon\Carbon::parse($order->recovery_date)->format('d/m/Y') }}</td>
    <td>{{ $order->recovery_code }}</td>
    <td>{{ $order->staff }}</td>
    <td>{{ $order->status }}</td>
    <td>{{ number_format($order->total_discount) }}</td>
    <td>{{ number_format($order->total_discounted_amount) }}</td>
    <td>
        <button class="btn btn-primary btn-sm btn-sum" data-order="{{ $order }}">Tính CK</button>
    </td>
</tr>
<!-- Chi tiết đơn hàng thu hồi -->
<tr id="details{{ $order->id }}" style="display: none;">
        <td colspan="100%">
            <div class="table-responsive">
                <table class="table table-striped table-hover"> <!-- Thêm table-hover cho hiệu ứng khi di chuột qua từng dòng -->
                    <thead class="bg-info text-white">
                        <tr>
                            <th>STT</th>    
                            <th>Mã Sản Phẩm</th>
                            <th>Tên Sản Phẩm</th>
                            <th>Số Lượng</th>
                            <th>Chiết khấu</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->recoveryDetails as $detail)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $detail->product_code }}</td>
                            <td>{{ $detail->product_name }}</td>
                            <td>{{ $detail->quantity}}</td>
                            <td>{{ $detail->discount }}</td>
                            <td>{{ $detail->payable }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
@endforeach