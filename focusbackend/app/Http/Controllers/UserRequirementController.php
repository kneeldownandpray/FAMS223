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

    public function adminIndex(Request $request)
    {
        $query = UserRequirement::with('requirementType')->orderBy('created_at', 'desc');
    
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
    
        $requirements = $query->get();
    
        return response()->json($requirements);
    }
public function adminUpdate(Request $request, $id)
{
    // if (auth()->user()->account_type !== 0) {
    //     return response()->json(['message' => 'Unauthorized'], 403);
    // }

    $requirement = UserRequirement::findOrFail($id);

    $validated = $request->validate([
        'status' => 'nullable|in:processing,accepted,rejected',
        'note' => 'nullable|string|max:1000',
    ]);

    $requirement->update($validated);

    return response()->json(['message' => 'Requirement updated successfully.']);
}


    public function download($id)
    {
        $requirement = UserRequirement::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

        $path = storage_path("app/private/{$requirement->file_path}");

        // Get the correct mime type
        $mimeType = Storage::disk('private')->mimeType($requirement->file_path);

        return response()->download($path, $requirement->original_name, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $requirement->original_name . '"',
        ]);
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


    // Get All Requirement Types (existing method)
    public function getAllRequirementTypes()
    {
        $types = RequirementType::select('id', 'name', 'description')->get();
        return response()->json($types);
    }

    // Edit Requirement Type
    public function editRequirementType(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|string|max:255|unique:requirement_types,name,' . $id,
            'description' => 'nullable|string|max:1000',
        ]);

        $requirementType = RequirementType::find($id);

        if (!$requirementType) {
            return response()->json(['message' => 'Requirement type not found.'], 404);
        }

        // Update the requirement type
        $requirementType->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Return a success response
        return response()->json([
            'message' => 'Requirement type updated successfully!',
            'requirement_type' => $requirementType,
        ]);
    }

    // Delete Requirement Type
    public function deleteRequirementType($id)
    {
        $requirementType = RequirementType::find($id);

        if (!$requirementType) {
            return response()->json(['message' => 'Requirement type not found.'], 404);
        }

        // Delete the requirement type
        $requirementType->delete();

        // Return a success response
        return response()->json(['message' => 'Requirement type deleted successfully.']);
    }

    public function getUserResume()
        {
            $userId = auth()->id();

            $requirements = UserRequirement::with('requirementType')
                ->where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($requirements);
        }
    public function getMissingRequirements($userId)
        {
            
            $allTypes = RequirementType::select('id', 'name', 'description')->get();

          
            $userTypes = UserRequirement::where('user_id', $userId)->pluck('requirement_type_id');

            
            $missing = $allTypes->filter(function ($type) use ($userTypes) {
                return !$userTypes->contains($type->id);
            })->values(); // reset keys

            return response()->json([
                'user_id' => $userId,
                'missing_requirements' => $missing
            ]);
        }
    
}
