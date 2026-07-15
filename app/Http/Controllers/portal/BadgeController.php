<?php

namespace App\Http\Controllers\portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\BadgeRequest;
use App\Models\Badge;
use Illuminate\Http\Request;

class BadgeController extends Controller
{
    public function index()
    {
        return view('portal.badges.index');
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

        $totalRecords = Badge::count();
        $totalRecordswithFilter = Badge::whereHas('role', fn($q) => $q->where('name', 'like', "%$searchValue%"))->count();

        $records = Badge::with('role')
            ->whereHas('role', fn($q) => $q->where('name', 'like', "%$searchValue%"))
            ->orderBy($columnName === 'title' ? 'role_id' : $columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];
        foreach ($records as $record) {
            $editUrl = route('portal.badges.edit', $record->id);
            $deleteUrl = route('portal.badges.destroy', $record->id);
            $iconUrl = asset('storage/' . $record->icon);
            $iconImg = '<img src="' . $iconUrl . '" style="width:40px;height:40px;object-fit:contain;"/>';

            $data_arr[] = [
                "id" => $record->id,
                "role_name" => $record->role?->name ?? '-',
                "icon" => $iconImg,
                "action" => '<button class="btn btn-warning btn-sm editBtn" data-url="' . $editUrl . '">Edit</button>
                    <button class="btn btn-danger btn-sm deleteBtn" data-url="' . $deleteUrl . '">Delete</button>'
            ];
        }

        return response()->json([
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        ]);
    }

    public function create()
    {
        return view('portal.badges.partials.form', ['badge' => null])->render();
    }

    public function store(BadgeRequest $request)
    {
        $validated = $request->validated();
        $path = $request->file('icon')->store('badges', 'public');
        $iconName = str_replace('public/', '', $path);

        Badge::create([
            'role_id' => $validated['role_id'],
            'icon' => $iconName
        ]);

        return makeResponse(SUCCESS_CODE, CREATE_SUCCESS);
    }

    public function edit($id)
    {
        $badge = Badge::findOrFail($id);
        return view('portal.badges.partials.form', compact('badge'))->render();
    }

    public function update(BadgeRequest $request, $id)
    {
        $validated = $request->validated();
        $badge = Badge::findOrFail($id);

        $data = ['role_id' => $validated['role_id']];

        if ($request->hasFile('icon')) {
            $path = $request->file('icon')->store('badges', 'public');
            $iconName = str_replace('public/', '', $path);
            $data['icon'] = $iconName;
        }

        $badge->update($data);

        return makeResponse(SUCCESS_CODE, UPDATE_SUCCESS);
    }

    public function destroy($id)
    {
        $badge = Badge::findOrFail($id);
        $badge->delete();

        return makeResponse(SUCCESS_CODE, DELETE_SUCCESS);
    }
}
