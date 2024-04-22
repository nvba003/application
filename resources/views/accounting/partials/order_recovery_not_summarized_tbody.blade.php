@foreach($recoveryOrders as $order)
<tr data-order='{{ json_encode($order) }}'>
    <td><div class="checkbox-container">
        <input type="checkbox" class="order-checkbox checkItem" value="{{ $order->id }}">
        </div>
    </td>
    <td>
        <button class="btn btn-info btn-sm expand-button" data-target="#details{{ $order->id }}" data-order-id="{{ $order->id }}" data-order="{{ $order }}">+</button>
    </td>
    <td>{{ $loop->iteration }}</td>
    <td>{{ \Carbon\Carbon::parse($order->recovery_date)->format('d/m/Y') }}</td>
    <td>{{ $order->recovery_code }}</td>
    <td>{{ $order->staffs[0]->staff }}</td>
    <td>{{ $order->customer_name }}</td>
    <td>{{ number_format($order->total_discount) }}</td>
    <td>{{ number_format($order->total_discounted_amount) }}</td>

    <tr id="searchFilter{{ $order->id }}" class="collapse">
        <td colspan="100%"> <!-- Sử dụng colspan để chiếm toàn bộ chiều rộng của bảng -->
            <form class="form-inline">
                <div class="form-group mr-2">
                    <label for="customer_name{{ $order->id }}" class="mr-1">Tên KH:</label>
                    <input type="text" class="form-control form-control-sm" id="customer_name{{ $order->id }}" name="customer_name">
                </div>
                
                <div class="form-group mr-2">
                    <label for="phone{{ $order->id }}" class="mr-1">ĐT:</label>
                    <input type="text" class="form-control form-control-sm" id="phone{{ $order->id }}" name="phone">
                </div>

                <div class="form-group mr-2">
                    <label for="from_date{{ $order->id }}" class="mr-1">Từ ngày:</label>
                    <input type="date" class="form-control form-control-sm" id="from_date{{ $order->id }}" name="from_date">
                </div>

                <div class="form-group mr-2">
                    <label for="to_date{{ $order->id }}" class="mr-1">Đến ngày:</label>
                    <input type="date" class="form-control form-control-sm" id="to_date{{ $order->id }}" name="to_date">
                </div>
                <button type="button" class="btn btn-primary btn-filter btn-sm" data-order="{{ $order }}">Tìm kiếm</button>
            </form>
        </td>
    </tr>
</tr>
@endforeach