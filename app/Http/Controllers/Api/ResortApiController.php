<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Booking;
use App\Models\GalleryItem;
use App\Models\RestaurantItem;
use App\Models\Room;
use Illuminate\Http\Request;

class ResortApiController extends Controller
{
    public function rooms()
    {
        return Room::with('type', 'amenities')->where('is_active', true)->get();
    }

    public function amenities()
    {
        return Amenity::where('is_active', true)->get();
    }

    public function menu()
    {
        return RestaurantItem::with('restaurantCategory')->where('is_available', true)->get();
    }

    public function gallery()
    {
        return GalleryItem::with('galleryCategory')->get();
    }

    public function availability(Request $request)
    {
        $data = $request->validate([
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'room_id' => ['nullable', 'exists:rooms,id'],
        ]);

        $query = Room::where('is_active', true)->availableBetween($data['check_in'], $data['check_out']);
        if ($request->filled('room_id')) {
            $query->whereKey($data['room_id']);
        }

        return ['available' => $query->get(), 'blocked_bookings' => Booking::whereIn('status', ['pending', 'confirmed', 'checked_in'])->count()];
    }
}
