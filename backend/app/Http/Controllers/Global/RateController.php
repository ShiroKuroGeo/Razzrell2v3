<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;

class RateController extends Controller
{
    public function addRating(Request $request)
    {
        $rating = new Rating();
        $rating->package_id = $request->package_id;
        $rating->user_id = $request->user_id;
        $rating->booking_id = $request->booking_id;
        $rating->rating = $request->rating;
        $rating->review = $request->review;
        $rating->save();

        return response()->json(['message' => 'Rating added successfully']);
    }

    public function getAllRatings($id)
    {
        $ratings = Rating::where('user_id', $id)->get();
        return response()->json(['ratings' => $ratings]);
    }
}
