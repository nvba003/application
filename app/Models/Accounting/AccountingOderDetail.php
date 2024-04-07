<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingOderDetail extends Model
{
    use HasFactory;
    protected $guard = ['id'];
    public function order()
    {
        return $this->belongsTo(AccountingOder::class);
    }

}
