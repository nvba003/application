@foreach ($orders as $order)
<tr data-order='{{ json_encode($order) }}'>
    <td><div class="checkbox-container">
        <input type="checkbox" class="order-checkbox" value="{{ $order->id }}">
        </div>
    </td>
    <td>{{ $loop->iteration }}</td>
    <td>{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</td>
    <td>{{ $order->order_code }}</td>
    <td>{{ $order->staff }}</td>
    <td>{{ $order->status }}</td>
    <td>{{ number_format($order->discount) }}</td>
    <td>{{ number_format($order->total_amount) }}</td>
</tr>
@endforeach
