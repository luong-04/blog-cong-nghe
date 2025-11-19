<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    // Định nghĩa quan hệ: Một danh mục có nhiều bài viết (sẽ dùng sau này)
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}