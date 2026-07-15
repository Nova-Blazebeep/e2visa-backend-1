<?php

namespace App\Http\Controllers\portal;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    public function index()
    {
        return view('portal.contact.index');
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

        $totalRecords = Contact::count();
        $totalRecordswithFilter = Contact::where(function ($query) use ($searchValue) {
            $query->where('name', 'like', "%$searchValue%")
                ->orWhere('email', 'like', "%$searchValue%")
                ->orWhere('subject', 'like', "%$searchValue%")
                ->orWhere('message', 'like', "%$searchValue%");
        })->count();

        $records = Contact::where(function ($query) use ($searchValue) {
            $query->where('name', 'like', "%$searchValue%")
                ->orWhere('email', 'like', "%$searchValue%")
                ->orWhere('subject', 'like', "%$searchValue%")
                ->orWhere('message', 'like', "%$searchValue%");
        })
            ->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr = [];

        foreach ($records as $record) {
            $deleteUrl = route('contact.delete', $record->id);
            $detailsUrl = route('contact.details', $record->id);

            $data_arr[] = [
                "checkbox" => '<input type="checkbox" class="rowCheckbox" value="' . $record->id . '">',
                "name" => $record->name,
                "email" => $record->email,
                "subject" => $record->subject,
                "message" => Str::limit(strip_tags($record->message), 30, '...'),
                "action" => '
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-info viewDetailsBtn" data-url="' . $detailsUrl . '" title="View Details">
                        <i class="bi bi-eye"></i> View
                    </button>
                    <button class="btn btn-sm btn-danger deleteContactBtn" data-url="' . $deleteUrl . '" title="Delete Contact">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>'
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
            $contact = Contact::find($id);
            if (!$contact) {
                return response()->json(['message' => 'Contact not found'], 404);
            }

            $contact->delete();
            return response()->json(['message' => 'Contact deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting contact'], 500);
        }
    }

    public function showDetails($id)
    {
        $contact = Contact::find($id);
        if (!$contact) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json([
            'name' => $contact->name,
            'email' => $contact->email,
            'subject' => $contact->subject,
            'message' => nl2br(e($contact->message)),
        ]);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');

        if (!$ids || !is_array($ids)) {
            return makeResponse(FAILURE_CODE, 'Please select at least one contact to delete.');
        }

        try {
            Contact::whereIn('id', $ids)->delete();
            return makeResponse(SUCCESS_CODE, 'Selected contacts deleted successfully!');
        } catch (\Exception $e) {
            return makeResponse(FAILURE_CODE, $e->getMessage());
        }
    }
}
