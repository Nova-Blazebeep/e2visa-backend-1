<?php

namespace App\Http\Controllers\portal;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\County;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
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

    // Base query with role relationship
    $query = User::with('roles', 'roleBadge');

    // Role filter from DataTables request
    if ($request->filled('role_id')) {
        $roleId = $request->role_id;
        $query->whereHas('roles', function($q) use ($roleId) {
            $q->where('id', $roleId);
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
        $user = User::with('userInformation')->findOrFail($id);
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
            'about' => 'nullable|string',
            'licensed_states' => 'nullable|array|max:3',
            'licensed_states.*' => 'nullable|string|max:100',
        ];

        $validated = $request->validate($rules);

        $country = $validated['country_id'] ? Country::find($validated['country_id']) : null;
        $state = $validated['state_id'] ? State::find($validated['state_id']) : null;
        $county = $validated['county_id'] ? County::find($validated['county_id']) : null;
        $licensedStates = array_slice(array_filter($request->input('licensed_states', [])), 0, 3);

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

            return response()->json(['message' => 'User created successfully!', 'data' => $user], 201);
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
