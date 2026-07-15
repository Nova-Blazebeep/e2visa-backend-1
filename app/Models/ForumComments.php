<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumComments extends Model
{
    use SoftDeletes;
    protected $table = 'forum_comments';

    protected $fillable = [
        'forum_id',
        'content',
        'created_by_name',
        'created_by_id',
        'deleted_at',
    ];

    public function forum()
    {
        return $this->belongsTo(Forum::class);
    }

    public function replies()
    {
       return $this->hasMany(ForumCommentReplies::class, 'comment_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'created_by_id');
    }
}
