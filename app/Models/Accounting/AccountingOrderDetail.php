<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingOrderDetail extends Model
{
    use HasFactory;
    protected $guard = ['id'];
    public function order()
    {
        return $this->belongsTo(AccountingOrder::class);
    }

}
