@extends('portal.layout.app')

@section('title') Edit Blog @endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Card Component -->
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="mb-0">Edit Blog</h5>
        </div>
        <div class="card-body">
            <form id="editForm" action="{{ route('portal.blog.update', $blog->title) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="col-md-12 mb-3">
                    <label for="title" class="form-label">Blog Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $blog->title ?? '') }}">
                </div>
                <!-- Blog Category -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $blog->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($categories->isEmpty())
                            <p class="text-warning small mt-2 mb-0">No categories yet. <a href="{{ route('portal.blog-categories.index') }}">Add blog categories</a> first.</p>
                        @endif
                    </div>
                </div>

                <!-- Custom Author Name -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="custom_author" class="form-label">Author Name <small class="text-muted">(optional - leave blank to use your account name)</small></label>
                        <input type="text" class="form-control" id="custom_author" name="custom_author" value="{{ old('custom_author', $blog->custom_author ?? '') }}" placeholder="Leave blank to use your account name">
                    </div>
                </div>

                <!-- Blog Content -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="summernote" class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control jodit-editor" name="content" placeholder="Write some thing here" required> {{ old('content', $blog->content) }}</textarea>

                    </div>
                </div>

                <!-- Blog Banner -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="banner" class="form-label">Banner</label>
                        <input type="file" class="form-control" id="banner" name="banner">
                        @if($blog->banner)
                            <img src="{{ asset($blog->banner) }}" alt="Current Banner" class="mt-2" style="width: 200px;">
                        @endif
                    </div>
                </div>

                 <div class="row" >
                    <div class="col-md-3 mb-3">
                        <label for="active_date" class="form-label">Publish Date</label>
                        <input type="date" class="form-control" value="{{ old('active_date', $blog->active_date) }}" name="active_date" id="active_date">
                    </div>


                </div>
                <!-- Pin Article -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_pinned" id="is_pinned" value="1" {{ $blog->is_pinned ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_pinned">
                                Pin Article <small class="text-muted fw-normal">(Pinned articles always appear at the top of the listing)</small>
                            </label>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="is_archived" id="is_archived" value="{{ $blog->is_archived }}">

                <!-- Submit Button -->
                <button type="submit" onclick="setArchived(0)" class="btn btn-primary">Update & Publish</button>
                <button type="submit" onclick="setArchived(1)" class="btn btn-secondary">Save as Draft</button>
            </form>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/jodit/build/jodit.min.js"></script>
<script>
      function setArchived(value) {
        document.getElementById('is_archived').value = value;
    }

    const jodit = new Jodit('.jodit-editor', {
        toolbarSticky: false,
        placeholder: 'Type here...',
    });

    document.getElementById('editForm').addEventListener('submit', function (e) {
        const content = jodit.value;
        let plainText = content.replace(/<[^>]*>/g, '').trim();

        if (!plainText) {
            e.preventDefault();
            swal.fire('Validation Error', 'The content field is required.', 'error');
            return false;
        }
    });

</script>

@endsection


