<?php

namespace App\Http\Controllers;

use App\Models\VisaStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\VisaStatus;
use App\Models\User;
use App\Models\Skill;


class VisaStatusHistoryController extends Controller
{
    /**
     * Display a listing of the Visa Status History records.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        
        // Initialize the query
        $query = VisaStatusHistory::with([
            'user.accountInformation',
            'employer.accountInformation',
            'approvedBy.accountInformation',
            // 'visaStatus' // Uncomment if needed
        ]);
        
        // Filter by worker name (user name)
        if ($request->has('worker_name') && !empty($request->worker_name)) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->worker_name . '%')
                  ->orWhere('last_name', 'like', '%' . $request->worker_name . '%');
            });
        }
        
        // Filter by employer name
        if ($request->has('employer_name') && !empty($request->employer_name)) {
            $query->whereHas('employer', function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->employer_name . '%')
                  ->orWhere('last_name', 'like', '%' . $request->employer_name . '%');
            });
        }
        
        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }
        
        // Filter by profession
        if ($request->has('profession') && !empty($request->profession)) {
            $query->where('profession', 'like', '%' . $request->profession . '%');
        }
        
        // Paginate the results
        $visaStatusHistories = $query->paginate($perPage);
        
        return response()->json($visaStatusHistories);
    }
    
    /**
     * Store a newly created Visa Status History record.
     */
    public function store(Request $request)
{
    $user = Auth::user();

    // Append approved_by and default step
    $request->merge([
        'approved_by' => $user->id,
        'step' => 'initiated', // or any default value you'd like
        'completed_at' => now()
    ]);

    // Validate request
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

    // Create Visa Status History
    $visaStatusHistory = VisaStatusHistory::create($request->all());

    // Update corresponding VisaStatus.application_status to 0
    $visaStatus = VisaStatus::find($request->visa_status_id);
    if ($visaStatus) {
        $visaStatus->application_status = 0;
        $visaStatus->save();
    }

    return response()->json([
        'message' => 'Visa Status History created and Visa Status updated successfully',
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
