<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Forum extends Model
{
    use SoftDeletes;
    protected $table='forums';

    protected $fillable = [
        'title',
        'content',
        'created_by_name',
        'created_by_id',
        'deleted_at',
    ];

    public function comment()
    {
        return $this->hasMany(ForumComments::class);
    }

    public function created_by(){
        return $this->belongsTo(User::class,'created_by_id','id');
    }
}
