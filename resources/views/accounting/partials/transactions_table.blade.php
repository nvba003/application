@foreach ($transactions as $transaction)
<tr>
    <td>
        <div class="checkbox-container">
            <input type="checkbox" class="order-checkbox checkItem" value="{{ $transaction->id }}" data-id="{{ $transaction->id }}">
        </div>
    </td>    
    <td>
        <!-- Sử dụng data-target để chỉ định ID của phần chi tiết cần mở rộng -->
        <button class="btn btn-info btn-sm expand-button" data-target="#details{{ $transaction->id }}">+</button>
    </td>
    <td>{{ $loop->iteration }}</td>
    <td>{{ \Carbon\Carbon::parse($transaction->pay_date)->format('d/m/Y') }}</td>
    <td>{{ $transaction->staff->name ?? '_' }}</td>
    <td>{{ number_format($transaction->total_amount) }}</td>
    <td class="diff-amount">{{ number_format($transaction->diff_amount) }}</td>
    <td>{{ $transaction->notes ?? '_' }}</td>
    <td>{{ $transaction->id }}</td>
    <td>
        <button id="addButton-{{ $transaction->id }}" data-staff-id="{{ $transaction->staff_id }}" class="btn btn-primary btn-sm" onclick="showAddTransactionModal({{ json_encode($transaction) }})">Nộp thêm</button>
    </td>
</tr>
<!-- Thêm ID tương ứng với data-target của nút vào đây -->
<tr class="transaction-detail" id="details{{ $transaction->id }}" style="display:none;">
    <td colspan="10">
        <table class="table">
            <thead>
                <tr>
                    <th>STT</th>   
                    <th>Ngày ghi</th> 
                    <th>Số GD</th>
                    <th>Người nộp</th>
                    <th>Chuyển khoản</th>
                    <th>Tiền mặt</th>
                    <th>Tổng</th>
                    <th>Ghi chú</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->details as $index => $detail)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $detail->created_at }}</td>
                        <td>{{ $detail->id }}</td>
                        <td>{{ $detail->staff->name }}</td>
                        <td class="text-right">{{ number_format($detail->transfer_amount) }}</td>
                        <td class="text-right">{{ number_format($detail->total_amount - $detail->transfer_amount) }}</td>
                        <td class="text-right">{{ number_format($detail->total_amount) }}</td>
                        <td>{{ $detail->notes }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <strong>Ghi Chú:</strong> {{ $transaction->notes }}
    </td>
</tr>
@endforeach

