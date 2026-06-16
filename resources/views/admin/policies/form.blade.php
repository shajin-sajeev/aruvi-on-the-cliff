@extends('layouts.admin')

@section('title', $editing ? 'Edit Policy Page' : 'Create Policy Page')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.policies.index') }}" class="btn btn-sm btn-link text-teal text-decoration-none fw-semibold p-0">
        <i class="bi bi-arrow-left me-1"></i> Back to Website Policies
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
    <div class="form-card-header d-flex align-items-center justify-content-between px-4 py-3 bg-teal text-white">
        <div>
            <h5 class="fw-bold mb-1 font-serif">{{ $editing ? 'Update Policy Content' : 'Create New Policy Page' }}</h5>
            <small class="opacity-75">Manage the policy content shown on the public website.</small>
        </div>
        <span class="badge bg-white text-teal text-uppercase px-3 py-2 small">{{ $page->slug }}</span>
    </div>

    <form method="post" action="{{ $editing ? route('admin.policies.update', $page) : route('admin.policies.store') }}" class="p-4 p-md-5 bg-white" enctype="multipart/form-data">
        @csrf
        @if($editing)
            @method('patch')
        @endif

        <input type="hidden" name="slug" value="{{ $page->slug }}">

        <div class="row g-4">
            <div class="col-12">
                <label class="form-label fw-bold small text-ink mb-2">Page Title</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $page->title) }}" placeholder="Policy Page Title" required>
            </div>

            <div class="col-12">
                <label class="form-label fw-bold small text-ink mb-2">Page Content</label>
                <textarea id="policy-content-editor" name="content" class="form-control" rows="10" placeholder="Enter the policy text here...">{{ old('content', $page->content) }}</textarea>
            </div>

            <div class="col-12">
                <div class="form-check form-switch p-3 border rounded-3 bg-light bg-opacity-50">
                    <input class="form-check-input ms-0 me-2" type="checkbox" name="is_published" value="1" id="switch-is-published" @checked(old('is_published', $page->is_published))>
                    <label class="form-check-label text-ink fw-semibold small" for="switch-is-published">Published</label>
                    <small class="text-muted d-block extra-small mt-1">Enable this to make the policy page available on the public website.</small>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 justify-content-end mt-5 pt-4 border-top">
            <a href="{{ route('admin.policies.index') }}" class="btn btn-outline-secondary px-4 py-2.5 fw-semibold">Cancel</a>
            <button class="btn btn-teal px-5 py-2.5 shadow-sm fw-bold">{{ $editing ? 'Update Policy' : 'Create Policy' }}</button>
        </div>
    </form>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editorElement = document.querySelector('#policy-content-editor');
        let policyEditor = null;

        const policyForm = document.querySelector('form[action="{{ $editing ? route('admin.policies.update', $page) : route('admin.policies.store') }}"]');

        if (editorElement) {
            ClassicEditor.create(editorElement, {
                toolbar: [
                    'heading', '|', 'bold', 'italic', 'underline', 'link', 'bulletedList', 'numberedList',
                    'blockQuote', 'undo', 'redo', 'alignment', 'outdent', 'indent'
                ],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                    ]
                }
            })
            .then(editor => {
                policyEditor = editor;

                if (policyForm) {
                    policyForm.addEventListener('submit', function () {
                        policyEditor.updateSourceElement();
                    });
                }
            })
            .catch(error => {
                console.error('CKEditor load error:', error);
            });
        }
    });
</script>
@endsection
