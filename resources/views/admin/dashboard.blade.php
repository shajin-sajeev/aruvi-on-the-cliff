@extends('layouts.admin')

@section('title', 'Dashboard Analytics')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fw-bold text-ink mb-1">Dashboard Analytics</h1>
        <p class="text-muted small mb-0">Overview of resort bookings, revenue, and content status.</p>
    </div>
    <div>
        <a href="{{ route('admin.bookings.index') }}" class="btn btn-teal px-3 py-2 shadow-sm"><i class="bi bi-calendar-range me-2"></i>Manage Bookings</a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-5">
    @foreach($stats as $label => $value)
        @php
            $icon = match($label) {
                'Rooms' => 'bi-door-open-fill',
                'Bookings' => 'bi-calendar-check-fill',
                'Revenue' => 'bi-currency-rupee',
                'Guests' => 'bi-people-fill',
                'Messages' => 'bi-envelope-paper-fill',
                'Pending Reviews' => 'bi-chat-left-heart-fill',
                default => 'bi-circle-fill'
            };
            $colorClass = match($label) {
                'Rooms' => 'card-teal',
                'Bookings' => 'card-blue',
                'Revenue' => 'card-gold',
                'Guests' => 'card-purple',
                'Messages' => 'card-orange',
                'Pending Reviews' => 'card-pink',
                default => 'card-secondary'
            };
        @endphp
        <div class="col-md-6 col-lg-4 col-xl-2">
            <div class="stat-card {{ $colorClass }} p-3 h-100 border-0 rounded-3 shadow-sm d-flex flex-column justify-content-between position-relative overflow-hidden">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="stat-label text-uppercase tracking-wider extra-small opacity-75">{{ $label }}</span>
                    <span class="stat-icon-wrapper rounded-circle"><i class="bi {{ $icon }}"></i></span>
                </div>
                <div>
                    <h3 class="stat-value fw-bold mb-0 leading-none">{{ $value }}</h3>
                </div>
                <div class="stat-decorator"></div>
            </div>
        </div>
    @endforeach
</div>

<!-- Recent Bookings Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white border-light py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-ink font-serif"><i class="bi bi-clock-history text-teal me-2"></i>Recent Booking Activities</h5>
        <span class="badge bg-teal-soft text-teal font-sans">Latest 8 entries</span>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0 table-hover table-striped-columns">
            <thead class="bg-light text-muted small uppercase">
                <tr>
                    <th class="ps-4">Booking Number</th>
                    <th>Guest Details</th>
                    <th>Room Selected</th>
                    <th>Dates / Duration</th>
                    <th>Status</th>
                    <th class="pe-4 text-end">Total Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentBookings as $booking)
                    @php
                        $badgeClass = match($booking->status) {
                            'pending' => 'bg-warning text-dark',
                            'confirmed' => 'bg-success text-white',
                            'checked_in' => 'bg-info text-dark',
                            'checked_out' => 'bg-secondary text-white',
                            'cancelled' => 'bg-danger text-white',
                            default => 'bg-light text-dark'
                        };
                    @endphp
                    <tr>
                        <td class="ps-4 fw-bold text-ink">
                            {{ $booking->booking_number }}
                            <small class="d-block text-muted font-sans extra-small">Created {{ $booking->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="icon-avatar-small bg-teal-soft text-teal"><i class="bi bi-person"></i></span>
                                <div>
                                    <span class="fw-bold d-block text-ink small">{{ $booking->guest_name }}</span>
                                    <small class="text-muted extra-small">{{ $booking->guest_email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="small fw-semibold text-ink">{{ $booking->room?->name ?: 'N/A' }}</span>
                            <small class="d-block text-muted extra-small">{{ $booking->room?->type?->name }}</small>
                        </td>
                        <td>
                            <div class="small fw-semibold text-ink">
                                {{ $booking->check_in->format('d M') }} - {{ $booking->check_out->format('d M Y') }}
                            </div>
                            <small class="text-muted extra-small">
                                {{ $booking->check_in->diffInDays($booking->check_out) }} nights
                            </small>
                        </td>
                        <td>
                            <span class="badge {{ $badgeClass }} font-sans px-2.5 py-1.5 uppercase tracking-wider" style="font-size: 0.68rem; font-weight: 700;">
                                {{ str($booking->status)->headline() }}
                            </span>
                        </td>
                        <td class="pe-4 text-end fw-bold text-teal">
                            ₹{{ number_format($booking->grand_total, 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                            No bookings recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
