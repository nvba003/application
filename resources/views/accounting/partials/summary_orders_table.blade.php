@foreach ($summaryOrders as $summaryOrder)
    <tr>
        <td>
            <div class="checkbox-container">
                @if(is_null($summaryOrder->transaction_id))
                    <input type="checkbox" class="order-checkbox" value="{{ $summaryOrder->id }}" data-id="{{ $summaryOrder->id }}">
                @else
                    <i class="fa fa-check text-success"></i>
                @endif
            </div>
        </td>
        <td>
            <button class="btn btn-info btn-sm expand-button" data-target="#details{{ $summaryOrder->id }}" data-summary-order="{{ $summaryOrder }}">+</button>
        </td>
        <td>{{ \Carbon\Carbon::parse($summaryOrder->report_date)->format('Y-m-d') }}</td>
        <td>
            <!-- Hiển thị thông tin nhân viên dựa trên group_order -->
            @if ($summaryOrder->groupOrder)
                {{ $summaryOrder->groupOrder->first()->accountingOrders->first()->staff ?? '_' }}
            @else
                '_'
            @endif
        </td>
        <td>{{ $summaryOrder->invoice_code ?? '_' }}</td>
        <td>{{ $summaryOrder->transaction_id ?? '_' }}</td>
        <td>
            <!-- Tính toán tổng giá trị discount-->
            @if ($summaryOrder->groupOrder)
                @php
                    $totalDiscount = $summaryOrder->groupOrder->sum(function($group) {
                        return $group->accountingOrders->sum('discount');
                    });
                    $totalAmount = $summaryOrder->groupOrder->sum(function($group) {
                        return $group->accountingOrders->sum('total_amount');
                    });
                @endphp
                {{ number_format($totalDiscount) }}
            @else
                '_'
            @endif
        </td>
        <td>
            <!-- Tính toán tổng giá trị total_amount -->
            @if ($summaryOrder->groupOrder)
                @php
                    $totalDiscount = $summaryOrder->groupOrder->sum(function($group) {
                        return $group->accountingOrders->sum('discount');
                    });
                    $totalAmount = $summaryOrder->groupOrder->sum(function($group) {
                        return $group->accountingOrders->sum('total_amount');
                    });
                @endphp
                {{ number_format($totalAmount) }}
            @else
                '_'
            @endif
        </td>
        <td>
            @if ($summaryOrder->is_group)
                GN
            @endif
        </td>
        <td>{{ $summaryOrder->notes }}</td>
        <td>
            <button class="btn btn-primary btn-edit" data-order="{{ $summaryOrder }}">Sửa</button>
            @if($summaryOrder->is_entered == false)
                <button class="btn btn-success btn-enter" data-id="{{ $summaryOrder->id }}" data-entered="{{ $summaryOrder->is_entered }}">Nhập</button>
            @else
                <span>Đã nhập</span>
            @endif
        </td>
    </tr>
    <!-- Chi tiết đơn hàng -->
    <tr id="details{{ $summaryOrder->id }}" class="details-row" style="display: none;">
        <td colspan="10">
            <div id="productDetails{{ $summaryOrder->id }}" class="product-details-container">
            </div>
        </td>
    </tr>

@endforeach
