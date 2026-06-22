@extends('layouts.admin')
@section('title', $config['title'])
@section('content')

@php
$platformLogos = [
    'facebook'  => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Facebook_Logo_%282019%29.png/600px-Facebook_Logo_%282019%29.png',
    'instagram' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Instagram_icon.png/600px-Instagram_icon.png',
    'twitter'   => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6f/Logo_of_Twitter.svg/600px-Logo_of_Twitter.svg.png',
    'x'         => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/57/X_logo_2023_%28white_background%29.png/600px-X_logo_2023_%28white_background%29.png',
    'youtube'   => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/09/YouTube_full-color_icon_%282017%29.svg/600px-YouTube_full-color_icon_%282017%29.svg.png',
    'linkedin'  => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/LinkedIn_logo_initials.png/600px-LinkedIn_logo_initials.png',
    'pinterest' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/08/Pinterest-logo.png/600px-Pinterest-logo.png',
    'tiktok'    => 'https://upload.wikimedia.org/wikipedia/en/thumb/a/a9/TikTok_logo.svg/600px-TikTok_logo.svg.png',
    'whatsapp'  => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6b/WhatsApp.svg/600px-WhatsApp.svg.png',
];
@endphp

<div class="admin-page-header">
    <div>
        <h1>{{ $config['title'] }}</h1>
        <p class="text-muted small mb-0">Manage content entries, view details, and perform record updates.</p>
    </div>
    <a class="btn btn-teal fw-semibold shadow-sm" href="{{ route('admin.resources.create', $resource) }}">
        <i class="bi bi-plus-lg me-2"></i>Add New Entry
    </a>
</div>

{{-- ── Desktop Table ─────────────────────────────────────────── --}}
<div class="listing-desktop-table">
    <div class="table-card table-responsive">
        <table class="table align-middle mb-0 table-hover">
            <thead>
                <tr>
                    <th class="ps-4" style="width:70px;">ID</th>
                    <th>Record Details</th>
                    <th>Last Updated</th>
                    <th class="pe-4 text-end" style="width:130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    @php
                        $isSocialLinks = $resource === 'social-links';
                        $thumb = null;
                        $titleText = '';
                        $subText   = null;

                        if ($isSocialLinks) {
                            $platform    = strtolower($item->platform ?? '');
                            $thumb       = $platformLogos[$platform] ?? null;
                            $titleText   = ucfirst($item->platform ?? 'Unknown');
                            $subText     = $item->url ?? null;
                        } else {
                            $thumb     = $item->image ?? $item->cover_image ?? null;
                            $titleText = $item->title ?? $item->name ?? $item->key ?? $item->email ?? 'Record #'.$item->id;
                        }
                    @endphp
                    <tr>
                        <td class="ps-4 fw-bold text-secondary small">#{{ $item->id }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($isSocialLinks && $thumb)
                                    <div class="admin-thumb-avatar me-3 d-flex align-items-center justify-content-center bg-light border"
                                         style="padding:4px;">
                                        <img src="{{ $thumb }}" alt="{{ $titleText }}"
                                             style="width:28px;height:28px;object-fit:contain;">
                                    </div>
                                @elseif($thumb)
                                    <img src="{{ asset($thumb) }}" class="admin-thumb-avatar me-3" alt="Thumbnail">
                                @else
                                    <div class="admin-thumb-avatar me-3 d-flex align-items-center justify-content-center bg-teal-soft text-teal fw-bold font-serif" style="font-size:1.1rem;">
                                        {{ strtoupper(substr($titleText,0,1)) }}
                                    </div>
                                @endif
                                <div>
                                    <span class="fw-bold d-block text-ink">{{ $titleText }}</span>
                                    @if($subText)
                                        <small class="text-muted extra-small">{{ \Illuminate\Support\Str::limit($subText, 50) }}</small>
                                    @elseif(isset($item->category))
                                        <small class="text-teal extra-small fw-semibold text-uppercase">{{ $item->category }}</small>
                                    @elseif(isset($item->room_number))
                                        <small class="text-teal extra-small fw-semibold text-uppercase">Room {{ $item->room_number }}</small>
                                    @endif
                                    @if($isSocialLinks)
                                        <span class="badge ms-1 {{ $item->is_active ? 'bg-success' : 'bg-secondary' }} text-white"
                                              style="font-size:0.62rem;">
                                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="text-muted small">
                            {{ optional($item->updated_at)->format('d M Y') }}
                            <small class="d-block extra-small">{{ optional($item->updated_at)->format('H:i A') }}</small>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="d-inline-flex gap-2">
                                <a class="btn btn-action-circle btn-edit"
                                   href="{{ route('admin.resources.edit',[$resource,$item->id]) }}"
                                   title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button class="btn btn-action-circle btn-delete"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteConfirmModal"
                                        data-action="{{ route('admin.resources.destroy',[$resource,$item->id]) }}"
                                        title="Delete">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                            No records found for {{ $config['title'] }}.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Mobile Card List ──────────────────────────────────────── --}}
<div class="listing-mobile-cards">
    @forelse($items as $item)
        @php
            $isSocialLinks = $resource === 'social-links';
            $thumb = null;
            $titleText = '';
            $subText   = null;

            if ($isSocialLinks) {
                $platform  = strtolower($item->platform ?? '');
                $thumb     = $platformLogos[$platform] ?? null;
                $titleText = ucfirst($item->platform ?? 'Unknown');
                $subText   = $item->url ?? null;
            } else {
                $thumb     = $item->image ?? $item->cover_image ?? null;
                $titleText = $item->title ?? $item->name ?? $item->key ?? $item->email ?? 'Record #'.$item->id;
            }
        @endphp
        <div class="admin-record-card">
            <div class="admin-record-card-header">
                @if($isSocialLinks && $thumb)
                    <div class="admin-thumb-avatar d-flex align-items-center justify-content-center bg-light border flex-shrink-0"
                         style="padding:4px;">
                        <img src="{{ $thumb }}" alt="{{ $titleText }}"
                             style="width:28px;height:28px;object-fit:contain;">
                    </div>
                @elseif($thumb)
                    <img src="{{ asset($thumb) }}" class="admin-thumb-avatar" alt="Thumbnail" style="flex-shrink:0;">
                @else
                    <div class="admin-thumb-avatar d-flex align-items-center justify-content-center bg-teal-soft text-teal fw-bold font-serif"
                         style="font-size:1rem;flex-shrink:0;">
                        {{ strtoupper(substr($titleText,0,1)) }}
                    </div>
                @endif
                <div class="flex-grow-1 min-w-0">
                    <div class="fw-bold text-ink" style="font-size:0.9rem;">{{ $titleText }}</div>
                    @if($subText)
                        <small class="text-muted extra-small text-truncate d-block">{{ \Illuminate\Support\Str::limit($subText, 45) }}</small>
                    @elseif(isset($item->category))
                        <small class="text-teal extra-small fw-semibold text-uppercase">{{ $item->category }}</small>
                    @elseif(isset($item->room_number))
                        <small class="text-teal extra-small fw-semibold text-uppercase">Room {{ $item->room_number }}</small>
                    @endif
                    @if($isSocialLinks)
                        <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }} text-white ms-1"
                              style="font-size:0.6rem;">
                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    @endif
                </div>
                <span class="extra-small text-muted flex-shrink-0">#{{ $item->id }}</span>
            </div>
            <div class="admin-record-card-meta">
                <div class="admin-record-card-meta-item">
                    <i class="bi bi-clock"></i>
                    <span>{{ optional($item->updated_at)->format('d M Y, H:i A') ?? '—' }}</span>
                </div>
            </div>
            <div class="admin-record-card-actions">
                <a class="btn btn-sm btn-outline-secondary"
                   href="{{ route('admin.resources.edit',[$resource,$item->id]) }}">
                    <i class="bi bi-pencil-square me-1"></i>Edit
                </a>
                <button class="btn btn-sm btn-outline-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteConfirmModal"
                        data-action="{{ route('admin.resources.destroy',[$resource,$item->id]) }}">
                    <i class="bi bi-trash3-fill me-1"></i>Delete
                </button>
            </div>
        </div>
    @empty
        <div class="text-center py-5 text-muted">
            <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
            No records found for {{ $config['title'] }}.
        </div>
    @endforelse
</div>

<div class="mt-4">{{ $items->links() }}</div>
@endsection
