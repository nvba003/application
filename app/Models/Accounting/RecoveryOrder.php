<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecoveryOrder extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function recoveryDetails()
    {
        return $this->hasMany(RecoveryOrderDetail::class,'recovery_order_id');
    }
}
