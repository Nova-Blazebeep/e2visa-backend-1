{{-- Single-document picker. Pass ['currentDocUrl' => ...] from the edit page to show the saved document. --}}
@php $currentDocUrl = $currentDocUrl ?? ''; @endphp

<style>
    .doc-dropzone {
        border: 2px dashed #c4c9d0;
        border-radius: 8px;
        padding: 14px 16px;
        text-align: center;
        cursor: pointer;
        background: #fafbfc;
        transition: border-color .2s, background .2s;
    }

    .doc-dropzone:hover,
    .doc-dropzone.doc-dragover {
        border-color: #0d6efd;
        background: #f0f6ff;
    }

    .doc-dropzone .doc-dz-text {
        color: #8a919a;
        font-size: 13px;
        margin: 0;
        padding: 4px 0;
    }

    .doc-dropzone .doc-dz-preview {
        display: flex;
        align-items: center;
        gap: 12px;
        text-align: left;
    }

    .doc-dropzone .doc-dz-icon {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        background: #eef1f5;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .doc-dropzone .doc-dz-label {
        font-size: 12px;
        color: #6c757d;
        display: block;
        margin-bottom: 6px;
        word-break: break-all;
    }
</style>

<div id="docDropzone" class="doc-dropzone">
    <input type="file" id="seller_financial_documents" name="seller_financial_documents[]" class="d-none">

    <p id="docDzPlaceholder" class="doc-dz-text">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#8a919a" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="display:block;margin:0 auto 6px;">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="12" y1="18" x2="12" y2="12"/>
            <polyline points="9 15 12 12 15 15"/>
        </svg>
        Drag &amp; drop a document here (PDF, Word, Excel…), or <u>click to browse</u>
    </p>

    <div id="docDzPreview" class="doc-dz-preview d-none">
        <div class="doc-dz-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0A3161" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
        </div>
        <div>
            <span id="docDzLabel" class="doc-dz-label"></span>
            <a id="docDzViewBtn" href="#" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary me-1 d-none"
                onclick="event.stopPropagation();">View</a>
            <button type="button" id="docDzChangeBtn" class="btn btn-sm btn-outline-primary me-1">Change</button>
            <button type="button" id="docDzRemoveBtn" class="btn btn-sm btn-outline-danger d-none">Remove</button>
        </div>
    </div>
</div>

<script>
    (function() {
        const existingDocUrl = @json($currentDocUrl);
        const input = document.getElementById('seller_financial_documents');
        const dz = document.getElementById('docDropzone');
        const placeholder = document.getElementById('docDzPlaceholder');
        const preview = document.getElementById('docDzPreview');
        const label = document.getElementById('docDzLabel');
        const viewBtn = document.getElementById('docDzViewBtn');
        const removeBtn = document.getElementById('docDzRemoveBtn');

        function formatSize(bytes) {
            if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
            if (bytes >= 1024) return Math.round(bytes / 1024) + ' KB';
            return bytes + ' B';
        }

        function showPreview(text, isNew, viewUrl) {
            label.textContent = text;
            placeholder.classList.add('d-none');
            preview.classList.remove('d-none');
            removeBtn.classList.toggle('d-none', !isNew);
            if (viewUrl) {
                viewBtn.href = viewUrl;
                viewBtn.classList.remove('d-none');
            } else {
                viewBtn.classList.add('d-none');
            }
        }

        function resetToInitial() {
            input.value = '';
            if (existingDocUrl) {
                showPreview('Current document', false, existingDocUrl);
            } else {
                preview.classList.add('d-none');
                placeholder.classList.remove('d-none');
            }
        }

        function handleFile(file) {
            if (!file) return;
            const text = file.name + ' (' + formatSize(file.size) + ')' +
                (existingDocUrl ? ' — will replace current document' : '');
            showPreview(text, true, null);
        }

        dz.addEventListener('click', function(e) {
            if (e.target.closest('#docDzRemoveBtn') || e.target.closest('#docDzViewBtn')) return;
            input.click();
        });

        input.addEventListener('click', function(e) { e.stopPropagation(); });

        input.addEventListener('change', function() {
            handleFile(this.files[0]);
        });

        ['dragover', 'dragenter'].forEach(function(ev) {
            dz.addEventListener(ev, function(e) {
                e.preventDefault();
                dz.classList.add('doc-dragover');
            });
        });

        ['dragleave', 'dragend'].forEach(function(ev) {
            dz.addEventListener(ev, function(e) {
                e.preventDefault();
                dz.classList.remove('doc-dragover');
            });
        });

        dz.addEventListener('drop', function(e) {
            e.preventDefault();
            dz.classList.remove('doc-dragover');
            const files = e.dataTransfer ? e.dataTransfer.files : [];
            if (files.length) {
                const dt = new DataTransfer();
                dt.items.add(files[0]); // single document only
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
