@extends('portal.layout.app')

@section('title') Manage Forums @endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="mb-0">Manage Forums</h5>
        </div>
        <div class="card-body">

            {{-- User filter dropdown --}}
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Filter by User</label>
                    <select id="userFilter" class="form-select form-select-sm">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table id="forums_table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Content Preview</th>
                            <th>Created By</th>
                            <th>Comments</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function () {
    var table = $('#forums_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('portal.forums.list') }}",
            data: function (d) {
                d.user_id = $('#userFilter').val();
            }
        },
        responsive: true,
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1, orderable: false },
            { data: 'title' },
            { data: 'content', orderable: false },
            { data: 'created_by' },
            { data: 'comments', orderable: false },
            { data: 'created_at' },
            { data: 'action', orderable: false },
        ],
        order: [[5, 'desc']],
    });

    // Reload table when user filter changes
    $('#userFilter').on('change', function () {
        table.ajax.reload();
    });
});

function delete_confirmation(route) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This forum and all its comments will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = route;
        }
    });
}
</script>
@endsection
