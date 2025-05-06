<?php

namespace App\Http\Controllers;

use App\Models\ArchivedVisaStatus;
use Illuminate\Http\Request;

class ArchivedVisaStatusController extends Controller
{
    /**
     * Display a listing of the archived visa statuses.
     */
    public function index()
    {
        return response()->json(ArchivedVisaStatus::with('user', 'visaStatusHistory')->get());
    }

    /**
     * Store a newly created archived visa status.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'visa_status_history_id' => 'nullable|exists:visa_status_histories,id',
            'application_received' => 'boolean',
            'interview_employer_confirmation' => 'boolean',
            'requirements' => 'boolean',
            'skill_assessment' => 'boolean',
            'visa_preparation' => 'boolean',
            'visa_lodged' => 'boolean',
            'medical_biometrics' => 'boolean',
            'awaiting_decision' => 'boolean',
            'visa_outcome' => 'boolean',
            'ready_to_fly' => 'boolean',
        ]);

        $record = ArchivedVisaStatus::create($validated);

        return response()->json($record, 201);
    }

    /**
     * Display the specified archived visa status.
     */
    public function show($id)
    {
        $record = ArchivedVisaStatus::with('user', 'visaStatusHistory')->findOrFail($id);
        return response()->json($record);
    }

    /**
     * Update the specified archived visa status.
     */
    public function update(Request $request, $id)
    {
        $record = ArchivedVisaStatus::findOrFail($id);

        $validated = $request->validate([
            'application_received' => 'boolean',
            'interview_employer_confirmation' => 'boolean',
            'requirements' => 'boolean',
            'skill_assessment' => 'boolean',
            'visa_preparation' => 'boolean',
            'visa_lodged' => 'boolean',
            'medical_biometrics' => 'boolean',
            'awaiting_decision' => 'boolean',
            'visa_outcome' => 'boolean',
            'ready_to_fly' => 'boolean',
        ]);

        $record->update($validated);

        return response()->json($record);
    }

    /**
     * Remove the specified archived visa status.
     */
    public function destroy($id)
    {
        $record = ArchivedVisaStatus::findOrFail($id);
        $record->delete();

        return response()->json(['message' => 'Archived visa status deleted.']);
    }
}
