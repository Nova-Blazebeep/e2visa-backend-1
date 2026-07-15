<?php

namespace App\Http\Controllers\portal;

use App\Http\Controllers\Controller;
use App\Models\Forum;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('portal.forums.index', compact('users'));
    }

    public function list(Request $request)
    {
        $draw        = $request->get('draw');
        $start       = $request->get('start');
        $rowperpage  = $request->get('length');

        $columnIndex_arr = $request->get('order');
        $columnName_arr  = $request->get('columns');
        $search_arr      = $request->get('search');

        $columnIndex     = $columnIndex_arr[0]['column'];
        $columnName      = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $columnIndex_arr[0]['dir'];
        $searchValue     = $search_arr['value'];
        $userFilter      = $request->get('user_id');

        $query = Forum::with('created_by')
            ->when($searchValue, fn($q) => $q->where('title', 'like', "%{$searchValue}%")
                ->orWhere('content', 'like', "%{$searchValue}%"))
            ->when($userFilter, fn($q) => $q->where('created_by_id', $userFilter));

        $totalRecords          = Forum::count();
        $totalRecordswithFilter = (clone $query)->count();

        $records = $query
            ->orderBy('created_at', 'desc')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];
        foreach ($records as $record) {
            $delete_route = route('portal.forums.delete', $record->id);
            $content_preview = strip_tags($record->content ?? '');
            $content_preview = strlen($content_preview) > 80
                ? substr($content_preview, 0, 80) . '...'
                : $content_preview;

            $data_arr[] = [
                'id'           => $record->id,
                'title'        => $record->title,
                'content'      => $content_preview,
                'created_by'   => $record->created_by->name ?? $record->created_by_name ?? 'N/A',
                'comments'     => $record->comment()->count(),
                'created_at'   => Carbon::parse($record->created_at)->format('Y-m-d H:i'),
                'action'       => '
                    <a href="#" onclick="delete_confirmation(\'' . $delete_route . '\')"
                       class="text-danger" title="Delete">
                        <i class="fa fa-trash"></i>
                    </a>',
            ];
        }

        return response()->json([
            'draw'                => intval($draw),
            'iTotalRecords'       => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordswithFilter,
            'aaData'              => $data_arr,
        ]);
    }

    public function delete($id)
    {
        $forum = Forum::findOrFail($id);
        // Also delete related comments and replies
        foreach ($forum->comment as $comment) {
            $comment->replies()->delete();
            $comment->delete();
        }
        $forum->delete();

        return redirect()->route('portal.forums.index')->with('success', 'Forum deleted successfully.');
    }
}
