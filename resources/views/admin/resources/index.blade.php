@extends('layouts.admin')

@section('title', $config['title'])

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h1 class="fw-bold text-ink mb-1">{{ $config['title'] }}</h1>
        <p class="text-muted small mb-0">Manage content entries, view details, and perform record updates.</p>
    </div>
    <div>
        <a class="btn btn-teal px-4 py-2.5 shadow-sm fw-bold" href="{{ route('admin.resources.create', $resource) }}">
            <i class="bi bi-plus-lg me-2"></i>Add New Entry
        </a>
    </div>
</div>

<div class="table-card table-responsive">
    <table class="table align-middle mb-0 table-hover">
        <thead>
            <tr>
                <th class="ps-4" style="width: 80px;">ID</th>
                <th>Record Details</th>
                <th>Last Updated</th>
                <th class="pe-4 text-end" style="width: 150px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                @php
                    $thumb = $item->image ?? $item->cover_image ?? null;
                    $titleText = $item->title ?? $item->name ?? $item->key ?? $item->email ?? 'Record #'.$item->id;
                @endphp
                <tr>
                    <td class="ps-4 fw-bold text-secondary">#{{ $item->id }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($thumb)
                                <img src="{{ asset($thumb) }}" class="admin-thumb-avatar me-3" alt="Thumbnail">
                            @else
                                <div class="admin-thumb-avatar me-3 d-flex align-items-center justify-content-center bg-teal-soft text-teal fw-bold font-serif" style="font-size: 1.15rem;">
                                    {{ strtoupper(substr($titleText, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <span class="fw-bold d-block text-ink">{{ $titleText }}</span>
                                @if(isset($item->category))
                                    <small class="text-teal extra-small fw-semibold text-uppercase">{{ $item->category }}</small>
                                @elseif(isset($item->room_number))
                                    <small class="text-teal extra-small fw-semibold text-uppercase">Room {{ $item->room_number }}</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="text-muted small">
                        {{ optional($item->updated_at)->format('d M Y') }}
                        <small class="d-block extra-small text-muted-light">{{ optional($item->updated_at)->format('H:i A') }}</small>
                    </td>
                    <td class="pe-4 text-end">
                        <div class="d-inline-flex gap-2">
                            <a class="btn btn-action-circle btn-edit" href="{{ route('admin.resources.edit', [$resource, $item->id]) }}" title="Edit Entry">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button class="btn btn-action-circle btn-delete" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteConfirmModal" 
                                    data-action="{{ route('admin.resources.destroy', [$resource, $item->id]) }}" 
                                    title="Delete Entry">
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

<div class="mt-4">
    {{ $items->links() }}
</div>
@endsection
