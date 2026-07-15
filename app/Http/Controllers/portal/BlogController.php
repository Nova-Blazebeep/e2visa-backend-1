<?php

namespace App\Http\Controllers\portal;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        return view('portal.blogs.index');
    }

    public function add($editor)
    {
        $categories = BlogCategory::orderBy('name')->get();

        return view('portal.blogs.add', compact('categories', 'editor'));
    }

    public function edit($title)
    {
        // Retrieve the blog and its related category data
        $blog = Blog::where('title', $title)->first();
        $categories = BlogCategory::orderBy('name')->get();

        // Pass the blog and categories to the view
        return view('portal.blogs.edit', compact('blog', 'categories'));
    }

    public function list(Request $request)
    {
        // Read values
        $draw = $request->get('draw');
        $start = $request->get('start');
        $rowperpage = $request->get('length'); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = Blog::count();
        $totalRecordswithFilter = Blog::where('title', 'like', '%'.$searchValue.'%')->count();
        $statusFilter = $request->get('status');

        $query = Blog::with('category')
            ->when($searchValue, fn ($q) => $q->where('title', 'like', '%'.$searchValue.'%'))
            ->when(is_numeric($statusFilter), fn ($q) => $q->where('is_active', $statusFilter));

        // Fetch records with pagination and sorting
        $records = $query->orderBy($columnName, $columnSortOrder)
            ->where('title', 'like', '%'.$searchValue.'%')
            ->select('*')
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];

        foreach ($records as $record) {
            $delete_route = route('portal.blog.delete', $record->id);
            $edit_route = route('portal.blog.edit', $record->title);
            $data_arr[] = [
                'id' => $record->id,
                'title' => $record->title,
                'content' => $record->content,
                'banner' => $record->banner,
                'created_at' => Carbon::parse($record->created_at)->format('Y-m-d H:i:s'),
                'category' => $record->category ? $record->category->name : 'N/A', // Display category name
                'created_by' => $record->created_by,  // Example for created by
                'active_date' => Carbon::parse($record->active_date)->format('Y-m-d'),
                'is_active' => $record->is_active == 1
                    ? '<span class="badge bg-success">Published</span>'
                    : ($record->is_active == 2
                        ? '<span class="badge bg-info">Scheduled</span>'
                        : '<span class="badge bg-warning">Draft</span>'
                    ),
                'action' => '<div class="btn-group">
                        <a style="margin-right:10px;"href="'.$edit_route.'" class=" text-info" title="Edit">
                            <i class="fa fa-edit mr-5"></i>
                        </a>

                      <a href="#" onclick="delete_confirmation(\''.$delete_route.'\')" class="text-danger" title="Delete">
                            <i class="fa fa-trash"></i>
                        </a>
                    </div>',
            ];
        }

        $response = [
            'draw' => intval($draw),
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecordswithFilter,
            'aaData' => $data_arr,
        ];

        return \Response::json($response);
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'title' => [
                    'required',
                    'string',
                    'max:255',
                    'unique:blogs,title',
                    'regex:/^[^?&]*$/',
                ],
                'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
                'content' => 'required|string',
                'category_id' => 'required|integer|exists:blog_categories,id',
                'active_date' => 'nullable|date|after_or_equal:today',  // <-- ye line important hai
            ],
            [

                'title.regex' => 'The blog title must not contain "?" or "&" characters.',
            ]
        );

        if ($request->hasFile('banner')) {
            $banner = $request->file('banner');
            $bannerName = time().'_'.$banner->getClientOriginalName();
            $bannerPath = $banner->storeAs('/banners/'.date('Y-m-d'), $bannerName, 'public');
            $bannerUrl = asset('storage/'.$bannerPath);
        } else {
            $bannerUrl = null;
        }

        if ($request->active_date == Carbon::today()->toDateString() && $request->input('is_archived') != 1) {
            $is_active = 1;
        } elseif ($request->active_date > Carbon::today()->toDateString() && $request->input('is_archived') != 1) {
            $is_active = 2;
        } else {
            $is_active = 0;
        }

        $blog = Blog::create([
            'title' => $request->title,
            'banner' => $bannerUrl,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'created_by' => auth()->user()->id,
            'custom_author' => $request->input('custom_author'),
            'active_date' => $request->input('active_date'),
            'is_active' => $is_active,
            'is_pinned' => $request->has('is_pinned') ? 1 : 0,
        ]);

        if ($blog) {
            return response()->json(['message' => 'Blog added successfully', 'redirectURL' => route('portal.blog.index')], 200);
        } else {
            return response()->json(['message' => 'Failed to add blog'], 500);
        }
    }

    public function delete($id)
    {
        // Find the blog post or fail
        $blog = Blog::findOrFail($id);
        $blog->delete(); // Soft delete

        return redirect()->route('portal.blog.index')->with('success', 'Blog deleted');
    }

    public function update(Request $request, $title)
    {
        $request->validate(
            [
                'title' => [
                    'required',
                    'string',
                    'max:255',
                    'regex:/^[^?&]*$/',
                ],
                'banner' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
                'content' => 'required|string',
                'category_id' => 'required|integer|exists:blog_categories,id',
                'active_date' => 'nullable|date',
            ],
            [
                'title.required' => 'The blog title is required.',
                'title.string' => 'The blog title must be a string.',
                'title.max' => 'The blog title may not be greater than 255 characters.',
                'title.unique' => 'The blog title must be unique.',
                'title.regex' => 'The blog title must not contain "?" or "&" characters.',
        ]
        
        );
        $is_archived = $request->input('is_archived') == 1 ? 1 : 0;

        if ($request->active_date == Carbon::today()->toDateString() && $request->input('is_archived') != 1) {
            $is_active = 1;
        } elseif ($request->active_date > Carbon::today()->toDateString() && $request->input('is_archived') != 1) {
            $is_active = 2;
        } else {
            $is_active = 0;
        }

        $blog = Blog::where('title', $title)->first();
        $new_title = Blog::where('title', $request->title)->where('id', '!=', $blog->id)->first();
        if ($new_title) {
            return redirect()->back()->with('error', 'Please Choose a different title,this one is already taken');
        }

        if ($request->hasFile('banner')) {
            $banner = $request->file('banner');
            $bannerName = time().'_'.$banner->getClientOriginalName();
            $bannerPath = $banner->storeAs('banners/'.date('Y-m-d'), $bannerName, 'public');
            $bannerUrl = asset('storage/'.$bannerPath);
        } else {
            $bannerUrl = $blog->banner;
        }

        $blog->banner = $bannerUrl;
        $blog->content = $request->content;
        $blog->title = $request->title;
        $blog->category_id = $request->category_id;
        $blog->custom_author = $request->input('custom_author');
        $blog->is_active = $is_active;
        $blog->is_pinned = $request->has('is_pinned') ? 1 : 0;
        $blog->active_date = $request->active_date;
        $blog->save();

        if ($blog) {
            return redirect()->route('portal.blog.index')->with('success', 'Record Updated');
        } else {
            return response()->json(['message' => 'Failed to update blog'], 500);
        }
    }
}
