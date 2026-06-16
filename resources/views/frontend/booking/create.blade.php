@extends('layouts.frontend')
@section('title', 'Online Room Booking - Aruvi on the Cliff')
@section('content')
<section class="section section-soft">
    <div class="container">
        <div class="eyebrow">Online Room Booking</div><h1 class="display-5 fw-bold">Reserve your cliffside stay</h1>
        <form method="post" action="{{ route('booking.store') }}" class="lux-card p-4 mt-4">
            @csrf
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-ink"><i class="bi bi-calendar-check text-teal me-1"></i>Check-in Date</label>
                    <input class="form-control" type="date" name="check_in" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-ink"><i class="bi bi-calendar-x text-teal me-1"></i>Check-out Date</label>
                    <input class="form-control" type="date" name="check_out" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-ink"><i class="bi bi-people text-teal me-1"></i>Adults</label>
                    <input class="form-control" type="number" min="1" name="adults" value="2" required>
                    <small class="text-muted extra-small">Minimum 1 guest required per cottage.</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-ink"><i class="bi bi-person-plus text-teal me-1"></i>Children</label>
                    <input class="form-control" type="number" min="0" name="children" value="0">
                    <small class="text-muted extra-small">Ages 12 and below.</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-ink"><i class="bi bi-person-badge text-teal me-1"></i>Guest Name</label>
                    <input class="form-control" name="guest_name" value="{{ auth()->user()?->name }}" placeholder="e.g. John Doe" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-ink"><i class="bi bi-envelope text-teal me-1"></i>Guest Email</label>
                    <input class="form-control" type="email" name="guest_email" value="{{ auth()->user()?->email }}" placeholder="e.g. john@example.com" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-ink"><i class="bi bi-telephone text-teal me-1"></i>Guest Phone</label>
                    <input class="form-control" name="guest_phone" value="{{ auth()->user()?->phone }}" placeholder="e.g. +91 98765 43210" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold small text-ink"><i class="bi bi-chat-dots text-teal me-1"></i>Special Requests</label>
                    <textarea class="form-control" name="special_requests" rows="4" placeholder="Any specific requirements (e.g. dietary constraints, double bed preference, check-in details)..."></textarea>
                </div>
            </div>
            <button class="btn btn-teal btn-lg mt-4 px-4 py-2.5 shadow-sm fw-bold">Confirm Booking Request <i class="bi bi-check2-circle ms-2"></i></button>
        </form>
    </div>
</section>
@endsection
