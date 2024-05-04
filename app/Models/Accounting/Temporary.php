<?php

namespace App\Models\Accounting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temporary extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function details()
    {
        return $this->hasMany(TemporaryDetail::class);
    }
    public function staff()
    {
        return $this->belongsTo(SaleStaff::class, 'staff');
    }
}
