<?php

namespace App\Http\Controllers\Global;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['package'])->get();
        return response()->json($bookings);
    }

    public function addBooking(Request $request)
    {
        $booking = Booking::create([
            'user_id' => $request->user_id,
            'package_id' => $request->package_id,
            'full_name' => $request->fullName,
            'event_date' => $request->event_date,
            'event_time' => $request->event_time,
            'venue_name' => $request->venue_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'special_requests' => $request->special_requests,
            'payment_method' => $request->payment_method,
        ]);

        return response()->json([
            'message' => 'Booking created successfully',
            'booking' => $booking,
            'status' => 200
        ]);
    }

    public function getBookingById($id)
    {
        $booking = Booking::with(['package'])->find($id);
        return response()->json($booking);
    }

    public function updateBooking(Request $request)
    {

        $booking = Booking::find($request->id);

        $booking->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Booking updated successfully',
            'booking' => 'Global Booking Controller',
            'status' => 200,
        ]);

    }

    public function getBookingByUserId($id)
    {
        $bookings = Booking::where('user_id', $id)->with(['package'])->get();
        return response()->json([
            'bookings' => $bookings,
            'status' => 200
        ]);
    }
}
