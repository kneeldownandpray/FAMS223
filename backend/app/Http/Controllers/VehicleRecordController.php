<?php

namespace App\Http\Controllers;

use App\Models\VehicleRecord;
use Illuminate\Http\Request;
use Carbon\Carbon; // For handling date operations
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
            'user_id' => 'required|integer',
            // 'vehicle_status' => 'boolean' // Ensure it's a boolean value
        ]);
    
        $vehicleRecord = VehicleRecord::create([
            'user_id' => $request->user_id,
            'pattern' => $request->pattern,
            'color' => $request->color,
            'vehicle_type' => $request->vehicleType,
            'image' => $request->image,
            'vehicle_status' => $request->vehicle_status ?? true, // Default to true if not provided
        ]);
    
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
        $dateFilter = $request->query('date_filter'); // For filtering by date
        $startDate = $request->query('start_date'); // Start date for range filter
        $endDate = $request->query('end_date'); // End date for range filter

        $query = VehicleRecord::where('user_id', $user_id);

        // Apply search filter if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('pattern', 'LIKE', "%{$search}%")
                  ->orWhere('color', 'LIKE', "%{$search}%")
                  ->orWhere('vehicle_type', 'LIKE', "%{$search}%");
            });
        }

        // Apply date filter (Yesterday, Today, Date Range)
        if ($dateFilter) {
            if ($dateFilter == 'yesterday') {
                $query->whereDate('created_at', Carbon::yesterday()->toDateString());
            } elseif ($dateFilter == 'today') {
                $query->whereDate('created_at', Carbon::today()->toDateString());
            }
        }

        // Apply date range filter
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
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
