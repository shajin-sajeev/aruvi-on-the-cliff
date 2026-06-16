@extends('layouts.frontend')
@section('title', 'Invoice '.$booking->invoice?->invoice_number)
@section('content')
<section class="section"><div class="container"><div class="lux-card p-5"><div class="d-flex justify-content-between"><div>
				@if(isset($settings['site_logo']) && $settings['site_logo'])
					<img src="{{ asset($settings['site_logo']) }}" alt="Resort Logo" style="height:48px; max-width:150px;">
				@else
					<h1>Aruvi on the Cliff</h1>
					<p>Luxury Beachside Resort</p>
				@endif

				@if(isset($settings['site_brand_image']) && $settings['site_brand_image'])
					<div style="margin-top:8px;">
						<img src="{{ asset($settings['site_brand_image']) }}" alt="Brand Image" style="max-height:80px; max-width:220px; display:block;">
					</div>
				@endif
			</div><div class="text-end"><h4>Invoice</h4><p>{{ $booking->invoice?->invoice_number }}</p></div></div><hr><p><strong>Guest:</strong> {{ $booking->guest_name }}<br><strong>Booking:</strong> {{ $booking->booking_number }}<br><strong>Room:</strong> {{ $booking->room?->name }}</p><table class="table"><tr><th>Subtotal</th><td class="text-end">₹{{ number_format($booking->total_amount, 2) }}</td></tr><tr><th>Tax</th><td class="text-end">₹{{ number_format($booking->tax_amount, 2) }}</td></tr><tr><th>Total</th><td class="text-end fw-bold">₹{{ number_format($booking->grand_total, 2) }}</td></tr></table><button onclick="window.print()" class="btn btn-teal">Print / Save as PDF</button></div></div></section>
@endsection
