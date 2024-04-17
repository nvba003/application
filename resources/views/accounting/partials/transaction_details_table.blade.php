@php
    $detailIndex = 1;
@endphp
@foreach ($transactions as $transaction)
    @foreach ($transaction['details'] as $detail)
        <tr>
            <td>
                <div class="checkbox-container">
                    <input type="checkbox" class="order-checkbox checkItem" value="{{ $transaction->id }}" data-id="{{ $transaction->id }}">
                </div>
            </td> 
            <td>{{ $detailIndex }}</td>
            <td>{{ \Carbon\Carbon::parse($transaction->pay_date)->format('d/m/Y') }}</td>
            <td>{{ $transaction->staff->name ?? '_' }}</td>
            <td>{{ $transaction->id }}</td>
            <td class="text-right">{{ number_format($detail['transfer_amount']) }}</td>
            <td class="text-right">{{ number_format($detail['cash'] ?? 0) }}</td>
            <td class="text-right">{{ number_format($detail->total_amount) }}</td>
            <td>{{ $detail->notes ?? '_' }}</td>
        </tr>
        @php
            $detailIndex++;
        @endphp
    @endforeach
@endforeach

