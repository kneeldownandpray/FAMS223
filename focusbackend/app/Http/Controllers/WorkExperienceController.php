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
            'job_descriptions' => 'required|array',  // Array of job descriptions
            'job_descriptions.*.description' => 'required|string|max:255',  // Each job description is a string
        ]);

        // Create the Work Experience
        $workExperience = WorkExperience::create($request->only([
            'resume_id', 'company_name', 'company_address', 'position', 'start_date', 'end_date'
        ]));

        // Add Job Descriptions
        $jobDescriptions = collect($request->job_descriptions)->map(function ($jobDescription) use ($workExperience) {
            return new JobDescription([
                'description' => $jobDescription['description'],
                'work_experience_id' => $workExperience->id,
            ]);
        });

        // Save Job Descriptions
        $workExperience->jobDescriptions()->saveMany($jobDescriptions);

        return response()->json($workExperience->load('jobDescriptions'), 201);
    }

    // Update an existing WorkExperience with Job Descriptions

    public function jobDescriptions()
    {
        return $this->hasMany(JobDescription::class);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'job_descriptions' => 'required|array',  // Array of job descriptions
            'job_descriptions.*.description' => 'required|string|max:255',  // Each job description is a string
        ]);

        $workExperience = WorkExperience::findOrFail($id);
        $workExperience->update($request->only([
            'company_name', 'company_address', 'position', 'start_date', 'end_date'
        ]));

        // Update Job Descriptions
        $workExperience->jobDescriptions()->delete();  // Remove existing job descriptions
        $jobDescriptions = collect($request->job_descriptions)->map(function ($jobDescription) use ($workExperience) {
            return new JobDescription([
                'description' => $jobDescription['description'],
                'work_experience_id' => $workExperience->id,
            ]);
        });

        // Save new Job Descriptions
        $workExperience->jobDescriptions()->saveMany($jobDescriptions);

        return response()->json($workExperience->load('jobDescriptions'), 200);
    }
}
