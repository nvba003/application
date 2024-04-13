<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleStaff extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'sale_staffs'; // Chỉ định tên bảng

    // Staff có thể phụ trách nhiều Transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'staff_id');
    }

    // Staff có thể phụ trách nhiều TransactionDetails
    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class, 'staff_id');
    }
}
