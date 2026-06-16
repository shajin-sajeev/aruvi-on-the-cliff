<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\Review;
use App\Models\Room;
use App\Models\User;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $confirmedQuery = Booking::whereIn('status', ['confirmed', 'checked_in', 'checked_out']);
        
        $totalConfirmedBookings = (clone $confirmedQuery)->count();
        $totalGuests = (clone $confirmedQuery)->sum('adults') + (clone $confirmedQuery)->sum('children');
        $totalRevenue = (clone $confirmedQuery)->sum('grand_total');

        return view('admin.dashboard', [
            'stats' => [
                'Rooms' => Room::count(),
                'Bookings' => $totalConfirmedBookings,
                'Revenue' => '₹'.number_format((float) $totalRevenue, 2),
                'Guests' => $totalGuests,
                'Messages' => ContactMessage::where('status', 'new')->count(),
                'Pending Reviews' => Review::where('is_approved', false)->count(),
            ],
            'recentBookings' => Booking::with('room', 'user')->latest()->take(8)->get(),
        ]);
    }
}
