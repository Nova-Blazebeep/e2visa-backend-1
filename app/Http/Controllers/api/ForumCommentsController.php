<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ForumComments;
use Illuminate\Http\Request;

class ForumCommentsController extends Controller
{
    public function listComments(Request $request)
    {
        $rules = [
            'forum_id' => 'required|exists:forums,id',
        ];
        ValidateApiRequest($rules, $request->all());

        try {
            $comments = ForumComments::with('replies')
                ->where('forum_id', $request->forum_id)
                ->orderBy('id', 'desc')
                ->get();
            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $comments);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function addComment(Request $request)
    {
        $rules = [
            'forum_id' => 'required|exists:forums,id',
            'content' => 'required|string',
        ];
        ValidateApiRequest($rules, $request->all());

        try {
            $user = auth()->user();
            $comment = ForumComments::create([
                'forum_id' => $request->forum_id,
                'content' => $request->content,
                'created_by_name' => $user->name,
                'created_by_id' => $user->id,
            ]);
            $comment=ForumComments::with('user')->where('id',$comment->id)->get();
            

            return makeResponse(SUCCESS_CODE, 'Comment added successfully!', $comment);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }
}
