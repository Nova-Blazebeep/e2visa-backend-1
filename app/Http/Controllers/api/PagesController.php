<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PagesRequest;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PagesController extends Controller
{
    public function index()
    {
        try {
            $pages = Page::all();
            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $pages);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function store(PagesRequest $request)
    {
        try {
            $validated = $request->validated();

            $page = Page::create($validated);

            return makeResponse(SUCCESS_CODE, CREATE_SUCCESS, $page);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $page = Page::find($id);

            if (!$page) {
                return makeResponse(FAILURE_CODE, NOT_FOUND);
            }

            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $page);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function update(PagesRequest $request, $id)
    {
        try {
            $page = Page::find($id);

            if (!$page) {
                return makeResponse(FAILURE_CODE, NOT_FOUND);
            }

            $validated = $request->validated();
            $page->update($validated);

            return makeResponse(SUCCESS_CODE, UPDATE_SUCCESS, $page);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $page = Page::find($id);

            if (!$page) {
                return makeResponse(FAILURE_CODE, NOT_FOUND);
            }

            $page->delete();

            return makeResponse(SUCCESS_CODE, DELETE_SUCCESS);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }
}
