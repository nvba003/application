<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function promotions()
    {
        return $this->hasMany(PromotionProduct::class, 'sap_code', 'sap_code');
    }

    public function bonusPromotions()
    {
        return $this->hasMany(Promotion::class, 'bonus_product_id');
    }
}
