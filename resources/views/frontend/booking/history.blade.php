@extends('layouts.frontend')
@section('title', 'Booking History - Aruvi on the Cliff')
@section('content')
<section class="section"><div class="container"><h1 class="fw-bold mb-4">Booking History</h1><div class="table-responsive lux-card"><table class="table mb-0"><thead><tr><th>Booking</th><th>Room</th><th>Dates</th><th>Status</th><th>Total</th><th></th></tr></thead><tbody>@foreach($bookings as $booking)<tr><td>{{ $booking->booking_number }}</td><td>{{ $booking->room?->name }}</td><td>{{ $booking->check_in->format('d M Y') }} - {{ $booking->check_out->format('d M Y') }}</td><td><span class="badge text-bg-info">{{ str($booking->status)->headline() }}</span></td><td>₹{{ number_format($booking->grand_total, 2) }}</td><td><a href="{{ route('booking.show', $booking) }}">View</a></td></tr>@endforeach</tbody></table></div><div class="mt-4">{{ $bookings->links() }}</div></div></section>
@endsection
