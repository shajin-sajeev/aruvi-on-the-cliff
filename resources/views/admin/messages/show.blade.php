@extends('layouts.admin')

@section('title', 'Message Details')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h1 class="fw-bold text-ink mb-1">Message Details</h1>
        <p class="text-muted small mb-0">This message has been marked as read automatically when opened.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.messages.index') }}" class="btn btn-outline-secondary px-4 py-2">Back to Inbox</a>
        <button type="button" class="btn btn-danger px-4 py-2" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" data-action="{{ route('admin.messages.destroy', $message) }}">Delete</button>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h3 class="fw-bold text-ink mb-1">{{ $message->subject ?: 'No subject set' }}</h3>
                        <small class="text-muted">Sent on {{ $message->created_at->format('d M Y, H:i A') }}</small>
                    </div>
                    <span class="badge {{ $message->status === 'new' ? 'bg-danger' : 'bg-success' }} text-white text-uppercase px-3 py-2 small">{{ $message->status }}</span>
                </div>

                <div class="bg-light border rounded-4 p-4 mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">From</p>
                            <h5 class="fw-bold mb-0">{{ $message->name }}</h5>
                            <p class="mb-0 text-muted">{{ $message->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Phone</p>
                            <h5 class="fw-bold mb-0">{{ $message->phone ?: 'Not provided' }}</h5>
                            <p class="mb-0 text-muted">Message ID #{{ $message->id }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-4 border border-secondary border-opacity-10 overflow-hidden">
                    <div class="bg-teal text-white px-4 py-3">
                        <h6 class="mb-0">Message Content</h6>
                    </div>
                    <div class="p-4 bg-white">
                        <p class="text-ink mb-0" style="white-space: pre-line;">{{ $message->message }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
            <div class="card-header bg-teal text-white py-3">
                <h5 class="mb-0 fw-bold">Quick Actions</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="icon-avatar-small bg-teal-soft text-teal"><i class="bi bi-envelope-fill"></i></div>
                    <div>
                        <p class="text-muted small mb-1">Status</p>
                        <strong>{{ $message->status === 'new' ? 'Unread message' : 'Already read' }}</strong>
                    </div>
                </div>
                <div class="mb-4">
                    <p class="text-muted small mb-2">Sender</p>
                    <p class="fw-semibold mb-1">{{ $message->name }}</p>
                    <p class="text-muted small mb-0">{{ $message->email }}</p>
                </div>
                <div class="mb-4">
                    <p class="text-muted small mb-2">Subject</p>
                    <p class="fw-semibold mb-0">{{ $message->subject ?: 'None' }}</p>
                </div>
                <div>
                    <p class="text-muted small mb-2">Received</p>
                    <p class="fw-semibold mb-0">{{ $message->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
