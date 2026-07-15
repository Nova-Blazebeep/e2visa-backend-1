<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Forum;
use App\Models\ForumComments;
use App\Models\ForumCommentReplies;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function list(Request $request)
    {
        try {
            $forums = Forum::with([
                'comment',
                'created_by.roleBadge:id,role_id,icon' // only fetch needed columns
            ])->withCount('comment')->with('created_by')->orderBy('id', 'desc')->paginate(6);


            foreach ($forums as $forum) {
                $forum->creator_badge_icon = optional(optional($forum->created_by)->roleBadge)->icon;
            }

            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $forums);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function detail($id)
    {
        try {
            $forum = Forum::with([
                'created_by' => function ($q) {
                    $q->with('roleBadge:id,role_id,icon');
                },
                'comment.user' => function ($q) {
                    $q->with('roleBadge:id,role_id,icon');
                },
                'comment.replies.user' => function ($q) {
                    $q->with('roleBadge:id,role_id,icon');
                },
            ])
                ->withCount('comment')
                ->findOrFail($id);

            $forum->creator_badge_icon = optional(optional($forum->created_by)->roleBadge)->icon ?? null;
            foreach ($forum->comment ?? [] as $comment) {
                $comment->commentor_badge_icon = optional(optional($comment->user)->roleBadge)->icon ?? null;
                foreach ($comment->replies ?? [] as $reply) {
                    $reply->replier_badge_icon = optional(optional($reply->user)->roleBadge)->icon ?? null;
                }
            }

            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $forum);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function createForum(Request $request)
    {
        $rules = [
            'title' => 'required|string',
            'content' => 'required|string',
        ];
        ValidateApiRequest($rules, $request->all());

        try {
            $user = auth()->user();

            $forum = Forum::create([
                'title' => $request->title,
                'content' => $request->content,
                'created_by_name' => $user->name,
                'created_by_id' => $user->id,
            ]);

            return makeResponse(SUCCESS_CODE, 'Forum created successfully!', $forum);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function searchForums(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:255'
        ]);

        try {
            $query = $request->input('query');

            $forums = Forum::where('title', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%")
                ->with('comment')
                ->withCount('comment')
                ->with('created_by')
                ->orderBy('id', 'desc')
                ->get();

            foreach ($forums as $forum) {
                $forum->creator_badge_icon = optional(optional($forum->created_by)->roleBadge)->icon;
            }

            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $forums);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }
}
