<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes; // Kích hoạt nếu sử dụng Soft Deletes

class Menu extends Model
{
    use HasFactory;
    // use SoftDeletes; // Kích hoạt nếu sử dụng Soft Deletes

    protected $fillable = ['name', 'url', 'icon', 'parent_id'];

    public function children() {
        return $this->hasMany(Menu::class, 'parent_id');
    }

    public function parent() {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    // Thêm Accessors, Mutators, và phương thức tùy chỉnh ở đây
}