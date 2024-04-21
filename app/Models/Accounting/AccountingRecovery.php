<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingRecovery extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function recoveryDetails()
    {
        return $this->hasMany(AccountingRecoveryDetail::class,'recovery_order_id');
    }
    public function groupOrder()
    {
        return $this->belongsTo(GroupOrder::class, 'id', 'recovery_id');
    }
    public function staffs()
    {
        return $this->hasMany(AccountingRecoveryStaff::class, 'recovery_code', 'recovery_code');
    }
}
