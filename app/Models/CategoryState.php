<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryState extends Model
{
    protected $fillable = ['category_id', 'title_key', 'code', 'price', 'sort', 'active'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
