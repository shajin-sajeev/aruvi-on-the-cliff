@extends('layouts.frontend')
@section('title', 'Secure Payment - Aruvi on the Cliff')
@section('content')

{{-- ─── Scoped styles ─────────────────────────────────────────────────── --}}
<style>
.pay-hero {
    background: linear-gradient(135deg,#0a2a2a 0%,#0d3d3d 60%,#1a5050 100%);
    min-height: 220px;
    display: flex;
    align-items: center;
    padding: 80px 0 40px;
}
.pay-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,.12);
    overflow: hidden;
}
.pay-card-header {
    background: linear-gradient(135deg,#056363,#0a4040);
    color: #fff;
    padding: 28px 32px;
}
.pay-amount-badge {
    background: rgba(255,255,255,.15);
    border: 1.5px solid rgba(255,255,255,.3);
    border-radius: 12px;
    padding: 12px 22px;
    display: inline-block;
}
.booking-detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: .9rem;
}
.booking-detail-row:last-child { border-bottom: none; }
.badge-status {
    font-size:.75rem;
    padding: 4px 12px;
    border-radius: 20px;
    font-weight: 600;
    letter-spacing: .04em;
}
.razorpay-btn {
    background: linear-gradient(135deg,#056363,#078080);
    color: #fff;
    border: none;
    border-radius: 12px;
    padding: 16px 40px;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all .25s ease;
    box-shadow: 0 8px 24px rgba(5,99,99,.35);
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}
.razorpay-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 32px rgba(5,99,99,.45);
}
.razorpay-btn:active { transform: translateY(0); }
.trust-badges { display:flex; gap:16px; flex-wrap:wrap; justify-content:center; }
.trust-badge {
    display:flex;
    align-items:center;
    gap:6px;
    font-size:.78rem;
    color:#666;
}
.trust-badge i { color:#056363; font-size:1rem; }
.stripe-line {
    height: 4px;
    background: linear-gradient(90deg,#056363,#20b2aa,#48d1cc,#20b2aa,#056363);
    border-radius:2px;
}
.summary-icon {
    width: 36px; height: 36px;
    background: #e8f5f5;
    border-radius: 50%;
    display:flex; align-items:center; justify-content:center;
    flex-shrink:0;
}
</style>

{{-- ─── Hero ───────────────────────────────────────────────────────────── --}}
<div class="pay-hero">
    <div class="container text-center text-white">
        <span class="badge bg-white text-teal px-3 py-2 mb-3 fw-bold small">
            <i class="bi bi-lock-fill me-1"></i> SECURE CHECKOUT
        </span>
        <h1 class="display-5 fw-bold font-serif mb-2">One Step Away from Paradise</h1>
        <p class="text-white-50 mb-0">Complete your payment to confirm your cliffside stay.</p>
    </div>
</div>

{{-- ─── Flash messages ─────────────────────────────────────────────────── --}}
@if(session('error'))
<div class="container mt-3" style="max-width:900px;">
    <div class="alert alert-danger d-flex gap-2 align-items-center rounded-3">
        <i class="bi bi-x-circle-fill flex-shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
</div>
@endif

{{-- ─── Main Content ────────────────────────────────────────────────────── --}}
<section style="background:#f4f7f7; padding: 40px 0 80px;">
<div class="container" style="max-width:900px;">
    <div class="row g-4">

        {{-- Left: Booking Summary ──────────────────────────────────────── --}}
        <div class="col-lg-5">
            <div class="pay-card h-100">
                <div class="pay-card-header">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div style="width:44px;height:44px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-house-heart fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-5 font-serif">Booking Summary</div>
                            <div style="font-size:.8rem;opacity:.75;">{{ $booking->booking_number }}</div>
                        </div>
                    </div>
                    <div class="pay-amount-badge text-center w-100">
                        <div style="font-size:.75rem;opacity:.8;letter-spacing:.08em;">TOTAL PAYABLE</div>
                        <div class="fw-bold" style="font-size:2rem;letter-spacing:-.02em;">
                            ₹{{ number_format($booking->grand_total, 2) }}
                        </div>
                        <div style="font-size:.75rem;opacity:.7;">Including 12% GST</div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="booking-detail-row">
                        <div class="d-flex align-items-center gap-2">
                            <div class="summary-icon"><i class="bi bi-door-open text-teal small"></i></div>
                            <span class="text-muted">Room</span>
                        </div>
                        <span class="fw-semibold text-ink text-end" style="max-width:55%;">{{ $booking->room?->name ?? 'Cottage' }}</span>
                    </div>
                    <div class="booking-detail-row">
                        <div class="d-flex align-items-center gap-2">
                            <div class="summary-icon"><i class="bi bi-calendar-check text-teal small"></i></div>
                            <span class="text-muted">Check-in</span>
                        </div>
                        <span class="fw-semibold text-ink">{{ $booking->check_in->format('d M Y') }}</span>
                    </div>
                    <div class="booking-detail-row">
                        <div class="d-flex align-items-center gap-2">
                            <div class="summary-icon"><i class="bi bi-calendar-x text-teal small"></i></div>
                            <span class="text-muted">Check-out</span>
                        </div>
                        <span class="fw-semibold text-ink">{{ $booking->check_out->format('d M Y') }}</span>
                    </div>
                    <div class="booking-detail-row">
                        <div class="d-flex align-items-center gap-2">
                            <div class="summary-icon"><i class="bi bi-moon-stars text-teal small"></i></div>
                            <span class="text-muted">Nights</span>
                        </div>
                        <span class="fw-semibold text-ink">
                            {{ max(1, $booking->check_in->diffInDays($booking->check_out)) }} Night(s)
                        </span>
                    </div>
                    <div class="booking-detail-row">
                        <div class="d-flex align-items-center gap-2">
                            <div class="summary-icon"><i class="bi bi-people text-teal small"></i></div>
                            <span class="text-muted">Guests</span>
                        </div>
                        <span class="fw-semibold text-ink">
                            {{ $booking->adults }} Adult{{ $booking->adults > 1 ? 's':'' }}
                            @if($booking->children > 0), {{ $booking->children }} Child@endif
                        </span>
                    </div>
                    <div class="booking-detail-row">
                        <div class="d-flex align-items-center gap-2">
                            <div class="summary-icon"><i class="bi bi-person-badge text-teal small"></i></div>
                            <span class="text-muted">Guest</span>
                        </div>
                        <span class="fw-semibold text-ink text-end" style="max-width:55%;">{{ $booking->guest_name }}</span>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between small text-muted mb-2">
                        <span>Subtotal</span>
                        <span class="text-ink fw-semibold">₹{{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-3">
                        <span>GST (12%)</span>
                        <span class="text-ink fw-semibold">₹{{ number_format($booking->tax_amount, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold">
                        <span class="text-ink font-serif">Grand Total</span>
                        <span class="text-teal fs-5">₹{{ number_format($booking->grand_total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Payment Panel ─────────────────────────────────────────── --}}
        <div class="col-lg-7">
            <div class="pay-card">
                <div class="stripe-line"></div>

                <div class="p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div style="width:64px;height:64px;background:linear-gradient(135deg,#e8f5f5,#c5e8e8);border-radius:50%;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;">
                            <i class="bi bi-credit-card-2-front text-teal" style="font-size:1.6rem;"></i>
                        </div>
                        <h4 class="fw-bold font-serif text-ink mb-1">Pay Securely</h4>
                        <p class="text-muted small">Powered by Razorpay — supports UPI, Cards, Net Banking & Wallets</p>
                    </div>

                    {{-- Accepted payment methods --}}
                    <div class="d-flex gap-2 flex-wrap justify-content-center mb-4">
                        <span class="badge bg-light text-muted border px-2 py-2 small"><i class="bi bi-phone me-1"></i>UPI</span>
                        <span class="badge bg-light text-muted border px-2 py-2 small"><i class="bi bi-credit-card me-1"></i>Visa</span>
                        <span class="badge bg-light text-muted border px-2 py-2 small"><i class="bi bi-credit-card-fill me-1"></i>Mastercard</span>
                        <span class="badge bg-light text-muted border px-2 py-2 small"><i class="bi bi-bank me-1"></i>Net Banking</span>
                        <span class="badge bg-light text-muted border px-2 py-2 small"><i class="bi bi-wallet me-1"></i>Wallets</span>
                    </div>

                    {{-- Hidden form for payment callback --}}
                    <form id="razorpay-callback-form" action="{{ route('payment.callback') }}" method="POST">
                        @csrf
                        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                        <input type="hidden" name="razorpay_order_id"   id="razorpay_order_id">
                        <input type="hidden" name="razorpay_signature"  id="razorpay_signature">
                        <input type="hidden" name="booking_id"          value="{{ $booking->id }}">
                    </form>

                    {{-- Pay Button --}}
                    <button class="razorpay-btn" id="rzp-pay-btn" type="button">
                        <i class="bi bi-lock-fill"></i>
                        Pay ₹{{ number_format($booking->grand_total, 2) }} Now
                    </button>

                    <div class="text-center my-4">
                        <div class="trust-badges">
                            <div class="trust-badge"><i class="bi bi-shield-check"></i>SSL Encrypted</div>
                            <div class="trust-badge"><i class="bi bi-patch-check"></i>PCI Compliant</div>
                            <div class="trust-badge"><i class="bi bi-arrow-counterclockwise"></i>Instant Confirmation</div>
                        </div>
                    </div>

                    <div class="alert alert-light border rounded-3 small text-muted text-center mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        After payment, you'll receive an instant booking confirmation with your invoice.
                        Cancellation as per our <a href="{{ route('policies.show','cancellation-policy') }}" class="text-teal">cancellation policy</a>.
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</section>

{{-- ─── Razorpay JS ─────────────────────────────────────────────────────── --}}
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    key:         '{{ config('services.razorpay.key_id') }}',
    amount:      {{ (int) round($booking->grand_total * 100) }},
    currency:    'INR',
    name:        'Aruvi on the Cliff',
    description: 'Booking {{ $booking->booking_number }}',
    order_id:    '{{ $razorpayOrderId }}',
    image:       '{{ asset('images/logo.svg') }}',
    prefill: {
        name:    '{{ addslashes($booking->guest_name) }}',
        email:   '{{ addslashes($booking->guest_email) }}',
        contact: '{{ addslashes($booking->guest_phone) }}'
    },
    notes: {
        booking_number: '{{ $booking->booking_number }}'
    },
    theme: {
        color: '#056363'
    },
    handler: function (response) {
        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
        document.getElementById('razorpay_order_id').value   = response.razorpay_order_id;
        document.getElementById('razorpay_signature').value  = response.razorpay_signature;
        document.getElementById('razorpay-callback-form').submit();
    },
    modal: {
        ondismiss: function () {
            document.getElementById('rzp-pay-btn').disabled = false;
            document.getElementById('rzp-pay-btn').innerHTML =
                '<i class="bi bi-lock-fill"></i> Pay ₹{{ number_format($booking->grand_total, 2) }} Now';
        }
    }
};

var rzp = new Razorpay(options);

document.getElementById('rzp-pay-btn').addEventListener('click', function () {
    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Opening Payment...';
    rzp.open();
});
</script>
@endsection
