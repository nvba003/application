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
    <!-- Chi tiết đơn hàng thu hồi -->
    <tr id="orderDetails{{ $order->id }}" style="display: none;">
        <td colspan="6">
            <div class="table-responsive">
                <table class="table table-striped table-hover"> <!-- Thêm table-hover cho hiệu ứng khi di chuột qua từng dòng -->
                    <thead class="bg-info text-white">
                        <tr>
                            <th>Mã Sản Phẩm</th>
                            <th>Tên Sản Phẩm</th>
                            <th>Số Lượng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->recoveryDetails as $detail)
                        <tr>
                            <td>{{ $detail->product_code }}</td>
                            <td>{{ $detail->product_name }}</td>
                            <td>{{ $detail->packing * $detail->thung + $detail->le }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </td>
    </tr>

    <tr id="searchFilter{{ $order->id }}" class="collapse">
        <td colspan="100%">
            <div class="container-fluid bg-success p-3">
                <form class="form-inline">
                    <div class="form-group mr-2">
                        <label for="customer_name{{ $order->id }}" class="mr-1 text-light">Tên KH:</label> <!-- Text màu xanh lá -->
                        <input type="text" class="form-control form-control-sm" id="customer_name{{ $order->id }}" name="customer_name">
                    </div>
                    
                    <div class="form-group mr-2">
                        <label for="phone{{ $order->id }}" class="mr-1 text-light">ĐT:</label> <!-- Text màu xanh lá -->
                        <input type="text" class="form-control form-control-sm" id="phone{{ $order->id }}" name="phone">
                    </div>

                    <div class="form-group mr-2">
                        <label for="from_date{{ $order->id }}" class="mr-1 text-light">Từ ngày:</label> <!-- Text màu xanh lá -->
                        <input type="date" class="form-control form-control-sm" id="from_date{{ $order->id }}" name="from_date">
                    </div>

                    <div class="form-group mr-2">
                        <label for="to_date{{ $order->id }}" class="mr-1 text-light">Đến ngày:</label> <!-- Text màu xanh lá -->
                        <input type="date" class="form-control form-control-sm" id="to_date{{ $order->id }}" name="to_date">
                    </div>
                    <button type="button" class="btn btn-warning btn-sm btn-filter" data-order-id="{{ $order->id }}" data-order="{{ $order }}">Tìm SĐT</button>
                </form>
            </div>
        </td>
    </tr>
    <tr id="filterDetails{{ $order->id }}" class="collapse">
        <td colspan="100%">
            <div class="container-fluid" style="background-color: #d2e9d7;">
                <!-- Nội dung chi tiết đơn hàng sẽ được đổ vào đây bằng JavaScript -->
            </div>
        </td>
    </tr>

</tr>
@endforeach