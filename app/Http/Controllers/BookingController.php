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
use Razorpay\Api\Api as RazorpayApi;

class BookingController extends Controller
{
    // ── 1. Show booking form ─────────────────────────────────────────────
    public function create(Request $request)
    {
        return view('frontend.booking.create');
    }

    // ── 2. Availability JSON endpoint ────────────────────────────────────
    public function availability(Request $request)
    {
        $data = $request->validate([
            'check_in'  => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'adults'    => ['nullable', 'integer', 'min:1'],
            'children'  => ['nullable', 'integer', 'min:0'],
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

    // ── 3. Store booking + create Razorpay order → redirect to payment ──
    public function store(Request $request)
    {
        $data = $request->validate([
            'check_in'        => ['required', 'date', 'after_or_equal:today'],
            'check_out'       => ['required', 'date', 'after:check_in'],
            'adults'          => ['required', 'integer', 'min:1'],
            'children'        => ['nullable', 'integer', 'min:0'],
            'guest_name'      => ['required', 'string', 'max:255'],
            'guest_email'     => ['required', 'email', 'max:255'],
            'guest_phone'     => ['required', 'string', 'max:40'],
            'special_requests' => ['nullable', 'string', 'max:3000'],
        ]);

        $booking = DB::transaction(function () use ($data, $request) {
            // Check availability
            $bookedRoomIds = Booking::whereIn('status', ['pending', 'confirmed', 'checked_in'])
                ->where('check_in', '<', $data['check_out'])
                ->where('check_out', '>', $data['check_in'])
                ->pluck('room_id')
                ->all();

            $availableCottage = Room::where('is_active', true)
                ->whereNotIn('id', $bookedRoomIds)
                ->lockForUpdate()
                ->first();

            if (! $availableCottage) {
                throw ValidationException::withMessages([
                    'check_in' => 'No cottages are available for the selected dates.',
                ]);
            }

            $room     = $availableCottage;
            $nights   = max(1, Carbon::parse($data['check_in'])->diffInDays(Carbon::parse($data['check_out'])));
            $subtotal = $nights * ($room->discount_price ?: $room->price_per_night);
            $tax      = round($subtotal * 0.12, 2);

            $booking = Booking::create($data + [
                'room_id'        => $room->id,
                'user_id'        => $request->user()?->id,
                'booking_number' => 'AOC-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'children'       => $data['children'] ?? 0,
                'status'         => 'payment_pending',
                'total_amount'   => $subtotal,
                'tax_amount'     => $tax,
                'grand_total'    => $subtotal + $tax,
            ]);

            // Create razorpay order
            $razorpay = new RazorpayApi(
                config('services.razorpay.key_id'),
                config('services.razorpay.key_secret')
            );

            $rzpOrder = $razorpay->order->create([
                'receipt'        => $booking->booking_number,
                'amount'         => (int) round($booking->grand_total * 100), // paise
                'currency'       => 'INR',
                'payment_capture' => 1,
            ]);

            // Create Payment record
            Payment::create([
                'booking_id'     => $booking->id,
                'gateway'        => 'razorpay',
                'transaction_id' => $rzpOrder->id,
                'amount'         => $booking->grand_total,
                'currency'       => 'INR',
                'status'         => 'pending',
                'payload'        => json_encode(['razorpay_order_id' => $rzpOrder->id]),
            ]);

            // Create Invoice (draft until paid)
            Invoice::create([
                'booking_id'     => $booking->id,
                'invoice_number' => 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(6)),
                'subtotal'       => $booking->total_amount,
                'tax'            => $booking->tax_amount,
                'total'          => $booking->grand_total,
                'issued_at'      => now(),
            ]);

            $booking->razorpay_order_id = $rzpOrder->id;
            return $booking;
        });

        return redirect()->route('payment.show', $booking->booking_number);
    }

    // ── 4. Show payment page ─────────────────────────────────────────────
    public function paymentPage(string $bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with('room', 'payments')
            ->firstOrFail();

        // If already paid, redirect to confirmation
        if ($booking->status === 'confirmed') {
            return redirect()->route('booking.confirmation', $booking->booking_number)
                ->with('success', 'Payment already completed.');
        }

        $payment = $booking->payments()->where('gateway', 'razorpay')->latest()->first();

        if (! $payment || ! $payment->transaction_id) {
            abort(404, 'Payment order not found.');
        }

        return view('frontend.booking.payment', [
            'booking'          => $booking,
            'razorpayOrderId'  => $payment->transaction_id,
        ]);
    }

    // ── 5. Handle Razorpay callback ───────────────────────────────────────
    public function paymentCallback(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_order_id'   => ['required', 'string'],
            'razorpay_signature'  => ['required', 'string'],
            'booking_id'          => ['required', 'integer'],
        ]);

        $booking = Booking::findOrFail($request->booking_id);
        $payment = $booking->payments()->where('gateway', 'razorpay')->latest()->first();

        try {
            $razorpay = new RazorpayApi(
                config('services.razorpay.key_id'),
                config('services.razorpay.key_secret')
            );

            // Verify signature
            $attributes = [
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
            ];

            $razorpay->utility->verifyPaymentSignature($attributes);

            // Signature valid — mark as paid
            DB::transaction(function () use ($booking, $payment, $request) {
                $payment->update([
                    'transaction_id' => $request->razorpay_payment_id,
                    'status'         => 'paid',
                    'paid_at'        => now(),
                    'payload'        => json_encode([
                        'razorpay_order_id'   => $request->razorpay_order_id,
                        'razorpay_payment_id' => $request->razorpay_payment_id,
                        'razorpay_signature'  => $request->razorpay_signature,
                    ]),
                ]);

                $booking->update(['status' => 'confirmed']);
            });

            return redirect()->route('booking.confirmation', $booking->booking_number)
                ->with('success', 'Payment successful! Your booking is now confirmed.');

        } catch (\Exception $e) {
            // Signature verification failed — mark as failed
            if ($payment) {
                $payment->update([
                    'status'  => 'failed',
                    'payload' => json_encode([
                        'error'               => $e->getMessage(),
                        'razorpay_order_id'   => $request->razorpay_order_id,
                        'razorpay_payment_id' => $request->razorpay_payment_id ?? null,
                    ]),
                ]);
            }

            return redirect()->route('payment.show', $booking->booking_number)
                ->with('error', 'Payment verification failed. Please try again or contact support.');
        }
    }

    // ── 6. Booking confirmation page ─────────────────────────────────────
    public function show(string $bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)->firstOrFail();

        return view('frontend.booking.show', [
            'booking'  => $booking->load('room.type', 'invoice', 'payments'),
            'settings' => Setting::all()->pluck('value', 'key'),
        ]);
    }

    // ── 7. Invoice download ───────────────────────────────────────────────
    public function invoice(string $bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)->firstOrFail();

        return view('frontend.booking.invoice', [
            'booking'  => $booking->load('room', 'invoice', 'user'),
            'settings' => Setting::all()->pluck('value', 'key'),
        ]);
    }
}
