<?php

namespace App\Http\Controllers;

use App\Models\VehicleRecord;
use Illuminate\Http\Request;
use Carbon\Carbon; // Para sa date handling

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
        ]);

        $vehicleRecord = VehicleRecord::create([
            'user_id' => $request->user_id,
            'pattern' => $request->pattern,
            'color' => $request->color,
            'vehicle_type' => $request->vehicleType,
            'image' => $request->image,
            'vehicle_status' => $request->vehicle_status ?? true, // Default sa `true`
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
        $dateFilter = $request->query('date_filter');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $vehicleStatus = $request->query('vehicle_status');

        $query = VehicleRecord::where('user_id', $user_id);

        // Apply search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('pattern', 'LIKE', "%{$search}%")
                  ->orWhere('color', 'LIKE', "%{$search}%")
                  ->orWhere('vehicle_type', 'LIKE', "%{$search}%");
            });
        }

        // Filter by vehicle status (1 o 0)
        if ($vehicleStatus !== null) {
            $query->where('vehicle_status', $vehicleStatus);
        }

        // Apply date filter (Yesterday, Today)
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

        // Count IN and OUT events dynamically
        $counts = $this->countInOutEventsForDate($startDate, $endDate);

        return response()->json([
            'vehicleRecords' => $vehicleRecords,
            'dateCounts' => $counts,
        ], 200);
    }

    private function countInOutEventsForDate($startDate, $endDate): array
    {
        $counts = VehicleRecord::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('SUM(CASE WHEN vehicle_status = 1 THEN 1 ELSE 0 END) as inCount, 
                         SUM(CASE WHEN vehicle_status = 0 THEN 1 ELSE 0 END) as outCount')
            ->first();
    
        return [
            'inCount' => $counts->inCount ?? 0,
            'outCount' => $counts->outCount ?? 0
        ];
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
