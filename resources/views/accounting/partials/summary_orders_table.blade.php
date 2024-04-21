@foreach ($summaryOrders as $summaryOrder)
    <tr>
        <td>
            <div class="checkbox-container">
                <input type="checkbox" class="order-checkbox checkItem" value="{{ $summaryOrder->id }}" data-id="{{ $summaryOrder->id }}" data-staff-name="{{ $summaryOrder->staff }}">
            </div>
        </td>
        <td>
            <button class="btn btn-info btn-sm expand-button" data-target="#details{{ $summaryOrder->id }}" data-summary-order="{{ $summaryOrder }}">+</button>
        </td>
        <td>{{ \Carbon\Carbon::parse($summaryOrder->report_date)->format('d/m/Y') }}</td>
        <td>
            <!-- Hiển thị thông tin nhân viên dựa trên group_order -->
            @if($summaryOrder->groupOrder)
                @php
                    $groupOrder = $summaryOrder->groupOrder->first();
                @endphp
                @if($groupOrder->accountingOrders->isNotEmpty())
                    {{ $groupOrder->accountingOrders->first()->staff ?? '_' }}
                @elseif($groupOrder->recoveryOrders->isNotEmpty())
                    {{ $groupOrder->recoveryOrders->first()->staff ?? '_' }}
                @else
                    '_'
                @endif
            @else
                '_'
            @endif
        </td>
        <td style="display: none;">{{ $summaryOrder->invoice_code ?? '_' }}</td>
        <td>{{ $summaryOrder->transaction_id ?? '_' }}</td>
        <td>
            <!-- Tính toán tổng giá trị discount-->
            @if($summaryOrder->groupOrder->isNotEmpty())
                @php
                    $totalDiscount = 0;
                    if($summaryOrder->groupOrder->first()->accountingOrders->isNotEmpty()) {
                        $totalDiscount = $summaryOrder->groupOrder->sum(function($group) {
                            return $group->accountingOrders->sum('discount');
                        });
                    }
                    elseif($summaryOrder->groupOrder->first()->recoveryOrders->isNotEmpty()) {
                        $totalDiscount = $summaryOrder->groupOrder->sum(function($group) {
                            return $group->recoveryOrders->sum('discount');
                        });
                    }
                @endphp

                <!-- Hiển thị tổng giảm giá và tổng số tiền, định dạng bằng number_format -->
                {{ number_format($totalDiscount) }}
            @else
                '_'
            @endif
        </td>
        <td>
            <!-- Tính toán tổng giá trị total_amount -->
            @if($summaryOrder->groupOrder->isNotEmpty())
                @php
                    $totalAmount = 0;
                    if($summaryOrder->groupOrder->first()->accountingOrders->isNotEmpty()) {
                        $totalAmount = $summaryOrder->groupOrder->sum(function($group) {
                            return $group->accountingOrders->sum('total_amount');
                        });
                    }
                    elseif($summaryOrder->groupOrder->first()->recoveryOrders->isNotEmpty()) {
                        $totalAmount = $summaryOrder->groupOrder->sum(function($group) {
                            return $group->recoveryOrders->sum('total_amount');
                        });
                    }
                @endphp
                <!-- Hiển thị tổng giảm giá và tổng số tiền, định dạng bằng number_format -->
                {{ number_format($totalAmount) }}
            @else
                '_'
            @endif
        </td>
        <td>
            @if ($summaryOrder->is_group)
                Giao ngay
            @elseif ($summaryOrder->is_recovery)
                Thu hồi
            @else
                Giao sau
            @endif
        </td>
        <td class="max-width-td">
            <!-- Hiển thị thông tin khách hàng dựa trên group_order -->
            @if($summaryOrder->groupOrder)
                @php
                    $groupOrder = $summaryOrder->groupOrder->first();
                @endphp
                @if($groupOrder->accountingOrders->isNotEmpty() && !$summaryOrder->is_group && !$summaryOrder->is_recovery)
                    {{ $groupOrder->accountingOrders->first()->customer_name }}
                @else
                    {{ $groupOrder->recoveryOrders->first()->customer_name }}
                @endif
            @endif
        </td>
        <td>{{ $summaryOrder->notes }}</td>
        <td>
            <button class="btn btn-primary btn-sm btn-edit" data-order="{{ $summaryOrder }}">Sửa</button>
            <!-- @if($summaryOrder->is_entered == false)
                <button class="btn btn-success btn-sm btn-enter" data-id="{{ $summaryOrder->id }}" data-entered="{{ $summaryOrder->is_entered }}">Nhập</button>
            @else
                <span>Đã nhập</span>
            @endif -->
        </td>
    </tr>
    <!-- Chi tiết đơn hàng -->
    <tr id="details{{ $summaryOrder->id }}" class="details-row" style="display: none;">
        <td colspan="11">
            <div id="productDetails{{ $summaryOrder->id }}" class="product-details-container">
            </div>
        </td>
    </tr>

@endforeach
