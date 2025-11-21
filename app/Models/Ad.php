<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    // [QUAN TRỌNG] Thêm dòng này để cho phép lưu dữ liệu
    protected $fillable = [
        'title',
        'image',
        'link',
        'is_active',
    ];
}