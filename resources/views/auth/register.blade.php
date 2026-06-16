@extends('layouts.frontend')
@section('title', 'Guest Registration - Aruvi on the Cliff')
@section('content')
<section class="section section-soft"><div class="container" style="max-width:640px"><form method="post" action="{{ route('register.store') }}" class="lux-card p-4">@csrf<h1 class="h3 mb-3">Create Guest Account</h1><div class="row g-3"><div class="col-md-6"><input class="form-control" name="name" placeholder="Name" required></div><div class="col-md-6"><input class="form-control" name="phone" placeholder="Phone"></div><div class="col-12"><input class="form-control" name="email" type="email" placeholder="Email" required></div><div class="col-md-6"><input class="form-control" name="password" type="password" placeholder="Password" required></div><div class="col-md-6"><input class="form-control" name="password_confirmation" type="password" placeholder="Confirm Password" required></div></div><button class="btn btn-teal w-100 mt-3">Register</button></form></div></section>
@endsection
