<?php

namespace App\Http\Controllers;
use App\Models\UserRequirement;
use App\Models\RequirementType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class UserRequirementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'requirement_type_id' => 'required|exists:requirement_types,id',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);
    
        $path = $request->file('file')->store('user_requirements/' . auth()->id(), 'private');
    
        $requirement = UserRequirement::create([
            'user_id' => auth()->id(),
            'requirement_type_id' => $request->requirement_type_id,
            'file_path' => $path,
            'original_name' => $request->file('file')->getClientOriginalName(),
        ]);
    
        return response()->json([
            'message' => 'Uploaded successfully',
            'requirement' => $requirement,
        ]);
    }
        public function getAllRequirementTypes()
        {
            $types = RequirementType::select('name', 'description')->get();
            return response()->json($types); // for API
        }
    public function download($id)
    {
        // Check if account_type is 1, 2, 3, or 4
        if (in_array(auth()->user()->account_type, [1, 2, 3, 4])) {
            $requirement = UserRequirement::where('id', $id)
                ->where('user_id', auth()->id()) // para secured, sariling files lang pwede
                ->firstOrFail();
        
            return Storage::disk('private')->download($requirement->file_path, $requirement->original_name);
        } else {
            return response("You are not authorized to Download", 403); // Returning a 403 Forbidden response
        }
    }
    public function addRequirementType(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255|unique:requirement_types,name',  // Ensure 'name' is unique
            'description' => 'nullable|string|max:1000',  // Description is optional
        ]);

        // Create a new requirement type
        $requirementType = RequirementType::create([
            'name' => $request->name,
            'description' => $request->description,
            'inserted_by_id' => auth()->id(),  // Store the ID of the user who added this
        ]);

        // Return a success response
        return response()->json([
            'message' => 'Requirement type added successfully!',
            'requirement_type' => $requirementType,
        ], 201);  // HTTP Status code 201 - Created
    }
    
//{
//     "id": 1,
//     "user_id": 1,
//     "requirement_type_id": 1,
//     "file_path": "user_requirements/1/TzWgODVctCBUuA14UkWIfuGNWW88d9HDrXhGT4jt.docx",
//     "original_name": "Joseph Daily Work Report april 8 2025.docx",
//     "created_at": "2025-04-11T03:17:21.000000Z",
//     "updated_at": "2025-04-11T03:17:21.000000Z"
// }
    
}
