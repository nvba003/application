@foreach ($orders as $order)
<tr>
    <td>{{ $loop->iteration }}</td> <!-- Sử dụng $loop->iteration để hiển thị STT -->    
    <td>{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</td>
    <td>{{ $order->order_code }}</td>
    <td>{{ $order->staff }}</td>
    <td>{{ $order->status }}</td>
    <td>{{ number_format($order->discount) }}</td>
    <td>{{ number_format($order->total_amount) }}</td>
    <td>
        <!-- <button class="btn btn-primary btn-sm" onclick="addSummaryOrder({{ $order->id }})">Thêm</button> -->  
        <button id="addButton-{{ $order->id }}" class="btn btn-primary btn-sm" onclick="showAddSummaryOrderModal({{ json_encode($order) }})">Thêm</button>
        <span id="addedStatus-{{ $order->id }}" class="text-success" style="display: none;">Đã thêm</span>
    </td>
</tr>
@endforeach
