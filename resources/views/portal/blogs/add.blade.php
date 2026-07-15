@extends('portal.layout.app')

@section('title') Add Blog @endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Card Component -->
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="mb-0">Add Blog {{-- $editor --}}</h5>
        </div>
        <div class="card-body">
            <form id="modalForm" action="{{ route('portal.blog.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Title Input -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Blog Title" required>
                    </div>
                </div>

                <!-- Banner (Image Upload) -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="banner" class="form-label">Banner <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="banner" name="banner" accept="image/*" required>
                    </div>
                </div>

                <!-- Content Input -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="summernote" class="form-label">Content <span class="text-danger">*</span></label>
                        @if($editor=='summernote')
                        <textarea  class="form-control summernote" id="summernote" name="content" placeholder="Enter Blog Content" required></textarea>
                        @elseif($editor=='ck')
                        <textarea class="form-control ckeditor" name="content" placeholder="Enter Blog Content" required>CK Editor</textarea>
                        @elseif($editor=='quill')
                        <div class="quill-editor" style="min-height: 200px;">Quill Editor</div>
                        @elseif($editor=='jodit')
                        <textarea class="form-control jodit-editor" name="jodit_content" placeholder="Write some thing here"></textarea>
                        @elseif($editor=='trix')
                         <input id="trix-content" type="hidden" name="trix_content">
                         <trix-editor input="trix-content">Trix Editor</trix-editor>
                        @endif
                    </div>
                </div>
                <!-- hiddeb input for content to send to server -->
                <input type="hidden" name="content" id="hiddenContent">

                <!-- Category Selection -->
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
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
                        <input type="text" class="form-control" id="custom_author" name="custom_author" placeholder="Leave blank to use your account name">
                    </div>
                </div>

               <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="active_date" class="form-label">Publish Date</label>
                        <input type="date" value="{{ date('Y-m-d'); }}" class="form-control" name="active_date" id="active_date" min="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <!-- Pin Article -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_pinned" id="is_pinned" value="1">
                            <label class="form-check-label fw-semibold" for="is_pinned">
                                Pin Article <small class="text-muted fw-normal">(Pinned articles always appear at the top of the listing)</small>
                            </label>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="is_archived" id="is_archived" value="0">
                <!-- Submit Button -->
                <button type="submit" onclick="setArchived(0)" class="btn btn-primary">Publish</button>
                <button type="submit" onclick="setArchived(1)" class="btn btn-secondary">Save as Draft</button>
            </form>
        </div>
    </div>

    <!-- Progress Bar Container -->
    <div id="progress-container" style="display: none;">
        <div class="progress">
            <div id="progress-bar" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>
</div>

<!-- Summernote CSS & JS -->
@if($editor=='summernote')
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote.min.js"></script>
<script>
$(document).ready(function() {
    $('.summernote').summernote();
});
</script>
@endif

<!-- CK Editor -->
@if($editor=='ck')
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
<script>
    let ckeditorInstance;
    ClassicEditor.create(document.querySelector('.ckeditor'))
        .then(editor => {
            ckeditorInstance = editor;
        })
        .catch(error => console.error(error));
</script>
@endif

<!-- Quill -->
@if($editor=='quill')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
    const quill = new Quill('.quill-editor', {
        theme: 'snow',
    });
</script>
@endif

<!-- Jodit -->
@if($editor=='jodit')
<script src="https://cdn.jsdelivr.net/npm/jodit/build/jodit.min.js"></script>
<script>
    const jodit = new Jodit('.jodit-editor', {
        toolbarSticky: false,
        placeholder: 'Type here...',
    });
</script>
@endif

<!-- Trix -->
@if($editor=='trix')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"></script>
@endif
<script>
     function setArchived(value) {
        document.getElementById('is_archived').value = value;
    }

    document.getElementById('modalForm').addEventListener('submit', function (e) {
        const editor = "{{ $editor }}";
        let content = '';

        if (editor === 'quill') {
            content = quill.root.innerHTML;
        } else if (editor === 'jodit') {
            content = jodit.value;
        } else if (editor === 'trix') {
            content = document.querySelector('#trix-content').value;
        } else if (editor === 'ck' && typeof ckeditorInstance !== 'undefined') {
            content = ckeditorInstance.getData();
        } else if (editor === 'summernote') {
            content = $('.summernote').summernote('code');
        }

        let plainText = content.replace(/<[^>]*>/g, '').trim();
        if (!plainText) {
            e.preventDefault();
            return false;
        }
        document.getElementById('hiddenContent').value = content;
    });
</script>
@endsection
