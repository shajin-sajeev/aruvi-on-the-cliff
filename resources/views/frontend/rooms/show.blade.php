@extends('layouts.frontend')
@section('title', $room->seo_title ?: $room->name.' - Aruvi on the Cliff')
@section('meta_description', $room->seo_description ?: $room->short_description)
@section('content')
<section class="section section-soft">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6"><img class="img-fluid lux-card" src="{{ $room->cover_image ?: 'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=80' }}" alt="{{ $room->name }}"></div>
            <div class="col-lg-6"><div class="eyebrow">{{ $room->type?->name }}</div><h1 class="display-5 fw-bold">{{ $room->name }}</h1><p class="lead">{{ $room->short_description }}</p><p>{!! nl2br(e($room->description)) !!}</p><h4>₹{{ number_format($room->discount_price ?: $room->price_per_night) }} / night</h4><a href="{{ route('booking.create', ['room' => $room->id]) }}" class="btn btn-teal btn-lg mt-3">Book This Room</a></div>
        </div>
    </div>
</section>
<section class="section"><div class="container"><h3 class="fw-bold mb-4">Room Amenities</h3><div class="row g-3">@foreach($room->amenities as $amenity)<div class="col-md-3"><div class="lux-card p-3">{{ $amenity->name }}</div></div>@endforeach</div></div></section>
@endsection
