<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

    // Relasi untuk mengambil Sub-Kategori
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Relasi untuk mengambil Kategori Induk
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }
}
