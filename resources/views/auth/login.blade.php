@extends('layouts.frontend')
@section('title', 'Admin Login - Aruvi on the Cliff')
@section('content')
<section class="section section-soft"><div class="container" style="max-width:560px"><form method="post" action="{{ route('admin.login.store') }}" class="lux-card p-4">@csrf<h1 class="h3 mb-3 font-serif fw-bold text-ink">Administration Portal</h1><div class="mb-3"><label class="form-label small fw-bold">Email Address</label><input class="form-control" name="email" type="email" placeholder="admin@aruvi.test" required></div><div class="mb-3"><label class="form-label small fw-bold">Password</label><input class="form-control" name="password" type="password" placeholder="••••••••" required></div><label class="form-check mb-3"><input class="form-check-input" type="checkbox" name="remember"> Remember me</label><button class="btn btn-teal w-100 py-2">Sign In</button></form></div></section>
@endsection
