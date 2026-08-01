<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Forum;
use App\Models\ForumComments;
use App\Models\ForumCommentReplies;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    private function gatedBadgeIcon(?User $user): ?string
    {
        return $user?->activeBadgeIcon();
    }

    public function list(Request $request)
    {
        try {
            $forums = Forum::with([
                'comment',
                'created_by.roleBadge:id,role_id,icon', // only fetch needed columns
                'created_by.userBadge:id,user_id,status',
            ])->withCount('comment')->with('created_by')->orderBy('id', 'desc')->paginate(6);


            foreach ($forums as $forum) {
                $forum->creator_badge_icon = $this->gatedBadgeIcon($forum->created_by);
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
                    $q->with('roleBadge:id,role_id,icon', 'userBadge:id,user_id,status');
                },
                'comment.user' => function ($q) {
                    $q->with('roleBadge:id,role_id,icon', 'userBadge:id,user_id,status');
                },
                'comment.replies.user' => function ($q) {
                    $q->with('roleBadge:id,role_id,icon', 'userBadge:id,user_id,status');
                },
            ])
                ->withCount('comment')
                ->findOrFail($id);

            $forum->creator_badge_icon = $this->gatedBadgeIcon($forum->created_by);
            foreach ($forum->comment ?? [] as $comment) {
                $comment->commentor_badge_icon = $this->gatedBadgeIcon($comment->user);
                foreach ($comment->replies ?? [] as $reply) {
                    $reply->replier_badge_icon = $this->gatedBadgeIcon($reply->user);
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

            if (!$user->hasActiveBadge()) {
                return makeResponse(ABORT_CODE, 'Get your badge to participate in the forum.');
            }

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
                ->with('created_by.roleBadge:id,role_id,icon', 'created_by.userBadge:id,user_id,status')
                ->orderBy('id', 'desc')
                ->get();

            foreach ($forums as $forum) {
                $forum->creator_badge_icon = $this->gatedBadgeIcon($forum->created_by);
            }

            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $forums);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }
}
