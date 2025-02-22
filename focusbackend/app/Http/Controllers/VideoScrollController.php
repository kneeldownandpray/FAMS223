<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoScrollController extends Controller
{
    public function getNextVideo(Request $request)
    {
        $user = Auth::user();
        $accountType = $user->account_type;

        
            if ($accountType == 5) {
                // Fetch random user who has videos and resumes
                $user = User::has('videos') // Has videos
                            ->has('resumes') // Also has resumes
                            ->whereDoesntHave('applicantHired', function($query) {
                                $query->where('approval_of_admin', 1);
                            })
                            
                            ->with('videos')
                            ->inRandomOrder()
                            ->first();

                // If a user with videos is found, select a random video
                if ($user) {
                    $video = $user->videos->shuffle()->first();

                    return response()->json([
                        'video' => $video ? [
                            'title' => $video->title,
                            'description' => $video->description,
                            'link' => $video->youtube_link,
                            'user_id' => $video->user_id,
                        ] : null,
                    ]);
                } else {
                    return response()->json("No video found", 404);
                }
            } else {
                return response()->json("Invalid user", 404);
            }
    
    }

    public function getPreviousVideo(Request $request)
    {
        // Retrieve video ID or data from local storage (sent from front-end)
        $videoData = $request->input('video');

        if ($videoData) {
            return response()->json([
                'video' => $videoData,
            ]);
        } else {
            return response()->json("No previous video available", 404);
        }
    }
}
