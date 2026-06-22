@extends('layouts.admin')
@section('title', $booking ? 'Edit Booking' : 'New Booking')
@section('content')

<div class="mb-3">
    <a href="{{ route('admin.bookings.index') }}" class="btn btn-sm btn-link text-teal text-decoration-none fw-semibold p-0">
        <i class="bi bi-arrow-left me-1"></i> Back to Bookings
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
    <div class="form-card-header d-flex align-items-start align-items-sm-center justify-content-between flex-wrap gap-2">
        <div>
            <h5 class="fw-bold font-serif mb-1 text-white">
                <i class="bi {{ $booking ? 'bi-pencil-square' : 'bi-calendar-plus-fill' }} me-2 opacity-75"></i>
                {{ $booking ? 'Modify Booking Request' : 'Add New Reservation' }}
            </h5>
            <small class="text-white opacity-70">Invoices and payments recalculate automatically on save.</small>
        </div>
        <span class="badge px-3 py-2 text-uppercase tracking-wider extra-small fw-bold">
            Booking CRUD
        </span>
    </div>

    <form method="post"
          action="{{ $booking ? route('admin.bookings.update',$booking) : route('admin.bookings.store') }}"
          class="p-3 p-sm-4 p-md-5 bg-white">
        @csrf
        @if($booking) @method('patch') @endif

        <div class="row g-3 g-md-4">
            {{-- Cottage --}}
            <div class="col-12 col-sm-6">
                <label class="form-label fw-bold small text-ink mb-2">
                    <i class="bi bi-door-open text-teal me-1"></i>Select Cottage
                </label>
                <select class="form-select" name="room_id" required>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" @selected(old('room_id',$booking?->room_id) === $room->id)>
                            {{ $room->name }} ({{ $room->room_number }}) — &#8377;{{ number_format($room->price_per_night) }}/night
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Status --}}
            <div class="col-12 col-sm-6">
                <label class="form-label fw-bold small text-ink mb-2">
                    <i class="bi bi-shield-check text-teal me-1"></i>Reservation Status
                </label>
                <select class="form-select" name="status" required>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" @selected(old('status',$booking?->status) === $status)>
                            {{ str($status)->headline() }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Check-in / Check-out --}}
            <div class="col-12 col-sm-6">
                <label class="form-label fw-bold small text-ink mb-2">
                    <i class="bi bi-calendar-check text-teal me-1"></i>Check-in Date
                </label>
                <input class="form-control" type="date" name="check_in"
                       value="{{ old('check_in',$booking?->check_in?->format('Y-m-d')) }}" required>
            </div>
            <div class="col-12 col-sm-6">
                <label class="form-label fw-bold small text-ink mb-2">
                    <i class="bi bi-calendar-x text-teal me-1"></i>Check-out Date
                </label>
                <input class="form-control" type="date" name="check_out"
                       value="{{ old('check_out',$booking?->check_out?->format('Y-m-d')) }}" required>
            </div>

            {{-- Adults / Children --}}
            <div class="col-6 col-sm-3">
                <label class="form-label fw-bold small text-ink mb-2">
                    <i class="bi bi-people text-teal me-1"></i>Adults
                </label>
                <input class="form-control" type="number" min="1" name="adults"
                       value="{{ old('adults',$booking?->adults ?? 2) }}" required>
            </div>
            <div class="col-6 col-sm-3">
                <label class="form-label fw-bold small text-ink mb-2">
                    <i class="bi bi-person-plus text-teal me-1"></i>Children
                </label>
                <input class="form-control" type="number" min="0" name="children"
                       value="{{ old('children',$booking?->children ?? 0) }}">
            </div>

            {{-- Guest Name / Email / Phone --}}
            <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label fw-bold small text-ink mb-2">
                    <i class="bi bi-person-badge text-teal me-1"></i>Guest Name
                </label>
                <input class="form-control" name="guest_name"
                       value="{{ old('guest_name',$booking?->guest_name) }}"
                       placeholder="e.g. John Doe" required>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label fw-bold small text-ink mb-2">
                    <i class="bi bi-envelope text-teal me-1"></i>Guest Email
                </label>
                <input class="form-control" type="email" name="guest_email"
                       value="{{ old('guest_email',$booking?->guest_email) }}"
                       placeholder="e.g. john@example.com" required>
            </div>
            <div class="col-12 col-sm-6 col-md-4">
                <label class="form-label fw-bold small text-ink mb-2">
                    <i class="bi bi-telephone text-teal me-1"></i>Guest Phone
                </label>
                <input class="form-control" name="guest_phone"
                       value="{{ old('guest_phone',$booking?->guest_phone) }}"
                       placeholder="e.g. +91 98765 43210" required>
            </div>

            {{-- Special Requests --}}
            <div class="col-12">
                <label class="form-label fw-bold small text-ink mb-2">
                    <i class="bi bi-chat-dots text-teal me-1"></i>Special Requests
                </label>
                <textarea class="form-control" name="special_requests" rows="3"
                          placeholder="Dietary preferences, bed configuration, transport, late check-in…">{{ old('special_requests',$booking?->special_requests) }}</textarea>
            </div>
        </div>

        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end mt-4 pt-4 border-top">
            <a href="{{ route('admin.bookings.index') }}" class="btn btn-outline-secondary px-4 py-2 fw-semibold">
                Cancel
            </a>
            <button class="btn btn-teal px-4 py-2 shadow-sm fw-bold">
                <i class="bi bi-check-circle-fill me-2"></i>Save &amp; Recalculate
            </button>
        </div>
    </form>
</div>
@endsection
