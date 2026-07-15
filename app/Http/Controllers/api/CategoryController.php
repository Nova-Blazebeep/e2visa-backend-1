<?php

namespace App\Http\Controllers\api;

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
        try {
            $categories = Category::with(['subcategories' => function($q) {
                $q->orderBy('name', 'ASC');
            }])
            ->orderBy('name', 'ASC')
            ->get();

            if ($categories->isEmpty()) {
                return makeResponse(FAILURE_CODE, NOT_FOUND);
            }

            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $categories);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function storeCategory(CategoryRequest $request)
    {
        try {
            $validated = $request->validated();
            $category = Category::create($validated);

            return makeResponse(SUCCESS_CODE, CREATE_SUCCESS, $category);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function updateCategory(CategoryRequest $request, $id)
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return makeResponse(FAILURE_CODE, RECORD_NOT_FOUND);
            }

            $category->update($request->validated());
            return makeResponse(SUCCESS_CODE, UPDATE_SUCCESS, $category);
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




     public function listSubCategories(Request $request){
         $rules = [
            'category_id' => 'required|exists:categories,id',
         ];
            ValidateApiRequest($rules, request()->all());
            $sub_categories = Subcategory::where('category_id',$request->category_id)->get();
             return makeResponse(SUCCESS_CODE, REQUEST_SUCCESSFUL,$sub_categories);


     }



    public function storeSubcategory(SubcategoryRequest $request)
    {
        try {
            $validated = $request->validated();

            if (!Category::find($validated['category_id'])) {
                return makeResponse(FAILURE_CODE, 'Parent category not found.');
            }

            $subcategory = Subcategory::create($validated);
            return makeResponse(SUCCESS_CODE, CREATE_SUCCESS, $subcategory);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }



    public function listSubcategory(SubcategoryRequest $request)
    {
        try {
            $validated = $request->validated();

            if (!Category::find($validated['category_id'])) {
                return makeResponse(FAILURE_CODE, 'Parent category not found.');
            }

            $subcategory = Subcategory::create($validated);
            return makeResponse(SUCCESS_CODE, CREATE_SUCCESS, $subcategory);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function updateSubcategory(SubcategoryRequest $request, $id)
    {
        try {
            $subcategory = Subcategory::find($id);

            if (!$subcategory) {
                return makeResponse(FAILURE_CODE, RECORD_NOT_FOUND);
            }

            $validated = $request->validated();

            if (!Category::find($validated['category_id'])) {
                return makeResponse(FAILURE_CODE, NOT_FOUND);
            }

            $subcategory->update($validated);
            return makeResponse(SUCCESS_CODE, UPDATE_SUCCESS, $subcategory);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function destroySubcategory($id)
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
