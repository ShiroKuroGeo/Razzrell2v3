<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Portfolio;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    public function allPortfolios()
    {
        $portfolios = Portfolio::where('flag', '0')->get();
        return response()->json($portfolios);
    }

    public function addPortfolio(Request $request)
    {

        $validator = validator($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        $image = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::random(32) . '.' . $extension;
            Storage::disk('public')->putFileAs('portfolios', $file, $filename);
            $image = 'portfolios/' . $filename;
        }

        $portfolio = Portfolio::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $image,
        ]);

        if($portfolio){ 
            return response()->json([
                'message' => 'Portfolio added successfully',
                'status' => 200,
                'data' => $portfolio,
                'image' => $image
            ], 200);
        }else{
            return response()->json([
                'message' => 'Failed to add portfolio',
                'status' => 500
            ], 500);
        }
    }

    public function updatePortfolio(Request $request)
    {

        $validator = validator($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        $image = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::random(32) . '.' . $extension;
            Storage::disk('public')->putFileAs('portfolios', $file, $filename);
            $image = 'portfolios/' . $filename;
        }

        $portfolio = Portfolio::find($request->id);
        $portfolio->title = $request->title;
        $portfolio->description = $request->description;
        if($image){
            $portfolio->image = $image;
        }
        $portfolio->save();

        if($portfolio){ 
            return response()->json([
                'message' => 'Portfolio updated successfully',
                'status' => 200,
                'data' => $portfolio,
                'image' => $image
            ], 200);
        }else{
            return response()->json([
                'message' => 'Failed to update portfolio',
                'status' => 500
            ], 500);
        }
    }

    public function deletePortfolio($id)
    {
        $portfolio = Portfolio::find($id);
        $portfolio->flag = '1';
        $portfolio->save();

        return response()->json([
            'message' => 'Portfolio deleted successfully',
            'status' => 200
        ], 200);
    }
}
