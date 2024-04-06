@extends('layouts.app') {{-- Giả sử bạn có một layout chung tên là app --}}

@section('content')
<div class="container">
    <h1>Quản Lý Users</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Email</th>
                <th>Hành Động</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <a href="{{ route('user.edit.roles', $user->id) }}" class="btn btn-primary btn-sm">Chỉnh Sửa Roles/Permissions</a>
                    {{-- Các nút hành động khác như Edit, Delete --}}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
