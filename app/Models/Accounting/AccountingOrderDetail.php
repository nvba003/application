<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingOrderDetail extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function order()
    {
        return $this->belongsTo(AccountingOrder::class, 'order_id');
    }

}
