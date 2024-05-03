<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTemporary extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function details()
    {
        return $this->hasMany(OrderTemporaryDetail::class, 'order_temporary_id');
    }
    public function staff()
    {
        return $this->belongsTo(SaleStaff::class, 'staff_id');
    }
}
