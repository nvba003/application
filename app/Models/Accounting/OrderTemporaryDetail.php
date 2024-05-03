<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTemporaryDetail extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function temporaryOrder()
    {
        return $this->belongsTo(OrderTemporary::class, 'order_temporary_id');
    }
}
