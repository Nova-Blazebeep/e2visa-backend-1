<?php

namespace App\Http\Controllers\portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\RealestateInformationRequest;
use App\Models\BusinessImage;
use App\Models\BusinessInformation;
use App\Models\BusinessType;
use App\Models\Category;
use App\Models\Country;
use App\Models\County;
use App\Models\EstablishedYears;
use App\Models\ListingType;
use App\Models\PropertyType;
use App\Models\SellerFinancialDocument;
use App\Models\State;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RealestateController extends Controller
{
    public function index()
    {
        $listing_types = ListingType::all();
        return view('portal.real-estate.index', compact('listing_types'));
    }

    public function list(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");

        $columnIndex = $request->get('order')[0]['column'];
        $columnName = $request->get('columns')[$columnIndex]['data'];
        $columnSortOrder = $request->get('order')[0]['dir'];
        $searchValue = $request->get('search')['value'];

        $query = BusinessInformation::query();

        // Filter by business_type - only show real-estate
        $query->where('business_type', 'real-estate');

        // Filter by publish status
        if (in_array($request->is_publish, ['0', '1'], true)) {
            $query->where('is_publish', $request->is_publish);
        }

        // Filter by listing type
        if (!empty($request->listing_type)) {
            $query->where('listing_type_id', $request->listing_type);
        }

        // Search
        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('listing_heading', 'like', '%' . $searchValue . '%')
                    ->orWhere('listing_number', 'like', '%' . $searchValue . '%')
                    ->orWhere('contact_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('contact_email', 'like', '%' . $searchValue . '%');
            });
        }

        $totalRecords = BusinessInformation::where('business_type', 'real-estate')->count();
        $totalRecordsWithFilter = $query->count();

        $records = $query->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];
        foreach ($records as $record) {
            $data_arr[] = [
                "id" => $record->id,
                "business_name" => $record->business_name,
                "listing_type" => $record->listing_type,
                'is_published' => $record->is_publish == 1 ? '<span class="badge bg-success">Published</span>' : '<span class="badge bg-warning">Draft</span>',
                "action" => '<a href="/real-estate/' . $record->id . '" class="btn btn-sm btn-warning">Edit</a>
                         <button class="btn btn-sm btn-danger deleteBtn" data-id="' . $record->id . '">Delete</button>',
            ];
        }

        return response()->json([
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalRecordsWithFilter,
            "data" => $data_arr,
        ]);
    }

    public function showForm()
    {
        $users = User::with('userInformation')->get();
        $Seller = getUserByRole('Seller');
        $business = BusinessInformation::with(['business_images', 'financial_documents'])->get();
        $categories = Category::with('subcategories')->get();
        $countries = Country::with('states.counties')->get();
        $business_types = BusinessType::all();
        $listing_types = ListingType::all();
        $established_years = EstablishedYears::all();
        $property_types = PropertyType::all();

        return view('portal.real-estate.create', compact('users', 'business', 'categories', 'countries', 'business_types', 'listing_types', 'established_years', 'Seller', 'property_types'));
    }

    public function store(Request $request)
    {
        try {
            $isPublish = $request->input('is_publish');

            if ($isPublish == 1) {
                $validated = app(RealestateInformationRequest::class)->validated();
            } else {
                $validated = $request->all();
            }

            if (isset($validated['property_type']) && is_numeric($validated['property_type'])) {
                $propertyType = PropertyType::find($validated['property_type']);
                if ($propertyType) {
                    $validated['property_type_id'] = $propertyType->id;
                    $validated['property_type'] = $propertyType->name;
                }
            }

            $validated['business_type'] = 'real-estate';
            // Listing Title field maps to listing_heading; keep business_name in sync
            if (empty($validated['business_name'])) {
                $validated['business_name'] = $validated['listing_heading'] ?? '';
            }

            $business = BusinessInformation::create($validated);

            if (isset($validated['listing_type'])) {
                $listing_type = ListingType::where('listing_type', $validated['listing_type'])->first();
                if ($listing_type) {
                    $business->listing_type_id = $listing_type->id;
                    $business->listing_type = $listing_type->listing_type;
                    $business->save();
                }
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('business_images', 'public');

                    BusinessImage::create([
                        'business_information_id' => $business->id,
                        'image_path' => $path, 
                    ]);
                }
            }

            if ($request->hasFile('seller_financial_documents')) {
                foreach ($request->file('seller_financial_documents') as $doc) {
                    $filename = 'doc_' . time() . '_' . Str::random(8) . '.' . $doc->getClientOriginalExtension();
                    $doc->storeAs('public/documents', $filename);
                    SellerFinancialDocument::create([
                        'business_information_id' => $business->id,
                        'document_path' => 'documents/' . $filename
                    ]);
                }
            }

            if ($request->ajax()) {
                return response()->json(['code' => 200, 'message' => 'Saved successfully']);
            }

            return redirect()->route('real-estate.index')->with(SUCCESS_CODE, CREATE_SUCCESS);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['code' => 500, 'message' => $e->getMessage()]);
            }
            return back()->with(FAILURE_CODE, $e->getMessage());
        }
    }

    public function show($businessId)
    {
        $business = BusinessInformation::with(['business_images', 'financial_documents'])->findOrFail($businessId);

        $listing_types = ListingType::all();
        $categories = Category::with('subcategories')->get();
        $subcategories = Subcategory::all();
        $countries = Country::with('states.counties')->get();
        $states = State::all();
        $counties = County::all();
        $business_types = BusinessType::all();
        $established_years = EstablishedYears::all();
        $property_types = PropertyType::all();

        $users = User::with('userInformation')->get();

        $existingImagesCount = $business->business_images->count();
        $existingDocsCount = $business->financial_documents->count();

        $usersData = $users->mapWithKeys(function ($user) {
            return [
                $user->id => [
                    'name' => $user->userInformation->name ?? $user->name,
                    'email' => $user->email,
                    'phone' => $user->userInformation->phone_number ?? 'N/A',
                ]
            ];
        });

        return view('portal.real-estate.edit', compact(
            'business',
            'categories',
            'subcategories',
            'countries',
            'states',
            'listing_types',
            'business_types',
            'established_years',
            'counties',
            'users',
            'usersData',
            'existingImagesCount',
            'existingDocsCount',
            'property_types',
        ));
    }

    public function update(Request $request)
    {
        try {
            $businessId = $request->input('business_id');

            $isPublish = $request->input('is_publish');

            if ($isPublish == 1) {
                $validated = app(RealestateInformationRequest::class)->validated();
            } else {
                $validated = $request->all();
            }

            if (isset($validated['property_type']) && is_numeric($validated['property_type'])) {
                $propertyType = PropertyType::find($validated['property_type']);
                if ($propertyType) {
                    $validated['property_type_id'] = $propertyType->id;
                    $validated['property_type'] = $propertyType->name;
                }
            }

            $business = BusinessInformation::find($businessId);

            if (!$business) {
                if ($request->ajax()) {
                    return response()->json(['code' => 404, 'message' => 'Real estate not found.']);
                }
                return back()->with(ABORT_CODE, NOT_FOUND);
            }

            $validated['business_type'] = 'real-estate';
            if (empty($validated['business_name'])) {
                $validated['business_name'] = $validated['listing_heading'] ?? $business->listing_heading;
            }
            $business->update($validated);

            if (isset($validated['listing_type'])) {
                $listing_type = ListingType::where('listing_type', $validated['listing_type'])->first();
                if ($listing_type) {
                    $business->listing_type_id = $listing_type->id;
                    $business->listing_type = $listing_type->listing_type;
                    $business->save();
                }
            }

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

            if ($request->ajax()) {
                return response()->json(['code' => SUCCESS_CODE, 'message' => UPDATE_SUCCESS]);
            }

            return redirect()->route('real-estate.index')->with(SUCCESS_CODE, UPDATE_SUCCESS);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['code' => NORESPONSE_CODE, 'message' => $e->getMessage()]);
            }
            return back()->with(FAILURE_CODE, $e->getMessage());
        }
    }

    public function destroy($businessId)
    {
        try {
            $business = BusinessInformation::where('id', $businessId)
                ->with(['business_images', 'financial_documents'])
                ->first();

            if (!$business) {
                return response()->json(['message' => 'Real estate not found.'], ABORT_CODE);
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

            return response()->json(['message' => 'Real estate deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete real estate: ' . $e->getMessage()], NORESPONSE_CODE);
        }
    }


    public function getPropertyTypes()
    {
        try {
            $property_types = PropertyType::all();
            return response()->json($property_types);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to get property types: ' . $e->getMessage()], NORESPONSE_CODE);
        }
    }
}
