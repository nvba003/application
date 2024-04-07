<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingOrder extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function orderDetails()
    {
        return $this->hasOne(AccountingOrderDetail::class);
    }

}
