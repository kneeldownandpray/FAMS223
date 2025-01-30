<?php
namespace App\Http\Controllers;

use App\Models\WorkExperience;
use App\Models\JobDescription;
use Illuminate\Http\Request;

class WorkExperienceController extends Controller
{
    // Store a new WorkExperience along with Job Descriptions
    public function store(Request $request)
    {
        $request->validate([
            'resume_id' => 'required|integer|exists:resumes,id',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'job_descriptions' => 'required|array',
            'job_descriptions.*.description' => 'required|string|max:255',
        ]);

        // Create Work Experience
        $workExperience = WorkExperience::create($request->only([
            'resume_id', 'company_name', 'company_address', 'position', 'start_date', 'end_date'
        ]));

        // Save Job Descriptions
        $jobDescriptions = collect($request->job_descriptions)->map(fn ($jobDesc) => new JobDescription([
            'description' => $jobDesc['description']
        ]));

        $workExperience->jobDescriptions()->saveMany($jobDescriptions);

        return response()->json($workExperience->load('jobDescriptions'), 201);
    }

    // Update an existing WorkExperience with Job Descriptions
    public function update(Request $request, WorkExperience $workExperience)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'job_descriptions' => 'required|array',
            'job_descriptions.*.description' => 'required|string|max:255',
        ]);

        // Update Work Experience
        $workExperience->update($request->only([
            'company_name', 'company_address', 'position', 'start_date', 'end_date'
        ]));

        // Delete old Job Descriptions & Save new ones
        $workExperience->jobDescriptions()->delete();
        $jobDescriptions = collect($request->job_descriptions)->map(fn ($jobDesc) => new JobDescription([
            'description' => $jobDesc['description']
        ]));

        $workExperience->jobDescriptions()->saveMany($jobDescriptions);

        return response()->json($workExperience->load('jobDescriptions'), 200);
    }
}
