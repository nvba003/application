<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Accounting\SaleStaff;

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
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'final_char' => 'string|nullable',
            'customer_code' => 'string|nullable',
            'parameter' => 'string|nullable',
        ]);

        $member = SaleStaff::findOrFail($id);
        $member->update($validatedData);

        return redirect()->route('sale-staff')->with('success', 'Thông tin nhân viên đã được cập nhật.');
    }


    // Xóa nhân viên bán hàng
    public function destroy($id)
    {
        // Logic xóa nhân viên bán hàng
    }
}
