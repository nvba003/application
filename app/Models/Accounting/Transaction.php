<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    // Mối quan hệ với SaleStaff cho 'staff'
    public function staff()
    {
        return $this->belongsTo(SaleStaff::class, 'staff_id');
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
