<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionProduct extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    // public function promotion()
    // {
    //     return $this->belongsTo(Promotion::class, 'group_promotion_id', 'group_promotion_id');
    // }

    public function promotionGroup()
    {
        return $this->belongsTo(promotionGroup::class, 'group_promotion_id');
    }

    public function productPrice()
    {
        return $this->belongsTo(ProductPrice::class, 'sap_code', 'sap_code');
    }
}
