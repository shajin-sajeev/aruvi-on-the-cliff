@extends('layouts.frontend')
@section('title', 'Admin Login - Aruvi on the Cliff')
@section('content')
<section class="section section-soft" style="padding-top: 100px; padding-bottom: 60px;">
    <div class="container" style="max-width: 520px;">
        <div class="text-center mb-4">
            <span class="badge bg-teal-soft text-teal px-3 py-2 mb-2">
                <i class="bi bi-shield-lock me-1"></i> SECURE ACCESS
            </span>
            <h1 class="h3 font-serif fw-bold text-ink">Administration Portal</h1>
            <p class="text-muted small">Sign in to manage the resort content and bookings.</p>
        </div>
        <form method="post" action="{{ route('admin.login.store') }}" class="lux-card p-4 p-md-5">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold text-ink" for="email">
                    <i class="bi bi-envelope me-1 text-teal"></i>Email Address
                </label>
                <input class="form-control @error('email') is-invalid @enderror" name="email" id="email"
                       type="email" placeholder="admin@aruvi.test" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold text-ink" for="password">
                    <i class="bi bi-lock me-1 text-teal"></i>Password
                </label>
                <input class="form-control @error('password') is-invalid @enderror" name="password" id="password"
                       type="password" placeholder="••••••••" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <label class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember">
                <span class="form-check-label small text-muted">Remember me on this device</span>
            </label>
            <button class="btn btn-teal w-100 py-2 fw-semibold">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </form>
    </div>
</section>
@endsection
