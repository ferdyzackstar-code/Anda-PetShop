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

        static::saving(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::deleting(function ($category) {

            $category->children()->each(function ($child) {
                $child->delete();
            });

            $category->products()->delete(); 
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    public function childrenProducts()
    {
        return $this->hasManyThrough(
            Product::class,     // Model Akhir (Product)
            Category::class,    // Model Perantara (Sub-Category)
            'parent_id',        // FK di model perantara (categories.parent_id)
            'category_id',      // FK di model akhir (products.category_id)
            'id',               // Local key di model ini (categories.id)
            'id'                // Local key di model perantara (sub_categories.id)
        );
    }
}