<?php

namespace App\Http\Controllers;

use App\Models\VisaStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\VisaStatus;
use App\Models\User;
use App\Models\Skill;
use App\Models\ArchivedVisaStatus;

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
//     public function store(Request $request)
// {
//     $user = Auth::user();

//     // Append approved_by and default step
//     $request->merge([
//         'approved_by' => $user->id,
//         'step' => 'initiated', // or any default value you'd like
//         'completed_at' => now()
//     ]);

//     // Validate request
//     $validator = Validator::make($request->all(), [
//         'user_id' => 'required|exists:users,id',
//         'employer_id' => 'required|exists:users,id',
//         'visa_status_id' => 'required|exists:visa_statuses,id',
//         'approved_by' => 'required|exists:users,id',
//         'profession' => 'nullable|string',
//         'step' => 'required|string',
//         'status' => 'required|integer',
//         'completed_at' => 'nullable|date',
//     ]);

//     if ($validator->fails()) {
//         return response()->json(['errors' => $validator->errors()], 400);
//     }

//     // Create Visa Status History
//     $visaStatusHistory = VisaStatusHistory::create($request->all());

//     // Update corresponding VisaStatus.application_status to 0
//     $visaStatus = VisaStatus::find($request->visa_status_id);
//     if ($visaStatus) {
//         $visaStatus->application_status = 0;
//         $visaStatus->save();
//     }

//     return response()->json([
//         'message' => 'Visa Status History created and Visa Status updated successfully',
//         'data' => $visaStatusHistory
//     ], 201);
// }
public function store(Request $request)
{
    $user = Auth::user();

    // I-merge ang mga default na field
    $request->merge([
        'approved_by' => $user->id,
        'step' => 'initiated',
        'completed_at' => now()
    ]);

    // I-validate ang request data
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

    // Hanapin ang VisaStatus gamit ang visa_status_id
    $visaStatus = VisaStatus::find($request->visa_status_id);

    if (!$visaStatus) {
        return response()->json(['message' => 'Visa Status not found'], 404);
    }

    // I-update ang application_status ng VisaStatus
    $visaStatus->application_status = 0;
    $visaStatus->save();

    // Lumikha ng bagong VisaStatusHistory
    $visaStatusHistory = VisaStatusHistory::create($request->all());

    // I-archive ang kasalukuyang VisaStatus sa ArchivedVisaStatus
    $archivedVisaStatus = ArchivedVisaStatus::create([
        'user_id' => $visaStatus->user_id,
        'visa_status_history_id' => $visaStatusHistory->id,
        'application_received' => $visaStatus->application_received,
        'interview_employer_confirmation' => $visaStatus->interview_employer_confirmation,
        'requirements' => $visaStatus->requirements,
        'skill_assessment' => $visaStatus->skill_assessment,
        'visa_preparation' => $visaStatus->visa_preparation,
        'visa_lodged' => $visaStatus->visa_lodged,
        'medical_biometrics' => $visaStatus->medical_biometrics,
        'awaiting_decision' => $visaStatus->awaiting_decision,
        'visa_outcome' => $visaStatus->visa_outcome,
        'ready_to_fly' => $visaStatus->ready_to_fly,
    ]);

    // I-delete ang orihinal na VisaStatus kung na-save na ang history at archive
    if ($visaStatusHistory && $archivedVisaStatus) {
        $visaStatus->delete();
    }

    return response()->json([
        'message' => 'Visa Status History created, Visa Status archived, and original Visa Status deleted successfully',
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


    public function displayHistoryandVisaStatusOfWorker()
    {
        $user = Auth::user();
        $id = $user->id;
    
        // Get all visa status items for user
        $visaStatuses = VisaStatus::where('user_id', $id)->get()->keyBy('id'); // keyBy visa_status_id
    
        // Get visa status history and append matching visa status
        $histories = VisaStatusHistory::where('user_id', $id)->get()->map(function ($history) use ($visaStatuses) {
            $history->visa_status = $visaStatuses[$history->visa_status_id] ?? null;
            return $history;
        });
    
        if ($histories->isEmpty()) {
            return response()->json(['message' => 'No records found for this user ID.'], 404);
        }
    
        return response()->json([
            'data' => $histories
        ]);
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

    public function revert($id)
    {
        // Hanapin ang VisaStatusHistory record
        $history = VisaStatusHistory::find($id);
    
        if (!$history) {
            return response()->json(['message' => 'History not found.'], 404);
        }
    
        // Hanapin ang corresponding VisaStatus
        $visaStatus = VisaStatus::find($history->visa_status_id);
    
        if (!$visaStatus) {
            return response()->json(['message' => 'VisaStatus not found.'], 404);
        }
    
        // I-update ang application_status sa 1
        $visaStatus->application_status = 1;
        $visaStatus->save();
    
        // I-delete yung history record
        $history->delete();
    
        return response()->json(['message' => 'Visa status reverted and history deleted.'], 200);
    }
    
}
