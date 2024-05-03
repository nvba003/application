<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function promotionProducts()
    {
        return $this->hasMany(PromotionProduct::class, 'group_promotion_id', 'group_promotion_id');
    }

    public function promotionGroup()
    {
        return $this->belongsTo(PromotionGroup::class, 'group_promotion_id');
    }

    public function bonusProduct()
    {
        return $this->belongsTo(ProductPrice::class, 'bonus_product_id');
    }
}
