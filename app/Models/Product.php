<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 
        'name', 
        'short_description',
        'slug', 
        'description', 
        'price', 
        'sale_price', 
        'image', 
        'is_featured', 
        'stock',
        'sku',
        'barcode'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function stockHistories()
    {
        return $this->hasMany(StockHistory::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

}
