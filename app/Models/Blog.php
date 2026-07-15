<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'banner',
        'content',
        'category_id',
        'created_by',
        'custom_author',
        'is_archived',
        'is_deleted',
        'is_active',
        'is_pinned',
        'active_date',
    ];
    public function category()
    {
        return $this->belongsTo(BlogCategory::class);
    }
     public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class, 'blog_id');
    }
}
