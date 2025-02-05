<?php

namespace App\Http\Controllers;

use App\Models\VehicleRecord;
use Illuminate\Http\Request;

class VehicleRecordController extends Controller
{
    // Function to store vehicle record (Create)
    public function store(Request $request)
    {
        $request->validate([
            'pattern' => 'required|string',
            'color' => 'required|string',
            'vehicleType' => 'required|string',
            'image' => 'nullable|string',
            'user_id' => 'required|integer'
        ]);

        $vehicleRecord = new VehicleRecord();
        $vehicleRecord->user_id = $request->user_id;
        $vehicleRecord->pattern = $request->pattern;
        $vehicleRecord->color = $request->color;
        $vehicleRecord->vehicle_type = $request->vehicleType;
        $vehicleRecord->image = $request->image;
        $vehicleRecord->save();

        return response()->json([
            'message' => 'Vehicle record created successfully!',
            'vehicle_record' => $vehicleRecord
        ], 201);
    }

    // Function to get vehicle records with pagination & search (Read)
    public function index(Request $request, $user_id)
    {
        $search = $request->query('search');
        $perPage = $request->query('per_page', 10);

        $query = VehicleRecord::where('user_id', $user_id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('pattern', 'LIKE', "%{$search}%")
                  ->orWhere('color', 'LIKE', "%{$search}%")
                  ->orWhere('vehicle_type', 'LIKE', "%{$search}%");
            });
        }

        $vehicleRecords = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($vehicleRecords, 200);
    }

    // Function to update a vehicle record (Edit)
    public function update(Request $request, $id)
    {
        $vehicleRecord = VehicleRecord::find($id);

        if (!$vehicleRecord) {
            return response()->json(['message' => 'Vehicle record not found'], 404);
        }

        $request->validate([
            'pattern' => 'required|string',
        ]);

        $vehicleRecord->pattern = $request->pattern;
        
        $vehicleRecord->save();

        return response()->json([
            'message' => 'Vehicle record updated successfully!',
            'vehicle_record' => $vehicleRecord
        ], 200);
    }

    // Function to delete a vehicle record (Delete)
    public function destroy($id)
    {
        $vehicleRecord = VehicleRecord::find($id);

        if (!$vehicleRecord) {
            return response()->json(['message' => 'Vehicle record not found'], 404);
        }

        $vehicleRecord->delete();

        return response()->json(['message' => 'Vehicle record deleted successfully'], 200);
    }
}
