<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Đảm bảo sử dụng model User phù hợp với cấu trúc thư mục của bạn
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    // Hiển thị danh sách users
    public function index()
    {
        $users = User::all();
        $header = 'Quản lý người dùng';
        return view('users.index', compact('users','header'));
    }

    // Hiển thị form chỉnh sửa roles và permissions cho một user
    public function editRoles($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        $permissions = Permission::all();
        return view('users.edit_roles', compact('user', 'roles', 'permissions'));
    }

    // Xử lý cập nhật roles và permissions từ form
    public function updateRoles(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->syncRoles($request->roles);
        $user->syncPermissions($request->permissions);

        return redirect()->route('users.index')->with('success', 'Roles and permissions updated successfully.');
    }

    // Các phương thức khác như create, update, delete users...
}
