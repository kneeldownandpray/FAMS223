<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Resume;
use App\Models\UserVideo; // Import the UserVideo model
use Illuminate\Http\Request;

class ResumeController extends Controller
{
    public function index()
    {
        // Fetch resumes with related user, educational attainments, and work experiences
        return Resume::with(['user:id,gender,birthday,email,address', 'educationalAttainments', 'workExperiences'])->get();
    }

    public function show($id)
    {
        // Fetch a specific resume with related user, educational attainments, and work experiences
        return Resume::with(['user:id,gender,birthday,email,address', 'educationalAttainments', 'workExperiences'])->findOrFail($id);
    }

    public function store(Request $request)
    {
        // Validate incoming data
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'birth_address' => 'required|string|max:255',
            'height' => 'nullable|numeric', // Accepts decimal values
            'weight' => 'nullable|integer',
            'objectives' => 'nullable|string',
            'civil_status' => 'nullable|string',
            'religion' => 'nullable|string',
            'nationality' => 'nullable|string',
            'profession' => 'nullable|string',
            'contact_no' => 'nullable|string',
        ]);

        // Get the authenticated user's ID
        $userId = auth()->id();

        // Check if a resume already exists for the user
        $existingResume = Resume::where('user_id', $userId)->first();

        if ($existingResume) {
            return response()->json(['message' => 'Resume already exists.'], 400);
        }

        // Create a new resume with the validated data
        if (auth()->user()->account_type == 6) { 
            $data['user_id'] = $userId;
            $resume = Resume::create($data);
            return response()->json($resume, 201);
        }

        return response()->json(['message' => 'Unauthorized Application for Resume (this user is not a worker).'], 403);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'full_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'birth_address' => 'nullable|string|max:255',
            'height' => 'nullable|integer',
            'weight' => 'nullable|integer',
            'objectives' => 'nullable|string',
            'civil_status' => 'nullable|string',
            'religion' => 'nullable|string',
            'nationality' => 'nullable|string',
            'profession' => 'nullable|string',
            'contact_no' => 'nullable|string',
        ]);

        $resume = Resume::findOrFail($id);
        $resume->update($data);

        return response()->json($resume);
    }

    public function destroy($id)
    {
        $resume = Resume::findOrFail($id);
        $resume->delete();

        return response()->json(null, 204);
    }

    public function showOwnResume()
    {
        $userId = auth()->id();
        $resume = Resume::with(['user:id,gender,birthday,email,address', 'educationalAttainments', 'workExperiences'])
            ->where('user_id', $userId)
            ->first();

        if ($resume) {
            return response()->json($resume);
        }

        return response()->json(['message' => 'Resume not found for this user.'], 200);
    }

    public function getByUserId($userId)
    {

        // $userId = auth()->id();
        if (auth()->user()->account_type == 5) { 
        // Fetch the resume along with related user, educational attainments, work experiences, and user videos for the specified user_id
        $resume = Resume::with([
                'user:id,gender,birthday,email,address',
                'educationalAttainments',
                'workExperiences',
                'userVideos' // Include user videos in the query
            ])
            ->where('user_id', $userId)
            ->first();

        if ($resume) {
            return response()->json($resume);
        }

        return response()->json(['message' => 'Resume not found for this user.'], 404);
    } else {
        return response()->json(['message' => 'Invalid User.'], 404);
    }
    
    }

    
    public function getByUserVideo($userId)
    {

        if (auth()->user()->account_type == 5) { 
            // Fetch resume details, educational attainments, and user videos for the specified user
            $resume = Resume::where('user_id', $userId)
                ->with( 'userVideos') // Fetch related educational attainments and user videos
                ->first();
    
            if ($resume) {
                // Build the response structure
                $response = [
                    'user_id' => $resume->user_id,
                    'user_videos' => $resume->userVideos
                ];
    
                return response()->json($response);
            }
    
            return response()->json(['message' => 'Resume not found for this user.'], 404);
        } else {
            return response()->json(['message' => 'Invalid User.'], 404);
        }
    
    }
}
