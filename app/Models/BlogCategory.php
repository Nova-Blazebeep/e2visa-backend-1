<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $table='blog_categories';
    protected $fillable = [
        'name',
    ];

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }

    public function getBlogCount()
{
    return $this->blogs()->count();
}
}
