<?php

namespace App\Http\Controllers\portal;

use App\Http\Controllers\Controller;
use App\Models\BusinessInformation;
use App\Models\Country;
use App\Models\County;
use App\Models\State;
use App\Models\User;
use App\Models\UserBadge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // Buyers, Admins, and Moderators never need a paid badge.
    private const BADGE_EXEMPT_ROLES = ['Buyer', 'Admin', 'Moderator'];

    public function index()
    {
        $roles=Role::all();
        return view('portal.users.index',compact('roles'));
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

    // Base query with role relationship. live_listings_count is scoped to
    // published (is_publish=1) listings only — drafts don't count as "live".
    $query = User::with('roles', 'roleBadge', 'userBadge')
        ->withCount(['bussinessinformation as live_listings_count' => function ($q) {
            $q->where('is_publish', 1);
        }]);

    // Role filter from DataTables request
    if ($request->filled('role_id')) {
        $roleId = $request->role_id;
        $query->whereHas('roles', function($q) use ($roleId) {
            $q->where('id', $roleId);
        });
    }

    // Paid-badge status filter — lets an admin quickly pull up everyone
    // waiting on activation.
    if ($request->filled('badge_status')) {
        $badgeStatus = $request->badge_status;
        $query->whereHas('userBadge', function($q) use ($badgeStatus) {
            $q->where('status', $badgeStatus);
        });
    }

    // Search filter
    if (!empty($searchValue)) {
        $query->where(function($q) use ($searchValue) {
            $q->where('name', 'like', '%' . $searchValue . '%')
              ->orWhere('email', 'like', '%' . $searchValue . '%')
              ->orWhereHas('roles', function($roleQuery) use ($searchValue) {
                  $roleQuery->where('name', 'like', '%' . $searchValue . '%');
              });
        });
    }

    $totalRecords = User::count();
    $totalRecordswithFilter = $query->count();

    $records = $query->orderBy($columnName, $columnSortOrder)
                     ->skip($start)
                     ->take($rowperpage)
                     ->get();

    $data_arr = [];
    foreach ($records as $record) {
        $data_arr[] = [
            "id" => $record->id,
            "name" => $record->name,
            "email" => $record->email,
            "role_badge" => $record->roleBadge
                ? '<img width="50" height="50" src="' . asset('storage/' . $record->roleBadge->icon) . '" alt="Badge">'
                : '',
            'role' => $record->roles->pluck('name')->implode(', '), // Multiple roles comma separated
            'is_featured' => $record->is_featured,
            'live_listings' => $this->liveListingsCell($record),
            'badge_status' => $this->badgeStatusBadge($record),
            "action" => '',
        ];
    }

    return response()->json([
        "draw" => intval($draw),
        "recordsTotal" => $totalRecords,
        "recordsFiltered" => $totalRecordswithFilter,
        "data" => $data_arr,
    ]);
}

    // Small colored pill for the user list — blank for Buyer/Admin/Moderator
    // (no badge concept applies), otherwise Pending/Active/Revoked.
    private function badgeStatusBadge(User $record): string
    {
        if (in_array($record->role, self::BADGE_EXEMPT_ROLES, true) || !$record->userBadge) {
            return '';
        }
        $colors = ['pending' => '#b8860b', 'active' => '#1e8449', 'revoked' => '#a93226'];
        $status = $record->userBadge->status;
        $color = $colors[$status] ?? '#6c757d';
        return '<span style="display:inline-block;padding:2px 10px;border-radius:12px;font-size:11px;font-weight:600;'
            . 'text-transform:uppercase;color:#fff;background:' . $color . ';">' . ucfirst($status) . '</span>';
    }

    // "Live Listings" cell — a plain number when zero, a clickable link when
    // the user has published listings. Reuses the existing .viewDetailBtn /
    // #DetailModal pattern already wired up globally in portal.layout.app,
    // so clicking it pops the same modal as the "View Detail" button does.
    private function liveListingsCell(User $record): string
    {
        $count = $record->live_listings_count ?? 0;
        if ($count < 1) {
            return '<span class="text-muted">0</span>';
        }
        $url = route('portal.users.liveListings', $record->id);
        return '<a href="#" class="viewDetailBtn fw-semibold" data-url="' . $url . '">' . $count . '</a>';
    }

    // Renders the modal content listing a user's published listings by name,
    // each linking to that listing's admin edit/detail page.
    public function liveListings($id)
    {
        $record = User::findOrFail($id);
        $listings = BusinessInformation::where('user_id', $id)
            ->where('is_publish', 1)
            ->orderBy('listing_heading')
            ->get(['id', 'listing_heading', 'business_type']);

        return view('portal.users.partials.live-listings', compact('record', 'listings'))->render();
    }

    public function details($id)
    {
        $record = User::findOrFail($id);
        $role_badge = $record->roleBadge ? '<img width="50" height="50" src="' . asset('storage/' . $record->roleBadge->icon) . '" alt="Badge">' : '';
        $user_image = $record->userInformation ? '<img width="50" height="50" src="' . asset('storage/' . $record->userInformation->image) . '" alt="User Image">' : '';
        $role = $record->role;

        return view('portal.users.partials.detail', compact('record', 'role_badge', 'user_image', 'role'))->render();
    }

    public function create()
    {
        $roles = Role::all();
        $countries = Country::all();
        return view('portal.users.partials.form', [
            'user' => new User(),
            'roles' => $roles,
            'countries' => $countries
        ])->render();
    }

    public function edit($id)
    {
        $user = User::with('userInformation', 'userBadge')->findOrFail($id);
        $roles = Role::all();
        $countries = Country::all();
        return view('portal.users.partials.form', compact('user', 'roles', 'countries'))->render();
    }


    public function storeOrUpdate(Request $request, $id = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->whereNull('deleted_at')->ignore($id)],
            'role' => 'required|string|exists:roles,name',
            'password' => $id ? 'nullable|string|min:8' : 'required|string|min:8',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'country_id' => 'nullable|integer|exists:countries,id',
            'city' => 'nullable|string|max:255',
            'time_frame_for_immigration' => 'nullable|string|max:255',
            'state_id' => 'nullable|integer|exists:states,id',
            'county_id' => 'nullable|integer|exists:counties,id',
            'zipcode' => 'nullable|string|max:20',
            'subscribe_for_newsletter' => 'nullable|boolean',
            'image' => 'nullable|image|max:5120',
            'about' => 'nullable|string|max:150',
            'licensed_states' => 'nullable|array',
            'licensed_states.*' => 'nullable|string|max:100',
            'badge_status' => 'nullable|in:pending,active,revoked',
            'payment_reference' => 'nullable|string|max:255',
        ];

        $validated = $request->validate($rules);

        $country = $validated['country_id'] ? Country::find($validated['country_id']) : null;
        $state = $validated['state_id'] ? State::find($validated['state_id']) : null;
        $county = $validated['county_id'] ? County::find($validated['county_id']) : null;
        $licensedStates = array_values(array_unique(array_filter($request->input('licensed_states', []))));

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = $image->hashName();
            $path = $image->store('user_images', 'public');
            $imageSavePath =  $path;
        }

        $role = Role::where('name', $validated['role'])->firstOrFail();

        if ($id) {
            $user = User::findOrFail($id);
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'email_verified_at'=>now(),
                'role' => $validated['role'],
                'role_id' => $role->id,
                'image' => $imageSavePath ?? $user->userInformation->image ?? null
            ]);

            if (!empty($validated['password'])) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }

            $user->syncRoles([$validated['role']]);

            $user->userInformation()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'phone_number' => $validated['phone_number'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'country_id' => $validated['country_id'] ?? null,
                    'country_name' => $country->name ?? '',
                    'city' => $validated['city'] ?? null,
                    'state_id' => $validated['state_id'] ?? null,
                    'state_name' => $state->name ?? '',
                    'county_id' => $validated['county_id'] ?? null,
                    'county_name' => $county->name ?? '',
                    'zipcode' => $validated['zipcode'] ?? null,
                    'subscribe_for_newsletter' => $validated['subscribe_for_newsletter'] ?? false,
                    'image' => $imageSavePath ?? $user->userInformation->image ?? null,
                    'about' => $validated['about'] ?? null,
                    'licensed_states' => $licensedStates,
                ]
            );

            $this->syncBadgeStatus($user, $validated['role'], $validated['badge_status'] ?? null, $validated['payment_reference'] ?? null);

            return response()->json(['message' => 'User updated successfully!', 'data' => $user], 200);
        } else {
            // Permanently remove any soft-deleted record with this email so the unique index doesn't block the insert
            User::withTrashed()->where('email', $validated['email'])->whereNotNull('deleted_at')->forceDelete();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'email_verified_at'=>now(),
                'role_id' => $role->id,
                'password' => Hash::make($validated['password']),
                'image' => $imageSavePath ?? null
            ]);

            $user->assignRole($validated['role']);

            $user->userInformation()->create([
                'phone_number' => $validated['phone_number'] ?? null,
                'address' => $validated['address'] ?? null,
                'country_id' => $validated['country_id'] ?? null,
                'country_name' => $country->name ?? '',
                'city' => $validated['city'] ?? null,
                'state_id' => $validated['state_id'] ?? null,
                'state_name' => $state->name ?? '',
                'county_id' => $validated['county_id'] ?? null,
                'county_name' => $county->name ?? '',
                'zipcode' => $validated['zipcode'] ?? null,
                'time_frame_for_immigration' => $validated['time_frame_for_immigration'] ?? null,
                'have_broker' => $validated['have_broker'] ?? false,
                'have_attorney' => $validated['have_attorney'] ?? false,
                'subscribe_for_newsletter' => $validated['subscribe_for_newsletter'] ?? false,
                'broker_license' => $validated['broker_license'] ?? null,
                'attorney_license' => $validated['attorney_license'] ?? null,
                'image' => $imageSavePath ?? null,
                'about' => $validated['about'] ?? null,
                'licensed_states' => $licensedStates,
            ]);

            $this->syncBadgeStatus($user, $validated['role'], $validated['badge_status'] ?? null, $validated['payment_reference'] ?? null);

            return response()->json(['message' => 'User created successfully!', 'data' => $user], 201);
        }
    }

    // Creates/updates the user's paid-badge row from the admin form. Exempt
    // roles (Buyer/Admin/Moderator) are skipped entirely — nothing to gate for
    // them. A transition INTO 'active' or INTO 'revoked' sends the matching
    // email; every other change (payment reference, re-saving the same
    // status, or no change at all) is silent.
    private function syncBadgeStatus(User $user, string $roleName, ?string $requestedStatus, ?string $paymentReference): void
    {
        if (in_array($roleName, self::BADGE_EXEMPT_ROLES, true)) {
            return;
        }

        $badge = UserBadge::firstOrCreate(['user_id' => $user->id], ['status' => 'pending']);
        $newStatus = $requestedStatus ?? $badge->status;
        $previousStatus = $badge->status;
        $becameActive = $newStatus === 'active' && $previousStatus !== 'active';
        $becameRevoked = $newStatus === 'revoked' && $previousStatus !== 'revoked';

        $badge->status = $newStatus;
        $badge->payment_reference = $paymentReference;
        if ($becameActive) {
            $badge->activated_at = now();
            $badge->activated_by = Auth::id();
        }
        $badge->save();

        if ($becameActive) {
            Mail::send('emails.badge_activated', ['name' => $user->name], function ($message) use ($user) {
                $message->to($user->email)->subject('Your E2Visa badge is active');
            });
        } elseif ($becameRevoked) {
            Mail::send('emails.badge_revoked', ['name' => $user->name], function ($message) use ($user) {
                $message->to($user->email)->subject('Your E2Visa badge has been revoked');
            });
        }
    }



    public function destroy($id)
    {
        if($id==1 || $id==13 || $id==14){
            makeResponse(FAILURE_CODE,"Default Account can not be delete");
        }
        else{
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully!']);
        }
    }

    public function setting()
    {
        return view('portal.users.setting');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Old password does not match.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', UPDATE_PASSWORD);
    }
}
