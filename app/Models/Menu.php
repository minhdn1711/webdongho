<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    protected $fillable = [
        'label',
        'source_type',
        'source_id',
        'url',
        'sort_order',
        'is_active',
        'open_new_tab',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'open_new_tab' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'source_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'source_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'source_id');
    }

    public function getResolvedUrlAttribute(): ?string
    {
        return match ($this->source_type) {
            'category' => $this->category ? route('category.show', $this->category->slug) : null,
            'product' => $this->product ? route('product.show', $this->product->slug) : null,
            'post' => $this->post ? route('news.show', $this->post->slug) : null,
            default => $this->url,
        };
    }
}
