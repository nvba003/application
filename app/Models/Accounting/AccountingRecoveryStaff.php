<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingRecoveryStaff extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $table = 'accounting_recovery_staffs';

    public function recovery()
    {
        return $this->belongsTo(AccountingRecovery::class, 'recovery_code', 'recovery_code');
    }

}
