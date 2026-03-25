<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        // 1. Logika Auto-Slug: Dijalankan saat data akan disimpan (create/update)
        static::saving(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });

        // 2. Logika Cascading Delete: Dijalankan sebelum data dihapus
        static::deleting(function ($category) {
            // Kita ambil semua anak (sub-kategori) dan hapus satu per satu
            // Ini akan memicu event 'deleting' di level anak juga (rekursif)
            $category->children()->each(function ($child) {
                $child->delete();
            });
        });
    }

    // Scope untuk filter kategori aktif saja
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

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
