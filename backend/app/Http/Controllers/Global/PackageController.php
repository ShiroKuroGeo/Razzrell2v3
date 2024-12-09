<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Package;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PackageController extends Controller
{
    public function index(){
        try{
            $packages = Package::where('flag', '0')
                ->with(['ratings' => function($query) {
                    $query->where('status', 'active');
                }])
                ->withCount(['bookings' => function($query) {
                    $query->whereIn('status', ['pending', 'confirmed', 'ongoing', 'preparing', 'completed']);
                }])
                ->get()
                ->map(function($package) {
                    $avgRating = $package->ratings->avg('rating') ?? 0;
                    $package->rating = round($avgRating, 1);
                    $package->reviewsCount = $package->ratings->count();
                    $package->bookingsCount = $package->bookings_count;
                    unset($package->ratings);
                    unset($package->bookings_count);
                    return $package;
                });

            return response()->json($packages);
        }
        catch(\Exception $e){
            return response()->json([
                'message' => 'Error fetching packages',
                'status' => 500
            ], 500);
        }
    }
    
    public function addPackage(Request $request)
    {
        $image = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::random(32) . '.' . $extension;
            Storage::disk('public')->putFileAs('packages', $file, $filename);
            $image = 'packages/' . $filename;
        }

        $package = Package::create([
            'packs' => $request->packs,
            'package_name' => $request->name,
            'package_image' => $image,
            'package_description' => $request->description,
            'package_type' => $request->eventType,
            'package_price' => $request->price,
            'package_inclusion' => $request->inclusions,
        ]);

        return response()->json([
            'message' => 'Package added successfully',
            'status' => 200,
            'data' => $package
        ], 200);
    }

    public function updatePackage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'name' => 'required', 
                'eventType' => 'required',
                'price' => 'required',
                'description' => 'required',
                'inclusions' => 'required',
                'packs' => 'required',
                'status' => 'required',
                'image' => 'nullable|image|mimes:jpeg,png,webp|max:2048'
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
                Storage::disk('public')->putFileAs('packages', $file, $filename);
                $image = 'packages/' . $filename;
            }

            $package = Package::find($request->id);

            $package->update([
                'package_name' => $request->name,
                'package_image' => $image ?? $package->package_image,
                'packs' => $request->packs,
                'package_description' => $request->description,
                'package_type' => $request->eventType,
                'package_price' => $request->price,
                'status' => $request->status,
                'package_inclusion' => $request->inclusions,
            ]);
            
            if(!$package){
                return response()->json([
                    'message' => 'Package not found', 
                    'status' => 404
                ], 404);
            }else{
                return response()->json([
                    'message' => 'Package updated successfully',
                    'status' => 200,
                    'data' => $package
                ], 200);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error validating request',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }

    }

    public function getPackageById($id)
    {
        $package = Package::find($id);
        return response()->json($package);
    }

    public function deletePackage(Request $request, $id)
    {
        $package = Package::find($id);
        
        // Delete the image file if it exists
        if ($package->package_image) {
            Storage::disk('public')->delete($package->package_image);
        }
        
        $package->update([
            'flag' => '1'
        ]);
        
        return response()->json([
            'message' => 'Package updated successfully',
            'status' => 200
        ]);
    }
}
