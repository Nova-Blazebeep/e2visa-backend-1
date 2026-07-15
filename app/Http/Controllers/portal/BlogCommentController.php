<?php

namespace App\Http\Controllers\portal;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use Illuminate\Http\Request;

class BlogCommentController extends Controller
{
    public function index()
    {
        $comments = BlogComment::with('blog')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('portal.blogs.comments', compact('comments'));
    }

    public function destroy($id)
    {
        $comment = BlogComment::findOrFail($id);
        $comment->delete();

        return redirect()->route('portal.blog.comments.index')
            ->with('success', 'Comment deleted successfully.');
    }
}
