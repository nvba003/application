<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderReturnTemporaryDetail extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function temporaryOrderReturn()
    {
        return $this->belongsTo(OrderReturnTemporary::class, 'order_return_temporary_id');
    }
}
