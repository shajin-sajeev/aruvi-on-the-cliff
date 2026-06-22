@extends('layouts.admin')
@section('title', 'Booking Management')
@section('content')

<div class="admin-page-header">
    <div>
        <h1>Booking Management</h1>
        <p class="text-muted small mb-0">Monitor cottage availability, adjust reservations, and print bills.</p>
    </div>
    <a class="btn btn-teal fw-semibold shadow-sm" href="{{ route('admin.bookings.create') }}">
        <i class="bi bi-calendar-plus me-2"></i>Add Booking
    </a>
</div>

{{-- ── Desktop Table ─────────────────────────────────────────── --}}
<div class="listing-desktop-table">
    <div class="table-card table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead>
                <tr>
                    <th class="ps-4">Booking ID</th>
                    <th>Guest</th>
                    <th>Cottage</th>
                    <th>Dates &amp; Nights</th>
                    <th>Status</th>
                    <th>Amount</th>
                    <th class="pe-4 text-end" style="width:170px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    @php
                        $nights = max(1, $booking->check_in->diffInDays($booking->check_out));
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
                            <div class="d-flex align-items-center">
                                <div class="admin-thumb-avatar me-3 d-flex align-items-center justify-content-center bg-teal-soft text-teal fw-bold font-serif" style="font-size:1.1rem;">
                                    {{ strtoupper(substr($booking->guest_name,0,1)) }}
                                </div>
                                <div>
                                    <span class="fw-bold d-block text-ink">{{ $booking->guest_name }}</span>
                                    <small class="text-muted extra-small">{{ $booking->guest_email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="small fw-semibold text-ink">{{ $booking->room?->name ?: 'Unassigned' }}</span>
                            <small class="d-block text-muted extra-small">{{ $booking->room?->room_number }}</small>
                        </td>
                        <td>
                            <div class="small fw-semibold text-ink">{{ $booking->check_in->format('d M') }} – {{ $booking->check_out->format('d M Y') }}</div>
                            <small class="text-teal extra-small fw-semibold">{{ $nights }} {{ Str::plural('night',$nights) }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $badgeClass }}" style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;padding:4px 10px;">
                                {{ str($booking->status)->headline() }}
                            </span>
                        </td>
                        <td class="fw-bold text-teal">&#8377;{{ number_format($booking->grand_total,2) }}</td>
                        <td class="pe-4 text-end">
                            <div class="d-inline-flex gap-2">
                                <a class="btn btn-action-circle btn-edit" href="{{ route('booking.invoice',$booking->booking_number) }}" target="_blank" title="View Invoice">
                                    <i class="bi bi-file-earmark-pdf-fill"></i>
                                </a>
                                <a class="btn btn-action-circle btn-edit" href="{{ route('admin.bookings.edit',$booking) }}" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button class="btn btn-action-circle btn-delete"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteConfirmModal"
                                        data-action="{{ route('admin.bookings.destroy',$booking) }}"
                                        title="Delete">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
                            No booking activities recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Mobile Card List ──────────────────────────────────────── --}}
<div class="listing-mobile-cards">
    @forelse($bookings as $booking)
        @php
            $nights = max(1, $booking->check_in->diffInDays($booking->check_out));
            $badgeClass = match($booking->status) {
                'pending'      => 'bg-warning text-dark',
                'confirmed'    => 'bg-success text-white',
                'checked_in'   => 'bg-info text-dark',
                'checked_out'  => 'bg-secondary text-white',
                'cancelled'    => 'bg-danger text-white',
                default        => 'bg-light text-dark'
            };
        @endphp
        <div class="admin-record-card">
            <div class="admin-record-card-header">
                <div class="admin-thumb-avatar d-flex align-items-center justify-content-center bg-teal-soft text-teal fw-bold font-serif" style="font-size:1rem;flex-shrink:0;">
                    {{ strtoupper(substr($booking->guest_name,0,1)) }}
                </div>
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-bold text-ink" style="font-size:0.9rem;">{{ $booking->guest_name }}</div>
                    <div class="extra-small text-muted text-truncate">{{ $booking->booking_number }}</div>
                </div>
                <span class="badge {{ $badgeClass }} flex-shrink-0" style="font-size:0.65rem;font-weight:700;text-transform:uppercase;padding:4px 8px;white-space:nowrap;">
                    {{ str($booking->status)->headline() }}
                </span>
            </div>
            <div class="admin-record-card-meta">
                <div class="admin-record-card-meta-item">
                    <i class="bi bi-door-open"></i>
                    <span>{{ $booking->room?->name ?: 'Unassigned' }}</span>
                </div>
                <div class="admin-record-card-meta-item">
                    <i class="bi bi-calendar3"></i>
                    <span>{{ $booking->check_in->format('d M') }} – {{ $booking->check_out->format('d M Y') }} ({{ $nights }}n)</span>
                </div>
                <div class="admin-record-card-meta-item">
                    <i class="bi bi-envelope"></i>
                    <span class="text-truncate" style="max-width:160px;">{{ $booking->guest_email }}</span>
                </div>
                <div class="admin-record-card-meta-item">
                    <i class="bi bi-currency-rupee"></i>
                    <span class="fw-bold text-teal">{{ number_format($booking->grand_total,2) }}</span>
                </div>
            </div>
            <div class="admin-record-card-actions">
                <a class="btn btn-sm btn-light border" href="{{ route('booking.invoice',$booking->booking_number) }}" target="_blank">
                    <i class="bi bi-file-earmark-pdf-fill text-teal me-1"></i>Invoice
                </a>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.bookings.edit',$booking) }}">
                    <i class="bi bi-pencil-square me-1"></i>Edit
                </a>
                <button class="btn btn-sm btn-outline-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-action="{{ route('admin.bookings.destroy',$booking) }}">
                    <i class="bi bi-trash3-fill me-1"></i>Delete
                </button>
            </div>
        </div>
    @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>
            No booking activities recorded yet.
        </div>
    @endforelse
</div>

<div class="mt-4">{{ $bookings->links() }}</div>
@endsection
