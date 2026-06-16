@extends('layouts.admin')

@section('title', 'Website Policies')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h1 class="fw-bold text-ink mb-1">Website Policies</h1>
        <p class="text-muted small mb-0">Add, edit, or delete the core resort policy pages displayed on the website.</p>
    </div>
</div>

<div class="row g-4">
    @foreach($policyNames as $slug => $label)
        @php
            $page = $pages->get($slug);
            $isPublished = $page?->is_published ? true : false;
        @endphp
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="fw-bold mb-1 text-ink">{{ $label }}</h4>
                            <small class="text-muted">Slug: <code>{{ $slug }}</code></small>
                        </div>
                        <span class="badge {{ $isPublished ? 'bg-success' : 'bg-secondary' }} text-white text-uppercase small py-2 px-3">
                            {{ $isPublished ? 'Published' : 'Draft' }}
                        </span>
                    </div>

                    <p class="text-muted small mb-4">{{ $page ? \Illuminate\Support\Str::limit(strip_tags($page->content), 120) : 'This policy page is not yet created. Add content to display it on the website.' }}</p>

                    <div class="d-flex gap-2 flex-wrap">
                        @if($page)
                            <a href="{{ route('admin.policies.edit', $page) }}" class="btn btn-outline-teal px-4 py-2">Edit</a>
                            <button class="btn btn-danger px-4 py-2" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" data-action="{{ route('admin.policies.destroy', $page) }}">Delete</button>
                        @else
                            <a href="{{ route('admin.policies.create', $slug) }}" class="btn btn-teal px-4 py-2">Create Page</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
