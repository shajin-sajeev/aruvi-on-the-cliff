@extends('layouts.admin')
@section('title', 'Dashboard Analytics')
@section('content')

{{-- ── Page Header ──────────────────────────────────────────── --}}
<div class="admin-page-header">
    <div>
        <h1>Dashboard Analytics</h1>
        <p class="text-muted small mb-0">Overview of resort bookings, revenue, and content status.</p>
    </div>
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-teal fw-semibold shadow-sm">
        <i class="bi bi-calendar-range me-2"></i>Manage Bookings
    </a>
</div>

{{-- ── Stats — Desktop (row of 6) ──────────────────────────── --}}
<div class="row g-3 mb-4 dash-stats-desktop">
    @foreach($stats as $label => $value)
        @php
            $icon = match($label) {
                'Rooms'           => 'bi-door-open-fill',
                'Bookings'        => 'bi-calendar-check-fill',
                'Revenue'         => 'bi-currency-rupee',
                'Guests'          => 'bi-people-fill',
                'Messages'        => 'bi-envelope-paper-fill',
                'Pending Reviews' => 'bi-chat-left-heart-fill',
                default           => 'bi-circle-fill'
            };
            $colorClass = match($label) {
                'Rooms'           => 'card-teal',
                'Bookings'        => 'card-blue',
                'Revenue'         => 'card-gold',
                'Guests'          => 'card-purple',
                'Messages'        => 'card-orange',
                'Pending Reviews' => 'card-pink',
                default           => 'card-secondary'
            };
        @endphp
        <div class="col-6 col-sm-4 col-md-4 col-lg-2">
            <div class="stat-card {{ $colorClass }} p-3 h-100 border-0 rounded-3 shadow-sm d-flex flex-column justify-content-between position-relative overflow-hidden">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="stat-label text-uppercase tracking-wider extra-small opacity-75">{{ $label }}</span>
                    <span class="stat-icon-wrapper rounded-circle"><i class="bi {{ $icon }}"></i></span>
                </div>
                <h3 class="stat-value fw-bold mb-0">{{ $value }}</h3>
                <div class="stat-decorator"></div>
            </div>
        </div>
    @endforeach
</div>

{{-- ── Stats — Mobile (2-col grid cards) ───────────────────── --}}
<div class="stat-grid-mobile mb-4 dash-stats-mobile">
    @foreach($stats as $label => $value)
        @php
            $icon = match($label) {
                'Rooms'           => 'bi-door-open-fill',
                'Bookings'        => 'bi-calendar-check-fill',
                'Revenue'         => 'bi-currency-rupee',
                'Guests'          => 'bi-people-fill',
                'Messages'        => 'bi-envelope-paper-fill',
                'Pending Reviews' => 'bi-chat-left-heart-fill',
                default           => 'bi-circle-fill'
            };
            $bg = match($label) {
                'Rooms'           => 'linear-gradient(135deg,#073b3f,var(--teal))',
                'Bookings'        => 'linear-gradient(135deg,#094a50,#157d87)',
                'Revenue'         => 'linear-gradient(135deg,#6c4b18,#b38612)',
                'Guests'          => 'linear-gradient(135deg,#124046,#2fbf9f)',
                'Messages'        => 'linear-gradient(135deg,#6b2e1e,#d45c43)',
                'Pending Reviews' => 'linear-gradient(135deg,#0b2224,#1c5257)',
                default           => 'linear-gradient(135deg,#333,#555)'
            };
        @endphp
        <div class="stat-card-mobile" style="background: {{ $bg }};">
            <i class="bi {{ $icon }} sm-icon"></i>
            <div class="sm-label">{{ $label }}</div>
            <div class="sm-value">{{ $value }}</div>
        </div>
    @endforeach
</div>

{{-- ── Recent Bookings ──────────────────────────────────────── --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
    <div class="card-header bg-white border-light py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <h5 class="mb-0 fw-bold text-ink font-serif">
            <i class="bi bi-clock-history text-teal me-2"></i>Recent Bookings
        </h5>
        <span class="badge bg-teal-soft text-teal font-sans">Latest 8 entries</span>
    </div>

    {{-- Desktop table --}}
    <div class="table-responsive bookings-desktop-table">
        <table class="table align-middle mb-0 table-hover">
            <thead class="bg-light text-muted small">
                <tr>
                    <th class="ps-4">Booking #</th>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Dates</th>
                    <th>Status</th>
                    <th class="pe-4 text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentBookings as $booking)
                    @php
                        $badgeClass = match($booking->status) {
                            'pending'      => 'bg-warning text-dark',
                            'confirmed'    => 'bg-success text-white',
                            'checked_in'   => 'bg-info text-dark',
                            'checked_out'  => 'bg-secondary text-white',
                            'cancelled'    => 'bg-danger text-white',
                            default        => 'bg-light text-dark'
                        };
                    @endphp
                    <tr>
                        <td class="ps-4 fw-bold text-ink">
                            {{ $booking->booking_number }}
                            <small class="d-block text-muted extra-small fw-normal">{{ $booking->created_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="icon-avatar-small bg-teal-soft text-teal"><i class="bi bi-person"></i></span>
                                <div>
                                    <span class="fw-semibold d-block text-ink small">{{ $booking->guest_name }}</span>
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
                                {{ $booking->check_in->format('d M') }} – {{ $booking->check_out->format('d M Y') }}
                            </div>
                            <small class="text-muted extra-small">{{ $booking->check_in->diffInDays($booking->check_out) }} nights</small>
                        </td>
                        <td>
                            <span class="badge {{ $badgeClass }} px-2 py-1" style="font-size:0.68rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em;">
                                {{ str($booking->status)->headline() }}
                            </span>
                        </td>
                        <td class="pe-4 text-end fw-bold text-teal">
                            &#8377;{{ number_format($booking->grand_total, 2) }}
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

    {{-- Mobile card list --}}
    <div class="bookings-mobile-list p-3">
        @forelse($recentBookings as $booking)
            @php
                $badgeClass = match($booking->status) {
                    'pending'      => 'bg-warning text-dark',
                    'confirmed'    => 'bg-success text-white',
                    'checked_in'   => 'bg-info text-dark',
                    'checked_out'  => 'bg-secondary text-white',
                    'cancelled'    => 'bg-danger text-white',
                    default        => 'bg-light text-dark'
                };
            @endphp
            <div class="booking-mobile-card">
                <div class="bmc-row">
                    <div>
                        <div class="fw-bold text-ink small">{{ $booking->booking_number }}</div>
                        <div class="extra-small text-muted">{{ $booking->created_at->diffForHumans() }}</div>
                    </div>
                    <span class="badge {{ $badgeClass }} px-2 py-1" style="font-size:0.68rem; font-weight:700; text-transform:uppercase; white-space:nowrap;">
                        {{ str($booking->status)->headline() }}
                    </span>
                </div>
                <div class="bmc-row">
                    <div class="d-flex align-items-center gap-2">
                        <span class="icon-avatar-small bg-teal-soft text-teal" style="width:32px;height:32px;font-size:0.9rem;"><i class="bi bi-person"></i></span>
                        <div>
                            <div class="fw-semibold text-ink small">{{ $booking->guest_name }}</div>
                            <div class="extra-small text-muted">{{ $booking->guest_email }}</div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-teal small">&#8377;{{ number_format($booking->grand_total, 2) }}</div>
                        <div class="extra-small text-muted">{{ $booking->check_in->diffInDays($booking->check_out) }} nights</div>
                    </div>
                </div>
                <div class="extra-small text-muted mt-1">
                    <i class="bi bi-door-open text-teal me-1"></i>{{ $booking->room?->name ?: 'N/A' }}
                    &nbsp;&bull;&nbsp;
                    <i class="bi bi-calendar3 text-teal me-1"></i>{{ $booking->check_in->format('d M') }} – {{ $booking->check_out->format('d M Y') }}
                </div>
            </div>
        @empty
            <div class="text-center py-4 text-muted">
                <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary"></i>
                No bookings recorded yet.
            </div>
        @endforelse
    </div>
</div>

@endsection
