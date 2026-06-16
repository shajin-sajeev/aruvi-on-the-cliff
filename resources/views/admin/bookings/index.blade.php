@extends('layouts.admin')

@section('title', 'Booking Management')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h1 class="fw-bold text-ink mb-1">Booking Management</h1>
        <p class="text-muted small mb-0">Monitor cottage availability, adjust reservation dates, and print customer bills.</p>
    </div>
    <div>
        <a class="btn btn-teal px-4 py-2.5 shadow-sm fw-bold" href="{{ route('admin.bookings.create') }}">
            <i class="bi bi-calendar-plus me-2"></i>Add New Booking
        </a>
    </div>
</div>

<div class="table-card table-responsive">
    <table class="table align-middle mb-0 table-hover">
        <thead>
            <tr>
                <th class="ps-4">Booking ID</th>
                <th>Guest Details</th>
                <th>Cottage</th>
                <th>Dates & Nights</th>
                <th>Status</th>
                <th>Amount</th>
                <th class="pe-4 text-end" style="width: 180px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
                @php
                    $nights = max(1, $booking->check_in->diffInDays($booking->check_out));
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
                        <small class="d-block text-muted font-sans extra-small fw-normal">Created {{ $booking->created_at->diffForHumans() }}</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="admin-thumb-avatar me-3 d-flex align-items-center justify-content-center bg-teal-soft text-teal fw-bold font-serif" style="font-size: 1.15rem;">
                                {{ strtoupper(substr($booking->guest_name, 0, 1)) }}
                            </div>
                            <div>
                                <span class="fw-bold d-block text-ink">{{ $booking->guest_name }}</span>
                                <small class="text-muted extra-small">{{ $booking->guest_email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="small fw-semibold text-ink">{{ $booking->room?->name ?: 'Unassigned Cottage' }}</span>
                        <small class="d-block text-muted extra-small">{{ $booking->room?->room_number }}</small>
                    </td>
                    <td>
                        <div class="small fw-semibold text-ink">
                            {{ $booking->check_in->format('d M') }} - {{ $booking->check_out->format('d M Y') }}
                        </div>
                        <small class="text-teal extra-small fw-semibold">
                            {{ $nights }} {{ Str::plural('night', $nights) }}
                        </small>
                    </td>
                    <td>
                        <span class="badge {{ $badgeClass }} font-sans px-2.5 py-1.5 uppercase tracking-wider" style="font-size: 0.68rem; font-weight: 700;">
                            {{ str($booking->status)->headline() }}
                        </span>
                    </td>
                    <td class="fw-bold text-teal">
                        ₹{{ number_format($booking->grand_total, 2) }}
                    </td>
                    <td class="pe-4 text-end">
                        <div class="d-inline-flex gap-2">
                            <a class="btn btn-action-circle btn-edit" href="{{ route('booking.invoice', $booking->booking_number) }}" target="_blank" title="View Receipt/Invoice">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                            </a>
                            <a class="btn btn-action-circle btn-edit" href="{{ route('admin.bookings.edit', $booking) }}" title="Edit Booking Details">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button class="btn btn-action-circle btn-delete" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteConfirmModal" 
                                    data-action="{{ route('admin.bookings.destroy', $booking) }}" 
                                    title="Delete Booking Record">
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

<div class="mt-4">
    {{ $bookings->links() }}
</div>
@endsection
