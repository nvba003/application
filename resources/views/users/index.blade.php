@extends('layouts.app')

@section('content')
@if(auth()->user()->hasRole('admin'))

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
                    <!-- <a href="{{ route('user.edit.roles', $user->id) }}" class="btn btn-primary btn-sm">Chỉnh Sửa Roles/Permissions</a> -->
                    <form method="GET" action="{{ route('user.edit.roles') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $user->id }}">
                        <button type="submit" class="btn btn-primary btn-sm">Chỉnh Sửa Roles</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
@endsection
