@extends('layouts.frontend')
@section('title', 'Online Room Booking - Aruvi on the Cliff')

@push('styles')
<style>
/* ── Inline field error ─────────────────────────────── */
.field-error {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: .8rem;
    color: #dc3545;
    margin-top: 5px;
    font-weight: 500;
    animation: fadeInDown .18s ease;
}
.field-error i { font-size: .85rem; flex-shrink: 0; }

/* ── Input error state ──────────────────────────────── */
.form-control.input-error {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 3px rgba(220,53,69,.12) !important;
}

/* ── Input valid state (after correction) ───────────── */
.form-control.input-ok {
    border-color: #198754 !important;
    box-shadow: 0 0 0 3px rgba(25,135,84,.1) !important;
}

/* ── Stay summary pill ───────────────────────────────── */
.stay-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg,#e8f5f5,#d0eded);
    border: 1px solid #b2dcdc;
    border-radius: 30px;
    padding: 8px 18px;
    font-size: .87rem;
    color: #056363;
    font-weight: 600;
    margin-top: 4px;
}
.stay-pill i { font-size: 1rem; }

@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-4px); }
    to   { opacity: 1; transform: translateY(0); }
}
</style>
@endpush

@section('content')
<section class="section section-soft" style="padding-top:100px; padding-bottom:60px;">
    <div class="container" style="max-width:780px;">

        <div class="text-center mb-4">
            <span class="badge bg-teal-soft text-teal px-3 py-2 mb-2">
                <i class="bi bi-calendar2-heart me-1"></i> ONLINE RESERVATION
            </span>
            <h1 class="display-5 fw-bold font-serif text-ink">Reserve Your Cliffside Stay</h1>
            <p class="text-muted">Fill in your details below. You'll be taken to a secure payment screen to confirm your booking.</p>
        </div>

        <form method="post" action="{{ route('booking.store') }}"
              class="lux-card p-4 p-md-5 mt-2" id="bookingForm" novalidate>
            @csrf

            {{-- ═══════════════════════════════════════════════
                 STAY DATES
            ═══════════════════════════════════════════════ --}}
            <h6 class="fw-bold text-teal text-uppercase tracking-wider small mb-3">
                <i class="bi bi-calendar3 me-2"></i>Stay Dates
            </h6>
            <div class="row g-3 mb-2">

                {{-- Check-in --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-ink" for="check_in">
                        <i class="bi bi-calendar-check me-1"></i>Check-in Date <span class="text-danger">*</span>
                    </label>
                    <input class="form-control @error('check_in') input-error @enderror"
                           type="date" name="check_in" id="check_in"
                           value="{{ old('check_in') }}" autocomplete="off">
                    {{-- server error --}}
                    @error('check_in')
                        <span class="field-error" id="err-check_in">
                            <i class="bi bi-exclamation-circle-fill"></i>{{ $message }}
                        </span>
                    @else
                        <span class="field-error d-none" id="err-check_in">
                            <i class="bi bi-exclamation-circle-fill"></i><span></span>
                        </span>
                    @enderror
                    <small class="text-muted extra-small d-block mt-1">Earliest check-in: today.</small>
                </div>

                {{-- Check-out --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-ink" for="check_out">
                        <i class="bi bi-calendar-x me-1"></i>Check-out Date <span class="text-danger">*</span>
                    </label>
                    <input class="form-control @error('check_out') input-error @enderror"
                           type="date" name="check_out" id="check_out"
                           value="{{ old('check_out') }}" autocomplete="off">
                    @error('check_out')
                        <span class="field-error" id="err-check_out">
                            <i class="bi bi-exclamation-circle-fill"></i>{{ $message }}
                        </span>
                    @else
                        <span class="field-error d-none" id="err-check_out">
                            <i class="bi bi-exclamation-circle-fill"></i><span></span>
                        </span>
                    @enderror
                    <small class="text-muted extra-small d-block mt-1">Must be after check-in date.</small>
                </div>

            </div>

            {{-- Stay duration pill --}}
            <div id="stay-summary" class="d-none mb-4">
                <div class="stay-pill">
                    <i class="bi bi-moon-stars-fill"></i>
                    <span id="stay-summary-text"></span>
                </div>
            </div>
            <div class="mb-4"></div>

            <hr class="my-4">

            {{-- ═══════════════════════════════════════════════
                 GUESTS
            ═══════════════════════════════════════════════ --}}
            <h6 class="fw-bold text-teal text-uppercase tracking-wider small mb-3">
                <i class="bi bi-people me-2"></i>Guests
            </h6>
            <div class="row g-3 mb-4">

                {{-- Adults --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-ink" for="adults">
                        <i class="bi bi-person me-1"></i>Adults <span class="text-danger">*</span>
                    </label>
                    <input class="form-control @error('adults') input-error @enderror"
                           type="number" min="1" max="10" name="adults" id="adults"
                           value="{{ old('adults', 2) }}">
                    @error('adults')
                        <span class="field-error" id="err-adults">
                            <i class="bi bi-exclamation-circle-fill"></i>{{ $message }}
                        </span>
                    @else
                        <span class="field-error d-none" id="err-adults">
                            <i class="bi bi-exclamation-circle-fill"></i><span></span>
                        </span>
                    @enderror
                    <small class="text-muted extra-small d-block mt-1">Minimum 1 adult required.</small>
                </div>

                {{-- Children --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold small text-ink" for="children">
                        <i class="bi bi-person-plus me-1"></i>Children
                    </label>
                    <input class="form-control @error('children') input-error @enderror"
                           type="number" min="0" max="10" name="children" id="children"
                           value="{{ old('children', 0) }}">
                    @error('children')
                        <span class="field-error" id="err-children">
                            <i class="bi bi-exclamation-circle-fill"></i>{{ $message }}
                        </span>
                    @else
                        <span class="field-error d-none" id="err-children">
                            <i class="bi bi-exclamation-circle-fill"></i><span></span>
                        </span>
                    @enderror
                    <small class="text-muted extra-small d-block mt-1">Ages 12 and below.</small>
                </div>

            </div>

            <hr class="my-4">

            {{-- ═══════════════════════════════════════════════
                 GUEST DETAILS
            ═══════════════════════════════════════════════ --}}
            <h6 class="fw-bold text-teal text-uppercase tracking-wider small mb-3">
                <i class="bi bi-person-badge me-2"></i>Guest Details
            </h6>
            <div class="row g-3 mb-4">

                {{-- Full Name --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-ink" for="guest_name">
                        <i class="bi bi-person-badge me-1"></i>Full Name <span class="text-danger">*</span>
                    </label>
                    <input class="form-control @error('guest_name') input-error @enderror"
                           name="guest_name" id="guest_name"
                           value="{{ old('guest_name', auth()->user()?->name) }}"
                           placeholder="e.g. John Doe">
                    @error('guest_name')
                        <span class="field-error" id="err-guest_name">
                            <i class="bi bi-exclamation-circle-fill"></i>{{ $message }}
                        </span>
                    @else
                        <span class="field-error d-none" id="err-guest_name">
                            <i class="bi bi-exclamation-circle-fill"></i><span></span>
                        </span>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-ink" for="guest_email">
                        <i class="bi bi-envelope me-1"></i>Email Address <span class="text-danger">*</span>
                    </label>
                    <input class="form-control @error('guest_email') input-error @enderror"
                           type="email" name="guest_email" id="guest_email"
                           value="{{ old('guest_email', auth()->user()?->email) }}"
                           placeholder="e.g. john@example.com">
                    @error('guest_email')
                        <span class="field-error" id="err-guest_email">
                            <i class="bi bi-exclamation-circle-fill"></i>{{ $message }}
                        </span>
                    @else
                        <span class="field-error d-none" id="err-guest_email">
                            <i class="bi bi-exclamation-circle-fill"></i><span></span>
                        </span>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="col-md-4">
                    <label class="form-label fw-semibold small text-ink" for="guest_phone">
                        <i class="bi bi-telephone me-1"></i>Phone Number <span class="text-danger">*</span>
                    </label>
                    <input class="form-control @error('guest_phone') input-error @enderror"
                           name="guest_phone" id="guest_phone"
                           value="{{ old('guest_phone', auth()->user()?->phone ?? '') }}"
                           placeholder="e.g. +91 98765 43210">
                    @error('guest_phone')
                        <span class="field-error" id="err-guest_phone">
                            <i class="bi bi-exclamation-circle-fill"></i>{{ $message }}
                        </span>
                    @else
                        <span class="field-error d-none" id="err-guest_phone">
                            <i class="bi bi-exclamation-circle-fill"></i><span></span>
                        </span>
                    @enderror
                </div>

            </div>

            {{-- Special Requests --}}
            <div class="mb-4">
                <label class="form-label fw-semibold small text-ink" for="special_requests">
                    <i class="bi bi-chat-dots me-1"></i>Special Requests
                </label>
                <textarea class="form-control @error('special_requests') input-error @enderror"
                          name="special_requests" id="special_requests" rows="3"
                          placeholder="Any specific requirements (e.g. dietary constraints, bed preference, late check-in)...">{{ old('special_requests') }}</textarea>
                @error('special_requests')
                    <span class="field-error">
                        <i class="bi bi-exclamation-circle-fill"></i>{{ $message }}
                    </span>
                @enderror
            </div>

            <hr class="my-4">

            {{-- Submit --}}
            <div class="d-flex flex-column flex-sm-row align-items-center justify-content-between gap-3">
                <p class="text-muted small mb-0">
                    <i class="bi bi-shield-lock text-teal me-1"></i>
                    Your information is encrypted and secure.
                </p>
                <button type="submit" class="btn btn-teal btn-lg px-5 py-2 shadow-sm fw-bold" id="submitBtn">
                    <i class="bi bi-credit-card me-2"></i>Confirm &amp; Pay
                </button>
            </div>

        </form>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Helpers ──────────────────────────────────────────────────────────
    function showError(fieldId, message) {
        const input = document.getElementById(fieldId);
        const err   = document.getElementById('err-' + fieldId);
        if (!input || !err) return;

        input.classList.add('input-error');
        input.classList.remove('input-ok');

        const textNode = err.querySelector('span') || err;
        if (err.querySelector('span')) {
            err.querySelector('span').textContent = message;
        } else {
            err.childNodes[err.childNodes.length - 1].textContent = message;
        }
        err.classList.remove('d-none');
    }

    function clearError(fieldId) {
        const input = document.getElementById(fieldId);
        const err   = document.getElementById('err-' + fieldId);
        if (!input || !err) return;

        input.classList.remove('input-error');
        err.classList.add('d-none');
    }

    function markOk(fieldId) {
        const input = document.getElementById(fieldId);
        if (!input) return;
        clearError(fieldId);
        input.classList.add('input-ok');
    }

    // ── Date setup ───────────────────────────────────────────────────────
    const checkIn  = document.getElementById('check_in');
    const checkOut = document.getElementById('check_out');
    const staySummary     = document.getElementById('stay-summary');
    const staySummaryText = document.getElementById('stay-summary-text');

    const todayStr = new Date().toISOString().split('T')[0];
    checkIn.setAttribute('min', todayStr);

    function updateSummary() {
        if (!checkIn.value || !checkOut.value) { staySummary.classList.add('d-none'); return; }
        const ci = new Date(checkIn.value);
        const co = new Date(checkOut.value);
        if (co <= ci) { staySummary.classList.add('d-none'); return; }
        const nights  = Math.round((co - ci) / 86400000);
        const options = { day: 'numeric', month: 'short', year: 'numeric' };
        staySummaryText.textContent =
            `${nights} night${nights > 1 ? 's' : ''} · ` +
            `${ci.toLocaleDateString('en-IN', options)} → ${co.toLocaleDateString('en-IN', options)}`;
        staySummary.classList.remove('d-none');
    }

    checkIn.addEventListener('change', function () {
        clearError('check_in');
        const ciVal = checkIn.value;
        if (ciVal) {
            const next = new Date(ciVal);
            next.setDate(next.getDate() + 1);
            const nextStr = next.toISOString().split('T')[0];
            checkOut.setAttribute('min', nextStr);
            if (checkOut.value && checkOut.value <= ciVal) {
                checkOut.value = nextStr;
                clearError('check_out');
            }
        }
        updateSummary();
    });

    checkOut.addEventListener('change', function () {
        clearError('check_out');
        updateSummary();
    });

    // Restore summary on back-navigation with old() values
    if (checkIn.value) checkIn.dispatchEvent(new Event('change'));

    // ── Live validation on blur ───────────────────────────────────────────
    const today = () => { const d = new Date(); d.setHours(0,0,0,0); return d; };

    checkIn.addEventListener('blur', function () {
        if (!this.value) { showError('check_in', 'Check-in date is required.'); return; }
        if (new Date(this.value) < today()) { showError('check_in', 'Check-in date cannot be in the past.'); return; }
        markOk('check_in');
    });

    checkOut.addEventListener('blur', function () {
        if (!this.value) { showError('check_out', 'Check-out date is required.'); return; }
        if (checkIn.value && new Date(this.value) <= new Date(checkIn.value)) {
            showError('check_out', 'Check-out must be after check-in date.'); return;
        }
        markOk('check_out');
    });

    document.getElementById('adults').addEventListener('blur', function () {
        if (!this.value || parseInt(this.value) < 1) { showError('adults', 'At least 1 adult is required.'); return; }
        markOk('adults');
    });

    document.getElementById('guest_name').addEventListener('blur', function () {
        if (!this.value.trim()) { showError('guest_name', 'Full name is required.'); return; }
        markOk('guest_name');
    });

    document.getElementById('guest_email').addEventListener('blur', function () {
        if (!this.value.trim()) { showError('guest_email', 'Email address is required.'); return; }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value.trim())) {
            showError('guest_email', 'Enter a valid email address.'); return;
        }
        markOk('guest_email');
    });

    document.getElementById('guest_phone').addEventListener('blur', function () {
        if (!this.value.trim()) { showError('guest_phone', 'Phone number is required.'); return; }
        if (!/^[+\d][\d\s\-().]{7,}$/.test(this.value.trim())) {
            showError('guest_phone', 'Enter a valid phone number.'); return;
        }
        markOk('guest_phone');
    });

    // ── Submit: validate all fields before sending ────────────────────────
    document.getElementById('bookingForm').addEventListener('submit', function (e) {
        let hasError = false;

        function check(fieldId, condition, message) {
            if (condition) { showError(fieldId, message); hasError = true; }
            else if (!document.getElementById('err-' + fieldId)?.classList.contains('d-none') === false) {
                // already has a server error — leave it
            }
        }

        const ci    = checkIn.value  ? new Date(checkIn.value)  : null;
        const co    = checkOut.value ? new Date(checkOut.value) : null;
        const now   = today();
        const name  = document.getElementById('guest_name').value.trim();
        const email = document.getElementById('guest_email').value.trim();
        const phone = document.getElementById('guest_phone').value.trim();
        const adultVal = parseInt(document.getElementById('adults').value);

        if (!checkIn.value)          { showError('check_in',  'Check-in date is required.');             hasError = true; }
        else if (ci < now)           { showError('check_in',  'Check-in date cannot be in the past.');   hasError = true; }
        else                           clearError('check_in');

        if (!checkOut.value)         { showError('check_out', 'Check-out date is required.');            hasError = true; }
        else if (ci && co <= ci)     { showError('check_out', 'Check-out must be after check-in date.'); hasError = true; }
        else                           clearError('check_out');

        if (!adultVal || adultVal < 1) { showError('adults', 'At least 1 adult is required.');           hasError = true; }
        else                             clearError('adults');

        if (!name)  { showError('guest_name',  'Full name is required.');                                hasError = true; }
        else          clearError('guest_name');

        if (!email) { showError('guest_email', 'Email address is required.');                            hasError = true; }
        else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                      showError('guest_email', 'Enter a valid email address.');                          hasError = true; }
        else          clearError('guest_email');

        if (!phone) { showError('guest_phone', 'Phone number is required.');                             hasError = true; }
        else if (!/^[+\d][\d\s\-().]{7,}$/.test(phone)) {
                      showError('guest_phone', 'Enter a valid phone number.');                           hasError = true; }
        else          clearError('guest_phone');

        if (hasError) {
            e.preventDefault();
            // Scroll to first visible error
            const firstErr = document.querySelector('.field-error:not(.d-none)');
            if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

});
</script>
@endsection
