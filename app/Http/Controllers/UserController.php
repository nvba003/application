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
    public function editRoles(Request $request)
    {
        // Lấy ID từ request và lưu vào session
        if ($request->has('id')) {
            session(['edit_user_id' => $request->id]);
        }
        // Lấy ID từ session
        $id = session('edit_user_id');
        $user = User::findOrFail($id);
        $roles = Role::all();
        $permissions = Permission::all();
        $header = 'Chỉnh sửa quyền người dùng';

        return view('users.edit_roles', compact('user', 'roles', 'permissions', 'header'));
    }

    // Xử lý cập nhật roles và permissions từ form
    public function updateRoles(Request $request)
    {
        //dd($request->roles);
        if ($request->has('id')) {
            session(['edit_user_id' => $request->id]);
        }
        // Lấy ID từ session
        $id = session('edit_user_id');
        $user = User::findOrFail($id);
        // Validate các dữ liệu đầu vào nếu cần
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id', // Đảm bảo các ID role tồn tại trong bảng roles
        ]);
        // Đồng bộ hóa vai trò cho người dùng theo tên Roles
        // $user->syncRoles($request->roles);
        $roleNames = Role::whereIn('id', $request->roles)->pluck('name')->toArray();// Lấy tên các roles từ ID
        $user->syncRoles($roleNames);// Đồng bộ hóa vai trò cho người dùng
        // Kiểm tra nếu có permissions trong request
        // if ($request->has('permissions')) {
        //     $request->validate([
        //         'permissions' => 'nullable|array',
        //         'permissions.*' => 'exists:permissions,id', // Đảm bảo các ID permission tồn tại trong bảng permissions
        //     ]);
        //     $user->syncPermissions($request->permissions);
        // } else {
        //     $user->syncPermissions([]); // Xóa hết permissions nếu không có permission nào được gửi
        // }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    // Các phương thức khác như create, update, delete users...
}
