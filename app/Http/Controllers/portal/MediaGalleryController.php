<?php

namespace App\Http\Controllers\portal;
use App\Http\Controllers\Controller;
use App\Models\MediaGallery;
use Illuminate\Http\Request;
//Service
use App\Services\MediaGalleryService;
use Carbon\Carbon;

class MediaGalleryController extends Controller
{
        
    public function index()
    {
        return view('portal.gallery.index');
    }
    
    public function edit($id)
    {
        $record = MediaGallery::findorfail($id);

        return view('portal.gallery.partials.form', compact('record'))->render(); // Pass the existing record for edit
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        // Validate input
		$validated = $request->validate([
			'name' => 'required|string|max:255',
            'path' => $id ? 'nullable|string|max:255' : 'required|string|max:255',
		]);
            // if ($id) {
            // 	$media = MediaGallery::findorfail($id);
            // 	$media->update($validated);

            // 	return response()->json(['message' => 'media updated successfully.']);
            // }

        if ($id) {
            $media = MediaGallery::findOrFail($id);
            $media->update([
                'name' => $validated['name'],
                'path' => $validated['path'] ?? $media->path, 
            ]);
            return response()->json(['message' => 'media updated successfully.']);
        }

         else {
			$gallery=MediaGallery::create($validated);

            return response()->json([
				'message' => 'Gallery Item Added!',
				'data' => $gallery
			], 201);
			//return response()->json(['message' => 'media added successfully.']);
		}
    }

    public function create()
    {
        return view('portal.gallery.partials.form', ['record' => null])->render(); // Pass null for add
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

		// Fetch data using repository
		$totalRecords = MediaGallery::count();
		$totalRecordswithFilter = MediaGallery::where('name', 'like', '%' . $searchValue . '%')->count();
		
         $records = MediaGallery::orderBy($columnName, $columnSortOrder)
            ->where('name', 'like', '%' . $searchValue . '%')
            ->select('*')
            ->skip($start)
            ->take($rowperpage)
            ->get();


		// Prepare response data
		$data_arr = [];
		foreach ($records as $record) {
			$data_arr[] = [
				"id" => $record->id,
				"name" => $record->name, 
                "updated_at" => Carbon::parse($record->updated_at)->format('Y-m-d H:i:s'), // Format to 'YYYY-MM-DD HH:MM:SS'

				"url" => $record->path,
				//"image" => '<img src="' . $record->path . '" alt="' . $record->name . '" width="150" height="100">',
                'image'=>$record->path,
				"action" => ''
			];
		}

		// Return response
		$response= [
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecords,
			"iTotalDisplayRecords" => $totalRecordswithFilter,
			"aaData" => $data_arr
		];

        return response()->json($response);
    }
    
    public function delete($id)
    {
        try {
           $item=MediaGallery::findorfail($id);
           if($item){
            $item->delete();
             return response()->json(['message' => 'Record deleted successfully'], 200);
           }
           
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete record', 'error' => $e->getMessage()], 500);
        }
    }

    public function uploadCroppedImage(Request $request)
    {
        if (!$request->hasFile('cropped_image')) {
            return response()->json(['error' => 'No image uploaded'], 400);
        }
        $image = $request->file('cropped_image');
        $folder = 'galleries';
        $path = $image->store($folder, 'public');
            $response = [
        'message' => 'Image uploaded successfully.',
        'path' => asset('storage/' . $path), 
    ];
        return response()->json($response);
    }
}
