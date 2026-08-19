<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function toggle(Course $course)
    {
        $user = Auth::user();

        $existing = Wishlist::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'status' => 'removed',
                'message' => 'Course removed from wishlist.',
            ]);
        }

        Wishlist::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        return response()->json([
            'status' => 'added',
            'message' => 'Course added to wishlist!',
        ]);
    }
}
