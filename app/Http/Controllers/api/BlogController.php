<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function getBlogsList()
    {
        try {
            $blogs = Blog::where('is_active', 1)->with('user')
                ->orderBy('is_pinned', 'desc')
                ->latest()
                ->get();
            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $blogs);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function getBlogById($title)
    {
        try {
            $blog = Blog::where('is_active', 1)
                ->where('title', $title)
                ->with(['user', 'comments' => function($query) {
                    $query->where('is_approved', true)->latest();
                }])
                ->first();

            if (!$blog) {
                return makeResponse(FAILURE_CODE, 'Blog not found');
            }

            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $blog);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function searchBlogs(Request $request)
    {
        try {
            $query = $request->input('query');

            if (!$query) {
                return makeResponse(FAILURE_CODE, 'Search query is required.');
            }

            $blogs = Blog::where('is_active', 1)
                ->where(function ($q) use ($query) {
                    $q->where('title', 'LIKE', "%{$query}%")
                        ->orWhere('content', 'LIKE', "%{$query}%");
                })
                ->with('user')
                ->latest()
                ->get();

            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $blogs);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function addComment(Request $request, $title)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'comment' => 'required|string',
            ]);

            $blog = Blog::where('is_active', 1)
                ->where('title', $title)
                ->first();
            if (!$blog) {
                return makeResponse(FAILURE_CODE, 'Blog not found');
            }

            $comment = $blog->comments()->create([
                'name' => $request->name,
                'email' => $request->email,
                'comment' => $request->comment,
                'is_approved' => true,
            ]);

            return makeResponse(SUCCESS_CODE, 'Comment added successfully', $comment);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return makeResponse(FAILURE_CODE, $e->errors());
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }
}
