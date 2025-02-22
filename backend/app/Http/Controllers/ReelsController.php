<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Video;

class ReelsController extends Controller
{
    public function index(Request $request)
    {
        // Check if the user has the correct account type
        if ($request->user()->account_type !== 5) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get the current page number from the request
        $page = $request->input('page', 1);
        $perPage = 10; // Number of users per page

        // Fetch users who have videos and shuffle them
        $usersWithVideos = User::has('videos')
            ->inRandomOrder()
            ->paginate($perPage);

        // Initialize an array to hold the videos
        $result = [];

        // Loop through the paginated users
        foreach ($usersWithVideos as $user) {
            // Get the user's videos and shuffle them
            $videos = $user->videos()->inRandomOrder()->get();

            // Check if there are videos for the user
            if ($videos->isNotEmpty()) {
                // Get one random video for this user
                $randomVideo = $videos->first();
                
                // Add the user and the video to the result array
                $result[] = [
                    'user_id' => $user->id,
                    'video' => $randomVideo,
                    
                ];
            }
        }

        // Return the paginated result with the videos
        return response()->json([
            'data' => $result,
            'current_page' => $usersWithVideos->currentPage(),
            'last_page' => $usersWithVideos->lastPage(),
        ]);
    }
}
