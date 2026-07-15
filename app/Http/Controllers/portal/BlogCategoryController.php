<?php

namespace App\Http\Controllers\portal;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BlogCategoryController extends Controller
{
    public function index()
    {
        return view('portal.blog_categories.index');
    }

    public function list(Request $request)
    {
        $draw = $request->get('draw');
        $start = (int) $request->get('start', 0);
        $rowperpage = (int) $request->get('length', 10);

        $columnIndex_arr = $request->get('order', [['column' => 0, 'dir' => 'desc']]);
        $columnName_arr = $request->get('columns', []);
        $order_arr = $request->get('order', [['dir' => 'desc']]);
        $search_arr = $request->get('search', ['value' => '']);

        $columnIndex = $columnIndex_arr[0]['column'] ?? 0;
        $columnName = $columnName_arr[$columnIndex]['data'] ?? 'id';
        $columnSortOrder = $order_arr[0]['dir'] ?? 'desc';
        $searchValue = $search_arr['value'] ?? '';

        $allowedColumns = ['id', 'name', 'blogs_count', 'created_at'];
        if (! in_array($columnName, $allowedColumns, true)) {
            $columnName = 'id';
        }

        $query = BlogCategory::query()->withCount('blogs');
        if ($searchValue !== '') {
            $query->where('name', 'like', '%'.$searchValue.'%');
        }

        $totalRecords = BlogCategory::count();
        $totalRecordswithFilter = (clone $query)->count();

        $orderColumn = $columnName === 'blogs_count' ? 'blogs_count' : 'blog_categories.'.$columnName;

        $records = (clone $query)
            ->orderBy($orderColumn, $columnSortOrder === 'asc' ? 'asc' : 'desc')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];
        foreach ($records as $record) {
            $editUrl = route('portal.blog-categories.edit', $record->id);
            $deleteUrl = route('portal.blog-categories.destroy', $record->id);
            $data_arr[] = [
                'id' => $record->id,
                'name' => $record->name,
                'blogs_count' => (int) $record->blogs_count,
                'created_at' => $record->created_at?->format('Y-m-d H:i:s'),
                'action' => '<button type="button" class="btn btn-warning btn-sm editBlogCategoryBtn" data-url="'.$editUrl.'" data-title="Edit category">Edit</button>
                    <button type="button" class="btn btn-danger btn-sm deleteBlogCategoryBtn" data-url="'.$deleteUrl.'">Delete</button>',
            ];
        }

        return response()->json([
            'draw' => intval($draw),
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordswithFilter,
            'aaData' => $data_arr,
        ]);
    }

    public function create()
    {
        return view('portal.blog_categories.partials.form', ['category' => null])->render();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:blog_categories,name'],
        ]);

        BlogCategory::create(['name' => $validated['name']]);

        return makeResponse(SUCCESS_CODE, CREATE_SUCCESS);
    }

    public function edit($id)
    {
        $category = BlogCategory::findOrFail($id);

        return view('portal.blog_categories.partials.form', compact('category'))->render();
    }

    public function update(Request $request, $id)
    {
        $category = BlogCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('blog_categories', 'name')->ignore($category->id)],
        ]);

        $category->update(['name' => $validated['name']]);

        return makeResponse(SUCCESS_CODE, UPDATE_SUCCESS);
    }

    public function destroy($id)
    {
        $category = BlogCategory::findOrFail($id);

        if ($category->blogs()->count() > 0) {
            return makeResponse(FAILURE_CODE, 'This category is used by one or more blogs and cannot be deleted.');
        }

        $category->delete();

        return makeResponse(SUCCESS_CODE, DELETE_SUCCESS);
    }
}
