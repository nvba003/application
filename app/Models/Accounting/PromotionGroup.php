<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionGroup extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function promotion()
    {
        return $this->hasMany(Promotion::class, 'group_promotion_id');
    }
}
