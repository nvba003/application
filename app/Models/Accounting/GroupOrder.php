<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupOrder extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function accountingOrders()
    {
        return $this->hasMany(AccountingOrder::class, 'id', 'order_id'); // Chú ý đến khóa ngoại và khóa chính
    }
    public function summaryOrders()
    {
        // Sử dụng belongsTo nếu mỗi GroupOrder 'thuộc về' một SummaryOrder
        return $this->belongsTo(SummaryOrder::class, 'group_id', 'id');
    }
}
