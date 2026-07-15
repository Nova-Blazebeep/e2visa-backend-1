<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ForumCommentReplies;
use Illuminate\Http\Request;

class ForumCommentsRepliesController extends Controller
{
    public function listReplies(Request $request)
    {
        $rules = [
            'comment_id' => 'required|exists:forum_comments,id',
        ];
        ValidateApiRequest($rules, $request->all());

        try {
            $replies = ForumCommentReplies::where('comment_id', $request->comment_id)
                ->orderBy('id', 'desc')
                ->get();
            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $replies);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }
    public function addReply(Request $request)
    {
        $rules = [
            'forum_id' => 'required|exists:forums,id',
            'comment_id' => 'required|exists:forum_comments,id',
            'content' => 'required|string',
        ];
        ValidateApiRequest($rules, $request->all());

        try {
            $user = auth()->user();
            $reply = ForumCommentReplies::create([
                'forum_id' => $request->forum_id,
                'comment_id' => $request->comment_id,
                'content' => $request->content,
                'created_by_name' => $user->name,
                'created_by_id' => $user->id,
            ]);
            $reply=ForumCommentReplies::with('user')->where('id',$reply->id)->get();

            return makeResponse(SUCCESS_CODE, 'Reply added successfully!', $reply);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }
}
