<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleStaff extends Model
{
    use HasFactory;
    protected $guard = ['id'];
    protected $table = 'sale_staffs'; // Chỉ định tên bảng
}
