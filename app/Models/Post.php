<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'content',
        'featured_image',
        'status',     // 'draft' hoặc 'published'
        'view_count',
    ];

    // Quan hệ: Bài viết thuộc về một Tác giả (User)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ: Bài viết thuộc về một Danh mục
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Quan hệ: Bài viết có nhiều Thẻ (Tags)
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    // Quan hệ: Bài viết có nhiều Bình luận
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}