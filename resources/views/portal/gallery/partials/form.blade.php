<form class="form" id="galleryForm" action="{{ route('media_gallery.storeOrUpdate', $record->id ?? null) }}" method="POST"
    enctype="multipart/form-data">
    @csrf

    <div class="row">
        <!-- Title -->
        <div class="col-md-12 mb-3">
            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $record->name ?? '' }}" required>
        </div>
    </div>

    <div class="row">
        <!-- Image Upload -->
        <div class="col-md-12 mb-3">
            <label for="image" class="form-label">Image <span class="text-danger">*</span></label>
            <input type="file" class="form-control" id="image" name="image" {{ isset($record) ? '' : 'required' }}>
            <img id="image-preview" class="mt-2 rounded" width="200" style="display: none;">
        </div>
        @if (isset($record->path))
            <div class="col-md-12">
                <img src="{{ $record->path }}" class="img-fluid existing-image rounded mb-3" style="max-width: 200px; height: auto;">
            </div>
        @endif
        <!-- Hidden input for cropped image -->
        <input type="hidden" id="cropped_image" name="cropped_image">
        <input type="hidden" id="path" name="path">
    </div>
    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>
