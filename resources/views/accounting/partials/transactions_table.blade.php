@foreach ($transactions as $transaction)
<tr>
    <td>
        <!-- Sử dụng data-target để chỉ định ID của phần chi tiết cần mở rộng -->
        <button class="btn btn-info btn-sm expand-button" data-target="#details{{ $transaction->id }}">+</button>
    </td>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
    <td>{{ $transaction->staff->name ?? '_' }}</td>
    <td>{{ $transaction->submitter->name ?? '_' }}</td>
    <td>{{ number_format($transaction->transfer_amount) }}</td>
    <td>{{ number_format($transaction->total_amount) }}</td>
    <td>{{ $transaction->id ?? '_' }}</td>
</tr>
<!-- Thêm ID tương ứng với data-target của nút vào đây -->
<tr class="transaction-detail" id="details{{ $transaction->id }}" style="display:none;">
    <td colspan="8">
        <table class="table">
            <thead>
                <tr>
                    <th>Mệnh giá</th>    
                    <th>500k</th>
                    <th>200k</th>
                    <th>100k</th>
                    <th>50k</th>
                    <th>20k</th>
                    <th>10k</th>
                    <th>5k</th>
                    <th>2k</th>
                    <th>1k</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Số tờ</td>
                    <td>{{ $transaction->note_500 }} </td>
                    <td>{{ $transaction->note_200 }} </td>
                    <td>{{ $transaction->note_100 }} </td>
                    <td>{{ $transaction->note_50 }} </td>
                    <td>{{ $transaction->note_20 }} </td>
                    <td>{{ $transaction->note_10 }} </td>
                    <td>{{ $transaction->note_5 }} </td>
                    <td>{{ $transaction->note_2 }} </td>
                    <td>{{ $transaction->note_1 }} </td>
                </tr>
            </tbody>
        </table>
        <strong>Ghi Chú:</strong> {{ $transaction->notes }}
    </td>
</tr>
@endforeach

