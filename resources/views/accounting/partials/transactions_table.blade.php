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
    <td class="max-width-td">{{ $transaction->customer_name ?? '_' }}</td>
    <td>{{ $transaction->id }}</td>
    <td>
        <button id="addButton-{{ $transaction->id }}" data-staff-id="{{ $transaction->staff_id }}" class="btn btn-primary btn-sm" onclick="showAddTransactionModal({{ json_encode($transaction) }})">Nộp thêm</button>
        <button class="btn btn-secondary btn-sm btn-edit" data-transaction="{{ $transaction }}">Sửa</button>
    </td>
</tr>
<!-- Thêm ID tương ứng với data-target của nút vào đây -->
<tr class="transaction-detail" id="details{{ $transaction->id }}" style="display:none;">
    <td colspan="11">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>   
                    <th>Ngày ghi</th> 
                    <th>Số GD chi tiết</th>
                    <th>Người nộp</th>
                    <th class="text-right">C.Khoản</th>
                    <th class="text-right">500</th>
                    <th class="text-right">200</th>
                    <th class="text-right">100</th>
                    <th class="text-right">50</th>
                    <th class="text-right">20</th>
                    <th class="text-right">10</th>
                    <th class="text-right">5</th>
                    <th class="text-right">2</th>
                    <th class="text-right">1</th>
                    <th class="text-right">T.Mặt</th>
                    <th class="text-right">Tổng</th>
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
                        <td class="text-right">{{ $detail->note_500 }}</td>
                        <td class="text-right">{{ $detail->note_200 }}</td>
                        <td class="text-right">{{ $detail->note_100 }}</td>
                        <td class="text-right">{{ $detail->note_50 }}</td>
                        <td class="text-right">{{ $detail->note_20 }}</td>
                        <td class="text-right">{{ $detail->note_10 }}</td>
                        <td class="text-right">{{ $detail->note_5 }}</td>
                        <td class="text-right">{{ $detail->note_2 }}</td>
                        <td class="text-right">{{ $detail->note_1 }}</td>
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

