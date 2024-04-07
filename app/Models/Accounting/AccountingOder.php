<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingOder extends Model
{
    use HasFactory;
    protected $guard = ['id'];
    public function orderDetails()
    {
        return $this->hasOne(AccountingOrderDetail::class);
    }

}
