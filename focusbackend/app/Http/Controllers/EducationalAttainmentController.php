<?php

namespace App\Http\Controllers;

use App\Models\EducationalAttainment;
use Illuminate\Http\Request;

class EducationalAttainmentController extends Controller
{
    public function index()
    {
        return EducationalAttainment::all();
    }

    public function show($id)
    {
        return EducationalAttainment::findOrFail($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'resume_id' => 'required|integer',
            'level' => 'required|string|in:Primary,Secondary,Tertiary',
            'institution' => 'required|string|max:255',
            'period' => 'required|string|max:255',
            'course' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255'
        ]);

        $educationalAttainment = EducationalAttainment::create($data);

        return response()->json($educationalAttainment, 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'level' => 'nullable|string|in:Primary,Secondary,Tertiary',
            'institution' => 'nullable|string|max:255',
            'period' => 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255'
        ]);

        $educationalAttainment = EducationalAttainment::findOrFail($id);
        $educationalAttainment->update($data);

        return response()->json($educationalAttainment);
    }

    public function destroy($id)
    {
        $educationalAttainment = EducationalAttainment::findOrFail($id);
        $educationalAttainment->delete();

        return response()->json(null, 204);
    }
}
