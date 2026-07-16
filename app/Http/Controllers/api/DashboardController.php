<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Mail\ProfessionalEmail;
use App\Models\Badge;
use App\Models\Blog;
use App\Models\BusinessInformation;
use App\Models\BusinessType;
use Illuminate\Http\Request;
use App\Models\ListingType;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Country;
use App\Models\Page;
use App\Models\Role;
use App\Models\Subcategory;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{

    public function getDashboardStats()
    {
        $counts = [
            'allUsers' => User::count(),
            'allCategories' => Category::count(),
            'allSubCategories' => Subcategory::count(),
            'allBusinessInformation' => BusinessInformation::count(),
            'allPages' => Page::count(),
            'allSubscribers' => Subscriber::count(),
            'allContacts' => Contact::count(),
            'allBlogs' => Blog::count(),
            'allBadges' => Badge::count(),
            'allBusinessTypes' => BusinessType::count(),
            'allBuyers' => User::where('role_id', 2)->count(),
            'allBrokers' => User::where('role_id', 3)->count(),
            'allSellers' => User::where('role_id', 4)->count(),
            'allAttorneys' => User::where('role_id', 5)->count(),
        ];

        return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $counts);
    }


    public function getAllListingTypes()
    {
        try {
            $allListingTypes = ListingType::all();
            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $allListingTypes);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function getAllBusinessTypes()
    {
        try {
            $allBusinessTypes = BusinessType::all();
            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $allBusinessTypes);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

    public function getBrokersDetails()
    {
        try {
            $brokers = User::where('role_id', 3)->with('userInformation')->get();

            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $brokers);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }


    public function filterBusinessInformationRecords(Request $request)
    {
        try {
            $query = BusinessInformation::query();

            if ($request->has('business_type_name') && $request->business_type_name != null) {
                $businessTypeIds = BusinessType::where('business_type', 'like', '%' . $request->business_type_name . '%')->pluck('id');
                $query->where(function ($q) use ($businessTypeIds) {
                    foreach ($businessTypeIds as $btId) {
                        $q->orWhereRaw("JSON_CONTAINS(business_type_ids, ?)", [json_encode((int) $btId)]);
                    }
                });
            }

            if ($request->has('category_name') && $request->category_name != null) {
                $categoryIds = Category::where('name', 'like', '%' . $request->category_name . '%')->pluck('id');
                $query->whereIn('category_id', $categoryIds);
            }

            if ($request->has('sub_category_name') && $request->sub_category_name != null) {
                $subCategoryIds = SubCategory::where('name', 'like', '%' . $request->sub_category_name . '%')->pluck('id');
                $query->whereIn('sub_category_id', $subCategoryIds);
            }

            if ($request->has('country_name') && $request->country_name != null) {
                $countryIds = Country::where('name', 'like', '%' . $request->country_name . '%')->pluck('id');
                $query->whereIn('country_id', $countryIds);
            }

            $records = $query->get();

            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $records);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }


    public function getProfessionalRoles()
    {
        $roles = Role::select('id', 'name')->with('badge')->whereIn('id', PROFESSIONAL_ROLES_IDS)->orderBy('id','ASC')->get();
        return makeResponse(SUCCESS_CODE, 'Professional roles fetched.', $roles);
    }

    public function findUsersWithProfession(Request $request)
    {
        $rule = [
            'profession_id' => 'required|in:' . implode(',', PROFESSIONAL_ROLES_IDS),
        ];
        ValidateApiRequest($rule, $request->all());
        try {
            $role = Role::where('id', $request->profession_id)->whereIn('id', PROFESSIONAL_ROLES_IDS)->first();
            if (!$role) {
                return makeResponse(FAILURE_CODE, 'Role not found.');
            }

            $users = User::with(['userInformation'])
                ->where('role_id', $role->id)
                ->whereNotNull('email_verified_at')
                ->get();

            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $users);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }


    public function sendEmailToProfessionals(Request $request)
    {
        $rules = [
            'professional_id' => 'required|exists:users,id',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'message' => 'required|string|max:1000',
            'email'=>'required'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return makeResponse(ABORT_CODE, 'Validation failed.', $validator->errors());
        }

        try {
            $user = User::find($request->professional_id);

            if (!$user || empty($user->email)) {
                return makeResponse(FAILURE_CODE, 'Professional email not found.');
            }

            Mail::to($user->email)->send(new ProfessionalEmail(
                $request->full_name,
                $request->phone_number,
                $request->message,
                $request->email,
            ));

            return makeResponse(SUCCESS_CODE, 'Email sent to professional successfully.');
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

   public function professional_detail(Request $request)
{
    $rules = [
        'professional_id' => 'required'
    ];
    ValidateApiRequest($rules, $request->all());

    $professional = User::with([
            'userInformation',
            'roleBadge:id,role_id,icon' // eager load badge
        ])
        ->where('id', $request->professional_id)
        ->whereIn('role_id', PROFESSIONAL_ROLES_IDS)
        ->first();

    if ($professional) {
        $professional->badge_icon = optional($professional->roleBadge)->icon;

        return makeResponse(SUCCESS_CODE, REQUEST_SUCCESSFUL, $professional);
    } else {
        return makeResponse(FAILURE_CODE, REQUEST_FAILED);
    }
}


    public function getFeaturedProfessionals()
    {
        try {
        $professionals = User::with([
                'userInformation',
                'roleBadge:id,role_id,icon' // load badge icon
            ])
            ->whereNotNull('email_verified_at')
            ->whereIn('role_id', PROFESSIONAL_ROLES_IDS)
            ->get();

        foreach ($professionals as $pro) {
            $pro->badge_icon = optional($pro->roleBadge)->icon;
        }

        return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $professionals);

    } catch (\Exception $e) {
        return makeResponse(FAILURE_CODE, $e->getMessage());
    }
    }


    public function getNewBusinessListings()
    {
        try {
            $listings = BusinessInformation::where('is_publish', 1)
                ->with(['business_images', 'financial_documents'])
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            return makeResponse(SUCCESS_CODE, FETCH_SUCCESS, $listings);
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }

}
