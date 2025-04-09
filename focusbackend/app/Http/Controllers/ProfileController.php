<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfileController extends Controller
{
    public function uploadProfilePicture(Request $request)
{
    // Ensure that the user is authenticated
    $user = Auth::user();
    
    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // Validate the uploaded file
    $request->validate([
        'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Max size 5MB
    ]);

    // Get the uploaded file
    $file = $request->file('profile_picture');

    // Check if there's an existing profile picture
    if ($user->profile_picture) {
        // Get the path of the old profile picture
        $oldFilePath = storage_path('app/public/' . $user->profile_picture);
        
        // If the old file exists, delete it
        if (file_exists($oldFilePath)) {
            unlink($oldFilePath);
        }
    }

    // Store the new profile picture in public storage
    $path = $file->store('profile_pictures', 'public');

    // Update the user's profile picture path
    $user->update([
        'profile_picture' => $path,
    ]);

    return response()->json([
        'message' => 'Profile picture uploaded successfully!',
        'file_path' => $path,
    ], 200);
}

}
