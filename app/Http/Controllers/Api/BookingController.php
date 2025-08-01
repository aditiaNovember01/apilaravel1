<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        return response()->json(Booking::all());
    }

    public function show($id)
    {
        $booking = Booking::findOrFail($id);
        return response()->json($booking);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'barber_id' => 'required|exists:barbers,id',
            'booking_date' => 'required|date',
            'booking_time' => 'required',
            'status' => 'required|in:pending,confirmed,done,cancelled',
            'amount' => 'required|numeric',
            'payment_status' => 'required|in:unpaid,paid',
            'proof_of_payment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|',
        ]);

        // Proses upload file jika ada
        if ($request->hasFile('proof_of_payment')) {
            $file = $request->file('proof_of_payment');
            $path = $file->store('proofs', 'public');
            $validated['proof_of_payment'] = $path;
        } else {
            $validated['proof_of_payment'] = null;
        }

        $booking = Booking::create($validated);

        return response()->json($booking, 201);
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $validated = $request->validate([
            'user_id' => 'sometimes|required|exists:users,id',
            'barber_id' => 'sometimes|required|exists:barbers,id',
            'booking_date' => 'sometimes|required|date',
            'booking_time' => 'sometimes|required',
            'status' => 'sometimes|required|in:pending,confirmed,done,cancelled',
            'amount' => 'sometimes|required|numeric',
            'payment_status' => 'sometimes|required|in:unpaid,paid',
            'proof_of_payment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|',
        ]);

        // Proses upload file jika ada
        if ($request->hasFile('proof_of_payment')) {
            $file = $request->file('proof_of_payment');
            $path = $file->store('proofs', 'public');
            $validated['proof_of_payment'] = $path;
        }

        $booking->update($validated);

        return response()->json($booking);
    }

    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->delete();
        return response()->json(null, 204);
    }
}