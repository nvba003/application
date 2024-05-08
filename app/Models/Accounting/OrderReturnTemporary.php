<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturnTemporary extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function details()
    {
        return $this->hasMany(OrderReturnTemporaryDetail::class, 'order_return_temporary_id');
    }
    public function staff()
    {
        return $this->belongsTo(SaleStaff::class, 'staff_id');
    }
}
