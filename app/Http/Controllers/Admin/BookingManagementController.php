<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingManagementController extends Controller
{
    public function index()
    {
        return view('admin.bookings.index', [
            'bookings' => Booking::with('room', 'user', 'invoice')->latest()->paginate(20),
            'statuses' => ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'],
        ]);
    }

    public function create()
    {
        return view('admin.bookings.form', [
            'booking' => null,
            'rooms' => Room::where('is_active', true)->orderBy('name')->get(),
            'statuses' => ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'],
        ]);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'room_id' => ['required', 'exists:rooms,id'],
                'check_in' => ['required', 'date'],
                'check_out' => ['required', 'date', 'after:check_in'],
                'adults' => ['required', 'integer', 'min:1'],
                'children' => ['nullable', 'integer', 'min:0'],
                'guest_name' => ['required', 'string', 'max:255'],
                'guest_email' => ['required', 'email', 'max:255'],
                'guest_phone' => ['required', 'string', 'max:40'],
                'special_requests' => ['nullable', 'string', 'max:3000'],
                'status' => ['required', 'in:pending,confirmed,checked_in,checked_out,cancelled'],
            ]);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['errors' => $e->errors(), 'message' => 'Validation failed.'], 422);
            }
            throw $e;
        }

        $booking = DB::transaction(function () use ($data, $request) {
            $room = Room::findOrFail($data['room_id']);
            $checkIn = Carbon::parse($data['check_in']);
            $checkOut = Carbon::parse($data['check_out']);
            $nights = max(1, $checkIn->diffInDays($checkOut));

            $subtotal = $nights * ($room->discount_price ?: $room->price_per_night);
            $tax = round($subtotal * 0.12, 2);

            $booking = Booking::create($data + [
                'booking_number' => 'AOC-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                'user_id' => $request->user()?->id,
                'children' => $data['children'] ?? 0,
                'total_amount' => $subtotal,
                'tax_amount' => $tax,
                'grand_total' => $subtotal + $tax,
                'confirmed_at' => $data['status'] === 'confirmed' ? now() : null,
            ]);

            Payment::create([
                'booking_id' => $booking->id,
                'gateway' => 'offline',
                'amount' => $booking->grand_total,
                'currency' => 'INR',
                'status' => 'pending',
            ]);

            Invoice::create([
                'booking_id' => $booking->id,
                'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6)),
                'subtotal' => $booking->total_amount,
                'tax' => $booking->tax_amount,
                'total' => $booking->grand_total,
                'issued_at' => now(),
            ]);

            return $booking;
        });

        if ($request->ajax()) {
            return response()->json(['redirect' => route('admin.bookings.index')]);
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Booking created successfully.');
    }

    public function edit(Booking $booking)
    {
        return view('admin.bookings.form', [
            'booking' => $booking,
            'rooms' => Room::where('is_active', true)->orderBy('name')->get(),
            'statuses' => ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'],
        ]);
    }

    public function update(Request $request, Booking $booking)
    {
        try {
            $data = $request->validate([
                'room_id' => ['required', 'exists:rooms,id'],
                'check_in' => ['required', 'date'],
                'check_out' => ['required', 'date', 'after:check_in'],
                'adults' => ['required', 'integer', 'min:1'],
                'children' => ['nullable', 'integer', 'min:0'],
                'guest_name' => ['required', 'string', 'max:255'],
                'guest_email' => ['required', 'email', 'max:255'],
                'guest_phone' => ['required', 'string', 'max:40'],
                'special_requests' => ['nullable', 'string', 'max:3000'],
                'status' => ['required', 'in:pending,confirmed,checked_in,checked_out,cancelled'],
            ]);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json(['errors' => $e->errors(), 'message' => 'Validation failed.'], 422);
            }
            throw $e;
        }

        DB::transaction(function () use ($booking, $data) {
            $room = Room::findOrFail($data['room_id']);
            $checkIn = Carbon::parse($data['check_in']);
            $checkOut = Carbon::parse($data['check_out']);
            $nights = max(1, $checkIn->diffInDays($checkOut));

            $subtotal = $nights * ($room->discount_price ?: $room->price_per_night);
            $tax = round($subtotal * 0.12, 2);

            $booking->update($data + [
                'total_amount' => $subtotal,
                'tax_amount' => $tax,
                'grand_total' => $subtotal + $tax,
                'confirmed_at' => $data['status'] === 'confirmed' && !$booking->confirmed_at ? now() : $booking->confirmed_at,
            ]);

            if ($booking->invoice) {
                $booking->invoice->update([
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $subtotal + $tax,
                ]);
            }
        });

        if ($request->ajax()) {
            return response()->json(['redirect' => route('admin.bookings.index')]);
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Booking updated successfully.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Booking deleted successfully.');
    }
}
