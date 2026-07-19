<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'weight',
        'thumbnail',
        'is_active',
        'is_flash_sale',
        'flash_sale_price',
        'flash_sale_end'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?: 0;
    }

    public function getReviewCountAttribute()
    {
        return $this->reviews()->count();
    }
    
    public function getTotalStockAttribute()
    {
        return $this->variants()->sum('stock');
    }

    public function getIsFlashSaleActiveAttribute()
    {
        return $this->is_flash_sale && $this->flash_sale_end && \Carbon\Carbon::now()->lt($this->flash_sale_end);
    }

    public function getCurrentPriceAttribute()
    {
        return $this->is_flash_sale_active ? $this->flash_sale_price : $this->price;
    }
}
