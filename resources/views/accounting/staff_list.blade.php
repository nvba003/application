@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Danh Sách Nhân Viên</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Ký tự</th>
                <th>Mã KH</th>
                <th>Tham số</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($staff as $member)
            <tr>
                <td>{{ $member->id }}</td>
                <td>{{ $member->name }}</td>
                <td>{{ $member->final_char }}</td>
                <td>{{ $member->customer_code }}</td>
                <td>{{ $member->parameter }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
