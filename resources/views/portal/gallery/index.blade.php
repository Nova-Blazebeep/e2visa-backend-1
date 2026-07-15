@extends('portal.layout.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="mb-0">Media Gallery</h5>
                <div class="d-flex gap-1 flex-wrap justify-center">
                    <button class="btn btn-success btn-sm" data-url="/media/gallery/create" data-title="Add New" id="addButton">Add</button>
                </div>
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="table table-bordered" id="table1" width="100%">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>URL</th>
                        <th>Image</th>
                        <th>Created at</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>


<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" alt="Image Preview" class="img-fluid">
            </div>
        </div>
    </div>
</div>




            </div>
        </div>
    </div>
@endsection

@section('script')
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css">
    <script type="text/javascript" charset="utf8"
            src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>
    <script type="text/javascript">
        var datatable;
        $(function () {


datatable = $('#table1').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ route('media_gallery.list') }}",
        data: function (d) {}
    },
    responsive: true,
    stateSave: true,
    columns: [
        { data: 'id' },
        { data: 'name' },
        {
            data: 'url',
            orderable: false,
            searchable: false,
            render: function (data, type, row) {
                return `
                    <button class="btn btn-sm btn-outline-primary copyUrlBtn" data-url="${data}" title="Copy URL">
                        <i class="fas fa-copy"></i>
                    </button>`;
            }
        },
        {
            data: 'image',
    orderable: false,
    searchable: false,
    render: function (data, type, row) {
        return `
            <button class="btn btn-sm btn-outline-secondary viewImageBtn" data-image="${data}" title="View Image">
                <i class="fas fa-image"></i>
            </button>`;
    }
        },
        { data: 'updated_at' },
        {
            data: 'action',
            orderable: false,
            searchable: false,
            className: 'nowrap-actions',
            render: function (data, type, row) {
                const editButton = `
                    <button class="m-1 btn btn-warning btn-sm editBtn" data-title="Edit Media Gallery" data-url="/media/gallery/edit/${row.id}">
                        Edit
                    </button>`;
                const deleteButton = `
                    <button class="m-1 btn btn-danger btn-sm deleteBtn" data-url="/media/gallery/delete/${row.id}">
                        Delete
                    </button>`;
                const viewDetailButton = `
                    <button class="m-1 d-none btn btn-primary btn-sm viewDetailBtn" data-url="/media/gallery/details/${row.id}">
                        View Detail
                    </button>`;

                return `${viewDetailButton}${editButton}${deleteButton}`;
            }
        }
    ],
    columnDefs: [
        { 'targets': [3], 'orderable': false }
    ],
    order: [[0, 'desc']]
});

// Copy URL to clipboard functionality
$(document).on('click', '.copyUrlBtn', function () {
    const url = $(this).data('url');
    navigator.clipboard.writeText(url).then(() => {
       // alert('URL copied to clipboard: ' + url);
       toast_success('Url Copied');
    }).catch(err => {
       // console.error('Error copying URL:', err);
       toast_success('Error in copying url');
    });
});

// Show image in modal functionality
$(document).on('click', '.viewImageBtn', function () {
    const imageUrl = $(this).data('image');
    $('#imageModal img').attr('src', imageUrl);
    $('#imageModal').modal('show');
});


             });

             $(document).on('submit', '#galleryForm', function (e) {
                e.preventDefault();
                var form = $(this);
                var url = form.attr('action');
                var formData = new FormData(this);

                $.ajax({
                    type: 'POST',
                    url: url,
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        $('#ActionModal').modal('hide');
                        toast_success(response.message);
                        datatable.ajax.reload(null, false);
                    },
                    error: function (xhr) {
                        let errorMsg = 'Something went wrong';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        toast_error(errorMsg);
                    }
                });
            });

    </script>
@endsection
