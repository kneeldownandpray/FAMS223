<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleRecord;

class VehicleRecordController extends Controller
{
    public function store(Request $request)
{
    // Get the data from the request
    $pattern = $request->input('pattern');
    $color = $request->input('color');
    $vehicleType = $request->input('vehicle_type');
    $imageData = $request->input('image');

    // Store the data in the database
    $vehicleRecord = new VehicleRecord();
    $vehicleRecord->user_id = 1; // Assuming the user is authenticated
    $vehicleRecord->pattern = "";
    $vehicleRecord->color = $color;
    $vehicleRecord->vehicle_type = "car";
    $vehicleRecord->image = $imageData; // Store the base64 image directly in the database
    $vehicleRecord->save();
//save na sila pero ang problema ay masyado daw long
    return response()->json([
        'message' => 'Vehicle record created successfully!',
        'vehicle_record' => $vehicleRecord
    ], 201);
}

}
