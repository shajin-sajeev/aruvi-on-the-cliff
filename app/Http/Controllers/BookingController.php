<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        return view('frontend.booking.create');
    }

    public function availability(Request $request)
    {
        $data = $request->validate([
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'adults' => ['nullable', 'integer', 'min:1'],
            'children' => ['nullable', 'integer', 'min:0'],
        ]);

        $bookedRoomIds = Booking::whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in', '<', $data['check_out'])
            ->where('check_out', '>', $data['check_in'])
            ->pluck('room_id')
            ->all();

        $availableRoomsCount = Room::where('is_active', true)
            ->whereNotIn('id', $bookedRoomIds)
            ->count();

        return response()->json(['available_count' => $availableRoomsCount]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'adults' => ['required', 'integer', 'min:1'],
            'children' => ['nullable', 'integer', 'min:0'],
            'guest_name' => ['required', 'string', 'max:255'],
            'guest_email' => ['required', 'email', 'max:255'],
            'guest_phone' => ['required', 'string', 'max:40'],
            'special_requests' => ['nullable', 'string', 'max:3000'],
        ]);

        $booking = DB::transaction(function () use ($data, $request) {
            // Find booked rooms
            $bookedRoomIds = Booking::whereIn('status', ['pending', 'confirmed', 'checked_in'])
                ->where('check_in', '<', $data['check_out'])
                ->where('check_out', '>', $data['check_in'])
                ->pluck('room_id')
                ->all();

            // Find first available room/cottage
            $availableCottage = Room::where('is_active', true)
                ->whereNotIn('id', $bookedRoomIds)
                ->lockForUpdate()
                ->first();

            if (!$availableCottage) {
                throw ValidationException::withMessages(['check_in' => 'No cottages are available for the selected dates.']);
            }

            $room = $availableCottage;
            $nights = max(1, Carbon::parse($data['check_in'])->diffInDays(Carbon::parse($data['check_out'])));
            $subtotal = $nights * ($room->discount_price ?: $room->price_per_night);
            $tax = round($subtotal * 0.12, 2);
            
            $booking = Booking::create($data + [
                'room_id' => $room->id,
                'user_id' => $request->user()?->id,
                'booking_number' => 'AOC-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'children' => $data['children'] ?? 0,
                'status' => 'pending',
                'total_amount' => $subtotal,
                'tax_amount' => $tax,
                'grand_total' => $subtotal + $tax,
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
                'invoice_number' => 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'subtotal' => $booking->total_amount,
                'tax' => $booking->tax_amount,
                'total' => $booking->grand_total,
                'issued_at' => now(),
            ]);

            return $booking;
        });

        return redirect()->route('booking.confirmation', $booking->booking_number)->with('success', 'Your stay request has been reserved pending confirmation.');
    }

    public function history(Request $request)
    {
        abort(404);
    }

    public function show(string $bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)->firstOrFail();

        return view('frontend.booking.show', ['booking' => $booking->load('room.type', 'invoice', 'payments')]);
    }

    public function invoice(string $bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)->firstOrFail();

        return view('frontend.booking.invoice', [
            'booking' => $booking->load('room', 'invoice', 'user'),
            'settings' => Setting::all()->pluck('value', 'key'),
        ]);
    }
}
