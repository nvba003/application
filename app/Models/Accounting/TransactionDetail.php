<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $incrementing = false;
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
    public function staff()
    {
        return $this->belongsTo(SaleStaff::class, 'staff_id');
    }
}
