<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumCommentReplies extends Model
{
    use SoftDeletes;
    protected $table = 'forum_comments_replies';

    protected $fillable = [
        'forum_id',
        'comment_id',
        'content',
        'created_by_name',
        'created_by_id',
        'deleted_at',
        'create_by_image'
    ];

    public function comment()
    {
        return $this->belongsTo(ForumComments::class);
    }
    public function user(){
        return $this->belongsTo(User::class,'created_by_id');
    }
}
