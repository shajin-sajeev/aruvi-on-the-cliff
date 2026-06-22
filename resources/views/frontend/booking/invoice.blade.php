@extends('layouts.frontend')
@section('title', 'Invoice '.$booking->invoice?->invoice_number)
@section('content')
<section class="section section-soft">
    <div class="container" style="max-width: 780px;">
        <div class="lux-card p-4 p-md-5 invoice-container">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-4 mb-4">
                <div>
                    @if(isset($settings['site_logo']) && $settings['site_logo'])
                        <img src="{{ asset($settings['site_logo']) }}" alt="Resort Logo" style="height:48px; max-width:150px;">
                    @else
                        <h1 class="invoice-header-brand text-ink">Aruvi on the Cliff</h1>
                        <p class="text-muted small mb-0">Luxury Beachside Resort</p>
                    @endif
                    @if(isset($settings['site_brand_image']) && $settings['site_brand_image'])
                        <div style="margin-top:8px;">
                            <img src="{{ asset($settings['site_brand_image']) }}" alt="Brand Image" style="max-height:80px; max-width:220px; display:block;">
                        </div>
                    @endif
                </div>
                <div class="text-sm-end">
                    <h4 class="invoice-title fw-bold text-teal mb-1">Invoice</h4>
                    <p class="text-muted mb-0">{{ $booking->invoice?->invoice_number }}</p>
                </div>
            </div>
            <hr>
            <p class="mb-4">
                <strong>Guest:</strong> {{ $booking->guest_name }}<br>
                <strong>Booking:</strong> {{ $booking->booking_number }}<br>
                <strong>Room:</strong> {{ $booking->room?->name }}
            </p>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th>Subtotal</th>
                        <td class="text-end">&#8377;{{ number_format($booking->total_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Tax</th>
                        <td class="text-end">&#8377;{{ number_format($booking->tax_amount, 2) }}</td>
                    </tr>
                    <tr class="table-active">
                        <th>Total</th>
                        <td class="text-end fw-bold text-teal">&#8377;{{ number_format($booking->grand_total, 2) }}</td>
                    </tr>
                </table>
            </div>
            <div class="d-flex flex-column flex-sm-row gap-3 mt-4">
                <button onclick="window.print()" class="btn btn-teal px-4 py-2 fw-semibold">
                    <i class="bi bi-printer me-2"></i>Print / Save as PDF
                </button>
                <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4 py-2">
                    <i class="bi bi-house me-2"></i>Back to Home
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
