@foreach($recoveryOrders as $order)
<tr data-order='{{ json_encode($order) }}'>
    <td><div class="checkbox-container">
        <input type="checkbox" class="order-checkbox checkItem" value="{{ $order->id }}">
        </div>
    </td>
    <td>{{ $loop->iteration }}</td>
    <td>{{ \Carbon\Carbon::parse($order->recovery_date)->format('d/m/Y') }}</td>
    <td>{{ $order->recovery_code }}</td>
    <td>{{ $order->staffs[0]->staff }}</td>
    <td>{{ $order->customer_name }}</td>
    <td>{{ number_format($order->total_discount) }}</td>
    <td>{{ number_format($order->total_discounted_amount) }}</td>
</tr>
@endforeach