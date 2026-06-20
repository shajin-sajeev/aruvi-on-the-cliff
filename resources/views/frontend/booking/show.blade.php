@extends('layouts.frontend')

@section('title', 'Booking Confirmation - Aruvi on the Cliff')

@section('content')
<section class="section section-soft" style="margin-top: 50px;">
    <div class="container" style="max-width: 800px;">
        <div class="text-center mb-4">
            <span class="badge bg-teal-soft text-teal px-3 py-2 mb-2"><i class="bi bi-patch-check-fill me-1"></i> RESERVATION RECEIVED</span>
            <h1 class="display-6 font-serif fw-bold text-ink">Thank you for booking!</h1>
            <p class="text-muted">Your booking request has been submitted. A detailed receipt has been generated below.</p>
        </div>

        <!-- Printable Invoice Report Container -->
        <div id="invoice-report" class="bg-white border rounded-4 p-4 p-md-5 mb-4 shadow">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center border-bottom pb-4 mb-4 gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img src="{{ asset($settings['site_logo'] ?? 'images/logo.svg') }}" alt="Resort Logo" style="height: 48px; max-width: 150px;">
                    <div>
                        <h4 class="font-serif fw-bold text-ink mb-0">Aruvi on the Cliff</h4>
                        <small class="text-teal text-uppercase tracking-wider extra-small fw-bold">luxury beachside resort</small>
                    </div>
                </div>
                <div class="text-sm-end">
                    <h5 class="text-teal font-sans uppercase tracking-wider mb-1 fw-bold">Booking Receipt</h5>
                    <span class="badge bg-teal-soft text-teal font-sans fw-bold">ID: {{ $booking->booking_number }}</span>
                </div>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-sm-6">
                    <h6 class="fw-bold text-ink mb-2 font-serif">Guest Details</h6>
                    <p class="text-muted small mb-0">
                        <strong>Name:</strong> {{ $booking->guest_name }}<br>
                        <strong>Email:</strong> {{ $booking->guest_email }}<br>
                        <strong>Phone:</strong> {{ $booking->guest_phone }}
                    </p>
                </div>
                <div class="col-sm-6 text-sm-end">
                    <h6 class="fw-bold text-ink mb-2 font-serif">Invoice Info</h6>
                    <p class="text-muted small mb-0">
                        <strong>Invoice Number:</strong> {{ $booking->invoice?->invoice_number ?: 'TBD' }}<br>
                        <strong>Issued Date:</strong> {{ now()->format('d M Y') }}<br>
                        <strong>Booking Status:</strong> <span class="text-capitalize fw-bold text-teal">{{ $booking->status }}</span>
                    </p>
                </div>
            </div>

            <div class="border rounded-3 overflow-hidden mb-4">
                <table class="table table-borderless align-middle mb-0 small">
                    <thead class="bg-light text-muted border-bottom">
                        <tr>
                            <th class="ps-3 py-3">Reservation Details</th>
                            <th class="py-3 text-center">Nights</th>
                            <th class="py-3 text-center">Guests</th>
                            <th class="pe-3 py-3 text-end">Rate / Night</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-bottom border-light">
                            <td class="ps-3 py-3">
                                <strong class="text-ink d-block">{{ $booking->room?->name }}</strong>
                                <small class="text-muted">{{ $booking->room?->type?->name }}</small>
                                <div class="mt-1 extra-small text-muted">
                                    Check-In: <strong>{{ $booking->check_in->format('d M Y') }}</strong> (from 2:00 PM)<br>
                                    Check-Out: <strong>{{ $booking->check_out->format('d M Y') }}</strong> (by 11:00 AM)
                                </div>
                            </td>
                            <td class="py-3 text-center align-middle">
                                {{ max(1, $booking->check_in->diffInDays($booking->check_out)) }}
                            </td>
                            <td class="py-3 text-center align-middle">
                                {{ $booking->adults }} Adults @if($booking->children), {{ $booking->children }} Child @endif
                            </td>
                            <td class="pe-3 py-3 text-end align-middle fw-semibold text-ink font-sans">
                                ₹{{ number_format($booking->room?->discount_price ?: $booking->room?->price_per_night) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row justify-content-end">
                <div class="col-md-6">
                    <table class="table table-borderless mb-0 small font-sans">
                        <tr>
                            <td class="text-muted py-1">Room Subtotal</td>
                            <td class="text-end text-ink fw-semibold py-1">₹{{ number_format($booking->total_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">Tax (12% GST)</td>
                            <td class="text-end text-ink fw-semibold py-1">₹{{ number_format($booking->tax_amount, 2) }}</td>
                        </tr>
                        <tr class="border-top border-light">
                            <td class="text-ink fw-bold py-2 font-serif">Grand Total</td>
                            <td class="text-end text-teal fw-bold fs-5 py-2">₹{{ number_format($booking->grand_total, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($booking->special_requests)
                <div class="mt-4 pt-4 border-top border-light">
                    <h6 class="fw-bold text-ink mb-2 font-serif">Special Requests</h6>
                    <p class="text-muted small mb-0" style="font-style: italic;">“{{ $booking->special_requests }}”</p>
                </div>
            @endif
        </div>

        {{-- Payment status alert --}}
        @php
            $latestPayment = $booking->payments->sortByDesc('created_at')->first();
        @endphp
        @if($latestPayment && $latestPayment->status === 'paid')
            <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <div><strong>Payment Confirmed!</strong> ₹{{ number_format($latestPayment->amount, 2) }} received via {{ ucfirst($latestPayment->gateway) }}.</div>
            </div>
        @elseif($latestPayment && $latestPayment->status === 'pending' && $booking->status === 'payment_pending')
            <div class="alert alert-warning d-flex align-items-center gap-2 rounded-3 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                <div>
                    <strong>Payment Pending.</strong> Your booking is not confirmed until payment is completed.
                    <a href="{{ route('payment.show', $booking->booking_number) }}" class="btn btn-warning btn-sm ms-2 fw-bold">
                        <i class="bi bi-credit-card me-1"></i>Pay Now
                    </a>
                </div>
            </div>
        @elseif($latestPayment && $latestPayment->status === 'failed')
            <div class="alert alert-danger d-flex align-items-center gap-2 rounded-3 mb-4" role="alert">
                <i class="bi bi-x-circle-fill fs-5"></i>
                <div>
                    <strong>Payment Failed.</strong> Please retry your payment.
                    <a href="{{ route('payment.show', $booking->booking_number) }}" class="btn btn-danger btn-sm ms-2 fw-bold">
                        <i class="bi bi-arrow-repeat me-1"></i>Retry Payment
                    </a>
                </div>
            </div>
        @endif

        <div class="d-flex justify-content-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-outline-teal px-4 py-2"><i class="bi bi-house-door me-2"></i>Return Home</a>
            <button onclick="downloadInvoicePDF()" class="btn btn-teal px-4 py-2 shadow-sm"><i class="bi bi-file-earmark-pdf me-2"></i>Download PDF Report</button>
        </div>
    </div>
</section>

<!-- Include html2pdf.js CDN library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function downloadInvoicePDF() {
    const element = document.getElementById('invoice-report');
    const opt = {
        margin:       10,
        filename:     'Aruvi_Booking_{{ $booking->booking_number }}.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(element).save();
}
</script>
@endsection
