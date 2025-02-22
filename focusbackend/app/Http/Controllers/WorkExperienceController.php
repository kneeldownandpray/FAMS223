<?php

namespace App\Http\Controllers;

use App\Models\WorkExperience;
use Illuminate\Http\Request;

class WorkExperienceController extends Controller
{
    public function index()
    {
        return WorkExperience::all();
    }

    public function show($id)
    {
        return WorkExperience::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'resume_id' => 'required|integer',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'job_description' => 'nullable|string'
        ]);

        $workExperience = WorkExperience::create($data);

        return response()->json($workExperience, 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'company_address' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'job_description' => 'nullable|string'
        ]);

        $workExperience = WorkExperience::findOrFail($id);
        $workExperience->update($data);

        return response()->json($workExperience);
    }

    public function destroy($id)
    {
        $workExperience = WorkExperience::findOrFail($id);
        $workExperience->delete();

        return response()->json(null, 204);
    }
}
