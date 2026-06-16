@extends('layouts.admin')

@section('title', 'Message Inbox')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h1 class="fw-bold text-ink mb-1">Message Inbox</h1>
        <p class="text-muted small mb-0">Read and manage customer contact requests from the website.</p>
    </div>
    <div class="d-flex flex-wrap gap-2 align-items-center">
        <span class="badge bg-danger text-white px-3 py-2">Unread: {{ $unreadCount }}</span>
        <span class="badge bg-secondary text-white px-3 py-2">Total: {{ $messages->total() }}</span>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold text-ink">Recent Messages</h5>
                    <small class="text-muted">Click any message to view its full details.</small>
                </div>
                <span class="badge bg-teal-soft text-teal font-sans">{{ $messages->count() }} on page</span>
            </div>
            <div class="list-group list-group-flush">
                @forelse($messages as $item)
                    @php
                        $isNew = $item->status === 'new';
                    @endphp
                    <a href="{{ route('admin.messages.show', $item) }}" class="list-group-item list-group-item-action d-flex align-items-start gap-3 p-3 {{ $isNew ? 'bg-light bg-opacity-75' : '' }}">
                        <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 44px; height: 44px; background: {{ $isNew ? '#ffe8e6' : '#e7f7f2' }}; color: {{ $isNew ? '#c41c1c' : '#0f7d59' }}; font-weight: 700;">
                            {{ strtoupper(substr($item->name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                <div>
                                    <strong class="d-block text-ink">{{ $item->name }}</strong>
                                    <small class="text-muted">{{ $item->email }}</small>
                                </div>
                                <span class="badge {{ $isNew ? 'bg-danger' : 'bg-secondary' }} text-white text-uppercase small">{{ $item->status }}</span>
                            </div>
                            <div class="small text-ink fw-semibold">{{ $item->subject ?: 'No subject provided' }}</div>
                            <p class="text-muted small mb-0">{{ \Illuminate\Support\Str::limit($item->message, 90) }}</p>
                        </div>
                    </a>
                @empty
                    <div class="p-4 text-center text-muted small">
                        <i class="bi bi-envelope-open fs-1 d-block mb-3"></i>
                        No messages have arrived yet.
                    </div>
                @endforelse
            </div>
            <div class="card-footer bg-white border-top py-3">
                {{ $messages->links() }}
            </div>
        </div>
    </div>

    <div class="col-xl-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 p-4">
            <div class="d-flex flex-column justify-content-center align-items-center h-100 text-center">
                <div class="mb-4">
                    <span class="badge bg-teal-soft text-teal fs-6 mb-3">Tap the latest conversation</span>
                    <h2 class="fw-bold text-ink mb-3">Your message box is ready.</h2>
                    <p class="text-muted mb-0">Open any item from the inbox to read the customer's note, then mark it as read or delete it.</p>
                </div>
                <div class="d-flex gap-2 justify-content-center flex-wrap">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary px-4 py-2">Back to Dashboard</a>
                    <a href="{{ route('admin.messages.index') }}" class="btn btn-teal px-4 py-2">Refresh Inbox</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
