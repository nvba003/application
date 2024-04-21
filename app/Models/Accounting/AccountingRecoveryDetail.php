<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingRecoveryDetail extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function order()
    {
        return $this->belongsTo(AccountingRecovery::class, 'recovery_order_id');
    }
    public function productDiscount()
    {
        return $this->hasOne(ProductDiscount::class, 'product_code', 'product_code');
    }
}
