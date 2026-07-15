<?php

namespace App\Http\Controllers\portal;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index()
    {
        return view('portal.subscriber.index');
    }

    public function list(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");

        $searchValue = $request->get('search')['value'];
        $order_arr = $request->get('order');
        $columnIndex = $order_arr[0]['column'];
        $columnName = $request->get('columns')[$columnIndex]['data'];
        $columnSortOrder = $order_arr[0]['dir'];

        $totalRecords = Subscriber::count();

        $totalRecordswithFilter = Subscriber::where(function ($query) use ($searchValue) {
            $query->where('email', 'like', "%$searchValue%");
        })->count();

        $records = Subscriber::where(function ($query) use ($searchValue) {
            $query->where('email', 'like', "%$searchValue%");
        })
            ->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];

        foreach ($records as $record) {
            $deleteUrl = route('subscriber.delete', $record->id);

            $data_arr[] = [
                "checkbox" => '<input type="checkbox" class="subscriberCheckbox" value="' . $record->id . '">',
                "email" => $record->email,
                "action" => '
                <button class="btn btn-sm btn-danger deleteSubscriberBtn" data-url="' . $deleteUrl . '" title="Delete">
                    <i class="bi bi-trash"></i> Delete
                </button>'
            ];
        }

        return response()->json([
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        ]);
    }

    public function delete($id)
    {
        try {
            $subscriber = Subscriber::find($id);
            if (!$subscriber) {
                return response()->json(['message' => 'Subscriber not found'], 404);
            }

            $subscriber->delete();
            return response()->json(['message' => 'Subscriber deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting subscriber'], 500);
        }
    }


    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (!is_array($ids) || count($ids) == 0) {
            return response()->json(['message' => 'No subscribers selected'], 400);
        }

        try {
            Subscriber::whereIn('id', $ids)->delete();
            return response()->json(['message' => 'Selected subscribers deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete subscribers'], 500);
        }
    }
}
