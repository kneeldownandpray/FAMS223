<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\Resume;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    // Store new skill for a Resume
    public function store(Request $request, $resumeId)
    {
        // Check if the resume exists
        $resume = Resume::find($resumeId);
        if (!$resume) {
            return response()->json(['error' => 'Resume not found'], 404);
        }

        // Validate request data
        $request->validate([
            'skill_name' => 'required|string',
        ]);

        // Create new skill
        $skill = Skill::create([
            'resume_id' => $resumeId,
            'skill_name' => $request->skill_name,
        ]);

        return response()->json($skill, 201);
    }

    // Update a skill
    public function update(Request $request, $id)
    {
        // Validate request data
        $request->validate([
            'skill_name' => 'required|string',
        ]);

        // Find skill and update it
        $skill = Skill::findOrFail($id);
        $skill->update($request->all());

        return response()->json($skill, 200);
    }

    // Delete a skill
    public function destroy($id)
    {
        // Find skill and delete it
        $skill = Skill::findOrFail($id);
        $skill->delete();

        return response()->json(null, 204);
    }
}
