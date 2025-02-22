<?php
namespace App\Http\Controllers;

use App\Models\UserVideo;
use Illuminate\Http\Request;

class UserVideoController extends Controller
{
    // Store a new video link
    public function store(Request $request)
    {
        $request->validate([
            'youtube_link' => 'required|url',
            'title' => 'required|string|max:25', // Max 28 characters for title
            'description' => 'nullable|string|max:40', // Max 64 characters for description
        ]);

        $video = UserVideo::create([
            'user_id' => auth()->id(), // Ensure you're using Sanctum for auth
            'youtube_link' => $request->youtube_link,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json($video, 201);
    }

    // Fetch all videos for the authenticated user
    public function index()
    {
        $videos = UserVideo::where('user_id', auth()->id())->get();

        return response()->json($videos);
    }

    // Fetch a single video by its ID
    public function show($id)
    {
        $video = UserVideo::findOrFail($id);

        return response()->json($video);
    }

    // Update a video by its ID
    public function update(Request $request, $id)
    {
        $request->validate([
            'youtube_link' => 'required|url',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $video = UserVideo::findOrFail($id);
        $video->update([
            'youtube_link' => $request->youtube_link,
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json($video);
    }

    // Delete a video by its ID
    public function destroy($id)
    {
        $video = UserVideo::findOrFail($id);
        $video->delete();

        return response()->json(null, 204);
    }
}
