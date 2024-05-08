<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Xóa cache trước khi bắt đầu để đảm bảo dữ liệu mới nhất được sử dụng
        app()['cache']->forget('spatie.permission.cache');

        // Tạo Permissions
        Permission::create(['name' => 'edit post']);
        Permission::create(['name' => 'delete post']);
        Permission::create(['name' => 'publish post']);
        Permission::create(['name' => 'unpublish post']);
        Permission::create(['name' => 'approve users']); // Thêm permission để phê duyệt người dùng

        // Tạo Roles và gán Permissions
        $role = Role::create(['name' => 'writer']);
        $role->givePermissionTo('edit post');

        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(['publish post', 'unpublish post', 'approve users']); // Gán thêm permission approve users

        // Tạo role unapproved (không cần gán permission)
        Role::create(['name' => 'unapproved']);
    }
}
