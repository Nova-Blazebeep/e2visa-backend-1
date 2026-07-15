<?php

namespace App\Http\Controllers\portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\SubcategoryRequest;
use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return view('portal.category.index');
    }

    public function create()
    {
        return view('portal.category.partials.form', ['category' => null]);
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('portal.category.partials.form', compact('category'));
    }

    public function list(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];

        $totalRecords = Category::count();
        $totalRecordswithFilter = Category::where('name', 'like', '%' . $searchValue . '%')->count();

        $records = Category::where('name', 'like', '%' . $searchValue . '%')
            ->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];

        foreach ($records as $record) {
            $editUrl = route('portal.categories.edit', $record->id);
            $deleteUrl = route('portal.categories.destroy', $record->id);

            $data_arr[] = [
                "id" => $record->id,
                "title" => $record->name,
                "action" => '
                <button class="m-1 btn btn-warning btn-sm editCategoryBtn" data-url="' . $editUrl . '" data-title="Edit Category">Edit</button>
                <button class="m-1 btn btn-danger btn-sm deleteCategoryBtn" data-url="' . $deleteUrl . '">Delete</button>'
            ];
        }

        return response()->json([
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        ]);
    }

    public function storeOrUpdate(CategoryRequest $request, $id = null)
    {
        try {
            $validated = $request->validated();

            if ($id) {
                $category = Category::findOrFail($id);
                $category->update(['name' => $validated['name']]);
                return makeResponse(SUCCESS_CODE, 'Category updated successfully', $category);
            } else {
                $category = Category::create(['name' => $validated['name']]);
                return makeResponse(SUCCESS_CODE, 'Category created successfully', $category);
            }
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function destroyCategory($id)
    {
        try {
            $category = Category::with('subcategories')->find($id);

            if (!$category) {
                return makeResponse(FAILURE_CODE, RECORD_NOT_FOUND);
            }

            foreach ($category->subcategories as $subcategory) {
                $subcategory->delete();
            }

            $category->delete();

            return makeResponse(SUCCESS_CODE, DELETE_SUCCESSFUL);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }


    public function subCategoryIndex()
    {
        return view('portal.category.subcategory');
    }

    public function subCategoryCreate()
    {
        $categories = Category::all();
        return view('portal.category.partials.subForm', ['subcategory' => null, 'categories' => $categories]);
    }

    public function subCategoryEdit($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $categories = Category::all();
        return view('portal.category.partials.subForm', compact('subcategory', 'categories'));
    }

    public function subCategoryList(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column'];
        $columnName = $columnName_arr[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];
        $searchValue = $search_arr['value'];

        $totalRecords = Subcategory::count();
        $totalRecordswithFilter = Subcategory::where('name', 'like', "%$searchValue%")
            ->count();

        $records = Subcategory::with('category')
            ->where('name', 'like', "%$searchValue%")
            ->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];

        foreach ($records as $record) {
            $editUrl = route('portal.subcategories.edit', $record->id);
            $deleteUrl = route('portal.subcategories.destroy', $record->id);

            $data_arr[] = [
                "id" => $record->id,
                "category" => $record->category->name ?? '-',
                "name" => $record->name,
                "action" => '<button class="m-1 btn btn-warning btn-sm editCategoryBtn" data-url="' . $editUrl . '" data-title="Edit Subcategory">Edit</button>
                          <button class="m-1 btn btn-danger btn-sm deleteCategoryBtn" data-url="' . $deleteUrl . '">Delete</button>'
            ];
        }

        return response()->json([
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        ]);
    }

    public function subCategoryStoreOrUpdate(SubcategoryRequest $request, $id = null)
    {
        try {
            $validated = $request->validated();

            if ($id) {
                $subcategory = Subcategory::findOrFail($id);
                $subcategory->update($validated);
                return makeResponse(SUCCESS_CODE, 'Subcategory updated successfully', $subcategory);
            } else {
                $subcategory = Subcategory::create($validated);
                return makeResponse(SUCCESS_CODE, 'Subcategory created successfully', $subcategory);
            }
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function subCategoryDestroy($id)
    {
        try {
            $subcategory = Subcategory::find($id);

            if (!$subcategory) {
                return makeResponse(FAILURE_CODE, RECORD_NOT_FOUND);
            }

            $subcategory->delete();
            return makeResponse(SUCCESS_CODE, DELETE_SUCCESSFUL);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }
}
