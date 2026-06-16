<div class="lux-card h-100 position-relative overflow-hidden">
    <div class="position-relative">
        <img class="room-image w-100" src="{{ $room->cover_image ?: 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?auto=format&fit=crop&w=900&q=80' }}" alt="{{ $room->name }}">
        @php
            $roomBadgeText = null;
            $roomBadgeClass = 'bg-danger text-white';
            if ($room->discount_price && $room->discount_price < $room->price_per_night) {
                $roomBadgeText = 'Special Offer';
                $roomBadgeClass = 'bg-danger text-white';
            } elseif ($room->discount_price && $room->discount_price > $room->price_per_night) {
                $roomBadgeText = 'Peak Season';
                $roomBadgeClass = 'bg-warning text-dark';
            }
        @endphp
        @if($roomBadgeText)
            <div class="offer-badge {{ $roomBadgeClass === 'bg-warning text-dark' ? 'offer-badge--peak' : 'offer-badge--offer' }}">
                @if($roomBadgeClass === 'bg-warning text-dark')
                    <i class="bi bi-calendar-event-fill"></i>
                @else
                    <i class="bi bi-tag-fill"></i>
                @endif
                <span>{{ $roomBadgeText }}</span>
            </div>
        @endif
    </div>
    <div class="p-4">
        <div class="eyebrow">{{ $room->type?->name }}</div>
        <h4>{{ $room->name }}</h4>
        <p>{{ $room->short_description }}</p>
        <div class="d-flex justify-content-between align-items-center">
            <strong>₹{{ number_format($room->discount_price ?: $room->price_per_night) }}/night</strong>
            <a class="btn btn-sm btn-outline-teal" href="{{ route('rooms.show', $room) }}">Details</a>
        </div>
    </div>
</div>
