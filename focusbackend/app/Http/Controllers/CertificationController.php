<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use Illuminate\Http\Request;

class CertificationController extends Controller
{
    // Store a new certification for a Resume
    public function store(Request $request, $resumeId)
    {
        $request->validate([
            'cert_name' => 'required|string',
            'year' => 'required|integer',
        ]);

        // Create a new certification record
        $certification = Certification::create([
            'resume_id' => $resumeId,   // Using the resumeId from the URL
            'certificate_name' => $request->cert_name,
            'year_received' => $request->year,
        ]);

        // Return the newly created certification
        return response()->json($certification, 201);
    }

    // Update an existing certification
    public function update(Request $request, $id)
    {
        $request->validate([
            'cert_name' => 'required|string',
            'year' => 'required|integer',
        ]);

        $certification = Certification::findOrFail($id);
        $certification->update([
            'certificate_name' => $request->cert_name,
            'year_received' => $request->year,
        ]);

        return response()->json($certification, 200);
    }

    // Delete a certification
    public function destroy($id)
    {
        $certification = Certification::findOrFail($id);
        $certification->delete();

        return response()->json(null, 204);  // No content response
    }
}
