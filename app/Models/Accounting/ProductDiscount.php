<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDiscount extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function recoveryOrderDetails()
    {
        return $this->hasMany(RecoveryOrderDetail::class, 'product_code', 'product_code');
    }
}
