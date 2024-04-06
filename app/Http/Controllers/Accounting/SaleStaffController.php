<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Accounting\SaleStaff; // Đảm bảo dùng đúng namespace của model SaleStaff

class SaleStaffController extends Controller
{
    // Hiển thị danh sách nhân viên bán hàng
    public function index()
    {
        $staff = SaleStaff::all();
        $header = 'Danh sách NVBH';
        return view('accounting.staff_list', compact('staff', 'header'));
    }

    // Hiển thị form tạo nhân viên bán hàng mới
    public function create()
    {
        return view('accounting.sale_staff.create');
    }

    // Lưu nhân viên bán hàng mới
    public function store(Request $request)
    {
        // Validation và logic lưu nhân viên bán hàng
    }

    // Hiển thị form chỉnh sửa nhân viên bán hàng
    public function edit($id)
    {
        $saleStaff = SaleStaff::findOrFail($id);
        return view('accounting.sale_staff.edit', compact('saleStaff'));
    }

    // Cập nhật thông tin nhân viên bán hàng
    public function update(Request $request, $id)
    {
        // Validation và logic cập nhật nhân viên bán hàng
    }

    // Xóa nhân viên bán hàng
    public function destroy($id)
    {
        // Logic xóa nhân viên bán hàng
    }
}
