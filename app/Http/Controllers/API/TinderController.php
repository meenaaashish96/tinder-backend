<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\Swipe;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(title="Tinder Clone API", version="1.0")
 */
class TinderController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/profiles",
     * summary="Get recommended profiles",
     * @OA\Response(response="200", description="List of profiles")
     * )
     */
    public function getRecommendations(Request $request)
    {
        $user = Auth::user();

        // Get IDs of profiles already swiped by this user
        $swipedProfileIds = Swipe::where('user_id', $user->id)
            ->pluck('profile_id');

        // Fetch profiles NOT in the swiped list
        $profiles = Profile::with('images')
            ->whereNotIn('id', $swipedProfileIds)
            ->inRandomOrder()
            ->paginate(10); // Pagination as requested

        return response()->json($profiles);
    }

    /**
     * @OA\Post(
     * path="/api/swipe",
     * summary="Swipe Right (Like) or Left (Nope)",
     * @OA\Parameter(name="profile_id", in="query", required=true, @OA\Schema(type="integer")),
     * @OA\Parameter(name="is_like", in="query", required=true, @OA\Schema(type="boolean")),
     * @OA\Response(response="200", description="Swipe recorded")
     * )
     */
    public function swipe(Request $request)
    {
        $request->validate([
            'profile_id' => 'required|exists:profiles,id',
            'is_like' => 'required|boolean'
        ]);

        $swipe = Swipe::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'profile_id' => $request->profile_id
            ],
            [
                'is_like' => $request->is_like
            ]
        );

        return response()->json(['message' => 'Swipe recorded', 'data' => $swipe]);
    }

    /**
     * @OA\Get(
     * path="/api/likes",
     * summary="Get list of people the user liked",
     * @OA\Response(response="200", description="List of liked profiles")
     * )
     */
    public function getLikedProfiles(Request $request)
    {
        $user = Auth::user();

        // Get profiles where the user swiped "true" (Like)
        $likedProfiles = Profile::whereHas('swipes', function($query) use ($user) {
            $query->where('user_id', $user->id)->where('is_like', true);
        })->with('images')->get();

        return response()->json($likedProfiles);
    }

    /**
     * @OA\Post(
     * path="/api/profile",
     * summary="Create a new profile manually",
     * @OA\Response(response="201", description="Profile created")
     * )
     */
    public function createProfile(Request $request)
    {
        // 1. Validate Input
        $validated = $request->validate([
            'name' => 'required|string',
            'age' => 'required|integer',
            'location' => 'required|string',
            'bio' => 'nullable|string',
            'image_url' => 'required|url' // Accepts a direct URL string
        ]);

        // 2. Create the Profile
        $profile = Profile::create([
            'name' => $validated['name'],
            'age' => $validated['age'],
            'location' => $validated['location'],
            'bio' => $validated['bio'] ?? null,
        ]);

        // 3. Add the Image
        $profile->images()->create([
            'image_url' => $validated['image_url']
        ]);

        return response()->json([
            'message' => 'Profile created successfully', 
            'data' => $profile->load('images')
        ], 201);
    }
}