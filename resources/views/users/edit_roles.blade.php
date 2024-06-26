@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Sửa vai trò user: {{ $user->name }}</h1>
    <form action="{{ route('user.update.roles') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="roles">Select Roles:</label>
            <select name="roles[]" id="roles" class="form-control" multiple>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                    <!-- <option value="{{ $role->id }}" {{ $user->roles->contains($role->id) ? 'selected' : '' }}>{{ $role->name }}</option> -->
                @endforeach
            </select>
        </div>
        <input type="hidden" name="id" value="{{ $user->id }}">
        <!-- <div class="form-group">
            <label for="permissions">Select Permissions:</label>
            <select name="permissions[]" id="permissions" class="form-control" multiple>
                @foreach($permissions as $permission)
                    <option value="{{ $permission->id }}" {{ $user->hasPermissionTo($permission->name) ? 'selected' : '' }}>{{ $permission->name }}</option>
                @endforeach
            </select>
        </div> -->

        <button type="submit" class="btn btn-primary">Update Roles</button>
    </form>
</div>
@endsection
