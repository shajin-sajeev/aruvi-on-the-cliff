@extends('layouts.admin')

@section('title', $config['title'])

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.resources.index', $resource) }}" class="btn btn-sm btn-link text-teal text-decoration-none fw-semibold p-0">
        <i class="bi bi-arrow-left me-1"></i> Back to {{ $config['title'] }} List
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
    <div class="form-card-header d-flex align-items-center justify-content-between">
        <div>
            <h5 class="fw-bold font-serif mb-1 text-white">
                <i class="bi {{ $item ? 'bi-pencil-square' : 'bi-plus-circle-fill' }} me-2 text-teal-soft"></i>
                {{ $item ? 'Modify Existing Entry' : 'Create New Entry' }}
            </h5>
            <small class="text-white opacity-75 small">Fill in the fields below. Required files and inputs will be validated on save.</small>
        </div>
        <span class="badge bg-teal-soft text-white border border-light border-opacity-25 px-3 py-2 text-uppercase tracking-wider extra-small">{{ $resource }}</span>
    </div>
    
    <form method="post" action="{{ $item ? route('admin.resources.update', [$resource, $item->id]) : route('admin.resources.store', $resource) }}" class="p-4 p-md-5 bg-white" enctype="multipart/form-data">
        @csrf
        @if($item) @method('patch') @endif
        
        <div class="row g-4">
            @foreach($config['fields'] as $field)
                @php([$name, $type] = array_pad(explode(':', $field, 2), 2, 'text'))
                
                <div class="{{ $type === 'textarea' ? 'col-12' : 'col-md-6' }}">
                    <label class="form-label fw-bold small text-ink mb-2">
                        @php($fieldIcon = ['file' => 'bi-image', 'number' => 'bi-hash', 'checkbox' => 'bi-toggle-on', 'textarea' => 'bi-text-paragraph'][$type] ?? 'bi-input-cursor-text')
                        <i class="bi {{ $fieldIcon }} text-teal me-1"></i>
                        {{ str($name)->headline() }}
                    </label>

                    @if($resource === 'settings' && $item && $item->type === 'file' && $name === 'value')
                        <input class="form-control" type="file" name="value">
                        @if($item->value)
                            <div class="mt-3 p-3 bg-light rounded-3 d-inline-block">
                                <small class="text-muted d-block mb-2"><i class="bi bi-eye-fill me-1"></i>Current File Preview:</small>
                                <img src="{{ asset($item->value) }}" alt="Preview" class="img-thumbnail" style="max-height: 80px; width: auto;">
                            </div>
                        @endif
                    @elseif($type === 'textarea')
                        <textarea class="form-control" rows="6" name="{{ $name }}" placeholder="Provide detailed {{ str($name)->headline() }} content...">{{ old($name, $item?->{$name}) }}</textarea>
                    @elseif($type === 'checkbox')
                        <div class="form-check form-switch p-3 border rounded-3 bg-light bg-opacity-50">
                            <input class="form-check-input ms-0 me-2" type="checkbox" name="{{ $name }}" value="1" id="switch-{{ $name }}" @checked(old($name, $item?->{$name}))>
                            <label class="form-check-label text-ink fw-semibold small" for="switch-{{ $name }}">Enabled Status</label>
                            <small class="text-muted d-block extra-small mt-1">Check to make this entry live or active on the website.</small>
                        </div>
                    @elseif($type === 'file')
                        <div class="border border-dashed p-4 rounded-3 text-center bg-light bg-opacity-50">
                            <i class="bi bi-cloud-arrow-up text-teal fs-3 d-block mb-2"></i>
                            <input class="form-control" type="file" name="{{ $name }}">
                            <small class="text-muted d-block mt-1 extra-small">Recommended image size: 1200x800px (JPG/PNG/WEBP)</small>
                            @if($item?->{$name})
                                <div class="mt-3">
                                    <small class="text-muted d-block mb-2"><i class="bi bi-eye-fill me-1"></i>Current Image:</small>
                                    <img src="{{ asset($item->{$name}) }}" alt="Preview" class="img-thumbnail shadow-sm" style="max-height: 120px; width: auto; object-fit: cover;">
                                </div>
                            @endif
                        </div>
                    @else
                        @if(str_ends_with($name, '_id') && isset($relations[$name]))
                            <select class="form-select" name="{{ $name }}">
                                <option value="">Select {{ str(substr($name, 0, -3))->headline() }}...</option>
                                @foreach($relations[$name] as $rel)
                                    <option value="{{ $rel->id }}" @selected(old($name, $item?->{$name}) == $rel->id)>
                                        {{ $rel->name ?? $rel->title ?? $rel->key ?? $rel->email ?? 'Option #'.$rel->id }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            <input class="form-control" type="{{ $type === 'number' ? 'number' : 'text' }}" step="0.01" name="{{ $name }}" value="{{ old($name, $item?->{$name}) }}" placeholder="Enter {{ str($name)->headline() }}...">
                        @endif
                    @endif
                </div>
            @endforeach
        </div>

        <div class="d-flex flex-wrap gap-2 justify-content-end mt-5 pt-4 border-top">
            <a href="{{ route('admin.resources.index', $resource) }}" class="btn btn-outline-secondary px-4 py-2.5 fw-semibold">
                Cancel
            </a>
            <button class="btn btn-teal px-5 py-2.5 shadow-sm fw-bold">
                <i class="bi bi-check-circle-fill me-2"></i>Save & Publish
            </button>
        </div>
    </form>
</div>
@endsection
