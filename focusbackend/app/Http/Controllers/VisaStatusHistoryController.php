<?php

namespace App\Http\Controllers;

use App\Models\VisaStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisaStatusHistoryController extends Controller
{
    /**
     * Display a listing of the Visa Status History records.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20); // Default 20 per page

        // Fetch visa status histories with pagination
        $visaStatusHistories = VisaStatusHistory::paginate($perPage);

        return response()->json($visaStatusHistories);
    }

    /**
     * Store a newly created Visa Status History record.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'employer_id' => 'required|exists:users,id',
            'visa_status_id' => 'required|exists:visa_statuses,id',
            'approved_by' => 'required|exists:users,id',
            'profession' => 'nullable|string',
            'step' => 'required|string',
            'status' => 'required|integer',
            'completed_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Create a new VisaStatusHistory record
        $visaStatusHistory = VisaStatusHistory::create($request->all());

        return response()->json([
            'message' => 'Visa Status History created successfully',
            'data' => $visaStatusHistory
        ], 201);
    }

    /**
     * Display the specified Visa Status History record.
     */
    public function show($id)
    {
        // Fetch the visa status history by ID
        $visaStatusHistory = VisaStatusHistory::findOrFail($id);

        return response()->json($visaStatusHistory);
    }

    /**
     * Update the specified Visa Status History record.
     */
    public function update(Request $request, $id)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'status' => 'required|integer',
            'completed_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Find the VisaStatusHistory record
        $visaStatusHistory = VisaStatusHistory::findOrFail($id);

        // Update the record
        $visaStatusHistory->update($request->only('status', 'completed_at'));

        return response()->json([
            'message' => 'Visa Status History updated successfully',
            'data' => $visaStatusHistory
        ]);
    }

    /**
     * Remove the specified Visa Status History record.
     */
    public function destroy($id)
    {
        // Find and delete the VisaStatusHistory record
        $visaStatusHistory = VisaStatusHistory::findOrFail($id);
        $visaStatusHistory->delete();

        return response()->json([
            'message' => 'Visa Status History deleted successfully'
        ]);
    }

    public function testmuna($id)
    {
        // Find and delete the VisaStatusHistory record
        $visaStatusHistory = VisaStatusHistory::findOrFail($id);
        $visaStatusHistory->delete();

        return response()->json([
            'message' => 'Visa Status History deleted successfully'
        ]);
    }
}
