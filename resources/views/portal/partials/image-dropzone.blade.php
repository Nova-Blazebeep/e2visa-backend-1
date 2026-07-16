{{-- Single-image picker. Pass ['currentImageUrl' => ...] from the edit page to show the saved image. --}}
@php $currentImageUrl = $currentImageUrl ?? ''; @endphp

<style>
    .re-dropzone {
        border: 2px dashed #c4c9d0;
        border-radius: 8px;
        padding: 16px;
        text-align: center;
        cursor: pointer;
        background: #fafbfc;
        transition: border-color .2s, background .2s;
    }

    .re-dropzone:hover,
    .re-dropzone.re-dragover {
        border-color: #0d6efd;
        background: #f0f6ff;
    }

    .re-dropzone .re-dz-text {
        color: #8a919a;
        font-size: 13px;
        margin: 0;
        padding: 8px 0;
    }

    .re-dropzone .re-dz-text svg {
        display: block;
        margin: 0 auto 8px;
    }

    .re-dropzone .re-dz-preview {
        display: flex;
        align-items: center;
        gap: 14px;
        text-align: left;
    }

    .re-dropzone .re-dz-preview img {
        width: 110px;
        height: 80px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        flex-shrink: 0;
    }

    .re-dropzone .re-dz-label {
        font-size: 12px;
        color: #6c757d;
        display: block;
        margin-bottom: 6px;
        word-break: break-all;
    }
</style>

<div id="reDropzone" class="re-dropzone">
    <input type="file" id="images" accept="image/*" name="images[]" class="d-none">

    <p id="reDzPlaceholder" class="re-dz-text">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#8a919a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <polyline points="21 15 16 10 5 21"/>
        </svg>
        Drag &amp; drop an image here, or <u>click to browse</u>
    </p>

    <div id="reDzPreview" class="re-dz-preview d-none">
        <img id="reDzPreviewImg" src="" alt="Preview">
        <div>
            <span id="reDzPreviewLabel" class="re-dz-label"></span>
            <button type="button" id="reDzChangeBtn" class="btn btn-sm btn-outline-primary me-1">Change</button>
            <button type="button" id="reDzRemoveBtn" class="btn btn-sm btn-outline-danger d-none">Remove</button>
        </div>
    </div>
</div>

<script>
    (function() {
        const existingImageUrl = @json($currentImageUrl);
        const input = document.getElementById('images');
        const dz = document.getElementById('reDropzone');
        const placeholder = document.getElementById('reDzPlaceholder');
        const preview = document.getElementById('reDzPreview');
        const previewImg = document.getElementById('reDzPreviewImg');
        const previewLabel = document.getElementById('reDzPreviewLabel');
        const removeBtn = document.getElementById('reDzRemoveBtn');

        function showPreview(src, label, isNew) {
            previewImg.src = src;
            previewLabel.textContent = label;
            placeholder.classList.add('d-none');
            preview.classList.remove('d-none');
            // "Remove" only reverts a newly picked file
            removeBtn.classList.toggle('d-none', !isNew);
        }

        function resetToInitial() {
            input.value = '';
            if (existingImageUrl) {
                showPreview(existingImageUrl, 'Current image', false);
            } else {
                preview.classList.add('d-none');
                placeholder.classList.remove('d-none');
            }
        }

        function handleFile(file) {
            if (!file || !file.type.startsWith('image/')) return;
            showPreview(URL.createObjectURL(file),
                existingImageUrl ? file.name + ' (will replace current image)' : file.name,
                true);
        }

        dz.addEventListener('click', function(e) {
            if (e.target.closest('#reDzRemoveBtn')) return;
            input.click();
        });

        input.addEventListener('click', function(e) { e.stopPropagation(); });

        input.addEventListener('change', function() {
            handleFile(this.files[0]);
        });

        ['dragover', 'dragenter'].forEach(function(ev) {
            dz.addEventListener(ev, function(e) {
                e.preventDefault();
                dz.classList.add('re-dragover');
            });
        });

        ['dragleave', 'dragend'].forEach(function(ev) {
            dz.addEventListener(ev, function(e) {
                e.preventDefault();
                dz.classList.remove('re-dragover');
            });
        });

        dz.addEventListener('drop', function(e) {
            e.preventDefault();
            dz.classList.remove('re-dragover');
            const files = e.dataTransfer ? e.dataTransfer.files : [];
            if (files.length) {
                const dt = new DataTransfer();
                dt.items.add(files[0]); // single image only
                input.files = dt.files;
                handleFile(files[0]);
            }
        });

        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            resetToInitial();
        });

        resetToInitial();
    })();
</script>
