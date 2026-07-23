<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BusinessInformationRequest;
use App\Models\BusinessImage;
use App\Models\BusinessInformation;
use App\Models\Country;
use App\Models\SellerFinancialDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class BusinessInformationController extends Controller
{

    public function index()
    {
        try {
            $business = BusinessInformation::with(['business_images', 'financial_documents'])->get();
            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $business);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function store(BusinessInformationRequest $request)
    {
        try {
            $validated = $request->validated();

            $existing = BusinessInformation::where('user_id', $validated['user_id'])->first();
            if ($existing) {
                return makeResponse(FAILURE_CODE, ALREADY_EXIST);
            }

            $business = BusinessInformation::create($validated);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $filename = 'image_' . time() . '_' . Str::random(8) . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('public/images', $filename);
                    BusinessImage::create([
                        'business_information_id' => $business->id,
                        'image_path' => 'images/' . $filename
                    ]);
                }
            }

            if ($request->hasFile('seller_financial_documents')) {
                foreach ($request->file('seller_financial_documents') as $doc) {
                    $filename = 'doc_' . time() . '_' . Str::random(8) . '.' . $doc->getClientOriginalExtension();
                    $path = $doc->storeAs('public/documents', $filename);
                    SellerFinancialDocument::create([
                        'business_information_id' => $business->id,
                        'document_path' => 'documents/' . $filename
                    ]);
                }
            }

            return makeResponse(SUCCESS_CODE, CREATE_SUCCESS, $business->load(['business_images', 'financial_documents']));
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }


    public function show($userId)
    {
        try {
            $business = BusinessInformation::where('user_id', $userId)->with(['business_images', 'financial_documents'])->first();

            if (!$business) {
                return makeResponse(FAILURE_CODE, NOT_FOUND);
            }

            $business->business_images->transform(function ($img) {
                $img->image_path = url('storage/' . $img->image_path);
                return $img;
            });

            $business->financial_documents->transform(function ($doc) {
                $doc->document_path = url('storage/' . $doc->document_path);
                return $doc;
            });

            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $business);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }


    public function update(BusinessInformationRequest $request, $userId)
    {
        try {
            $business = BusinessInformation::where('user_id', $userId)->first();

            if (!$business) {
                return makeResponse(FAILURE_CODE, NOT_FOUND);
            }

            $validated = $request->validated();
            $business->update($validated);

             if ($request->hasFile('images')) {
                foreach ($business->business_images as $image) {
                    if (Storage::disk('public')->exists($image->image_path)) {
                        Storage::disk('public')->delete($image->image_path);
                    }
                    $image->delete();
                }

                foreach ($request->file('images') as $uploadedImage) {
                    $path = $uploadedImage->store('business_images', 'public');
                    BusinessImage::create([
                        'business_information_id' => $business->id,
                        'image_path' => $path,
                    ]);
                }
            }

            if ($request->hasFile('seller_financial_documents')) {
                foreach ($business->financial_documents as $doc) {
                    if (Storage::exists('public/' . $doc->document_path)) {
                        Storage::delete('public/' . $doc->document_path);
                    }
                    $doc->delete();
                }

                foreach ($request->file('seller_financial_documents') as $doc) {
                    $filename = 'doc_' . time() . '_' . Str::random(8) . '.' . $doc->getClientOriginalExtension();
                    $doc->storeAs('public/documents', $filename);
                    SellerFinancialDocument::create([
                        'business_information_id' => $business->id,
                        'document_path' => 'documents/' . $filename,
                    ]);
                }
            }

            return makeResponse(SUCCESS_CODE, UPDATE_SUCCESS, $business->load(['business_images', 'financial_documents']));
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }


    public function destroy($userId)
    {
        try {
            $business = BusinessInformation::where('user_id', $userId)->with(['business_images', 'financial_documents'])->first();

            if (!$business) {
                return makeResponse(FAILURE_CODE, NOT_FOUND);
            }

            foreach ($business->business_images as $img) {
                Storage::delete('public/' . $img->image_path);
                $img->delete();
            }

            foreach ($business->financial_documents as $doc) {
                Storage::delete('public/' . $doc->document_path);
                $doc->delete();
            }

            $business->delete();

            return makeResponse(SUCCESS_CODE, DELETE_SUCCESS);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function find_business(Request $request)
    {
        $rules = [
            'search_type'       => 'required|in:real_estate,business',
            'country'           => 'nullable|string|max:255',
            'state_id'          => 'nullable|string|max:255',
            'category_id'       => 'nullable|string|max:255',
            'sub_category_id'   => 'nullable|string|max:255',
        ];
        ValidateApiRequest($rules, $request->all());
        try {

            $eagerLoad = $request->search_type == 'real_estate'
                ? ['business_images', 'country', 'state', 'county']
                : ['business_images', 'country', 'state', 'county'];

            $query = BusinessInformation::with($eagerLoad)->where('is_publish', 1);
            if ($request->search_type == 'business') {
                $query = $query->whereIn('listing_type_id', listing_type_business);
            }
            if ($request->search_type == 'real_estate') {
                $query = $query->whereIn('listing_type_id', listing_type_real_estate);
            }


            if ($request->has('country')) {
                $country_ids = Country::where('name', 'like', '%' . $request->country . '%')->pluck('id')->toArray();
                $query = $query->whereIn('country_id', $country_ids);
            }

            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }
            if ($request->has('sub_category_id')) {
                $query->where('sub_category_id', $request->sub_category_id);
            }

            $business = $query->orderBy('id', 'desc')->get();

            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $business);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function showBusinessDetail($id)
    {
        try {
            $business = BusinessInformation::with(['business_images', 'financial_documents', 'country', 'state', 'county', 'category', 'subcategory', 'user.userInformation'])->find($id);

            if (!$business) {
                return makeResponse(FAILURE_CODE, NOT_FOUND);
            }


            $business->business_images->transform(function ($img) {
                $img->image_path = url('storage/' . $img->image_path);
                return $img;
            });

            $business->financial_documents->transform(function ($doc) {
                $doc->document_path = url('storage/' . $doc->document_path);
                return $doc;
            });

            if ($business->nda_document_path) {
                $business->nda_document_url = url('storage/' . $business->nda_document_path);
            } else {
                $business->nda_document_url = null;
            }

            $business->rent = rand(10000, 50000);

            // business_name and listing_number are confidential — not shown on frontend
            $business->makeHidden(['business_name', 'listing_number']);

            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $business);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

}
