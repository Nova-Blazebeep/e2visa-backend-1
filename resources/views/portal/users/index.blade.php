@extends('portal.layout.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <!-- Card -->
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="mb-0">Users List</h5>
                <div>
                    <button class="btn btn-success btn-sm" id="addButton"
                        data-url="{{ route('portal.users.create') }}">Add</button>
                </div>
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="table table-bordered" id="table1" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Badge</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
       $(function() {
    var datatable = $('#table1').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('portal.users.list') }}",
            data: function (d) {
                d.role_id = $('#roleFilter').val(); // send role filter value
            }
        },
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'email' },
            { data: 'role' },
            { data: 'role_badge' },
            {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'nowrap-actions',
                        render: function(data, type, row) {
                            return `
                            <button class="btn btn-primary btn-sm viewDetailBtn m-1" data-url="/users/details/${row.id}">View Detail</button>
                            <button class="btn btn-warning btn-sm editBtn m-1" data-url="/users/edit/${row.id}">Edit</button>
                            <button class="btn btn-danger btn-sm deleteBtn m-1" data-url="/users/delete/${row.id}">Delete</button>
                        `;
                        }
            }
        ],
        order: [[0, 'desc']],
        initComplete: function () {
            let $filter = $('#table1_filter');

            const roleDropdown = `
                <select id="roleFilter" class="form-select form-select-sm" 
                    style="width:150px; display:inline-block; margin-right:10px;">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
            `;

            // prepend dropdown before search input
            $filter.prepend(roleDropdown);

            $('#roleFilter').on('change', function () {
                datatable.ajax.reload();
            });
        }
    });

    // Setup CSRF headers for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        }
    });

    // Add button click
    $('#addButton').on('click', function() {
        let url = $(this).data('url');
        $.get(url, function(res) {
            $('#ActionModal .modal-body').html(res);
            $('#ActionModal').modal('show');
        });
    });

    // Edit button click
    $(document).on('click', '.editBtn', function() {
        let url = $(this).data('url');
        $.get(url, function(res) {
            $('#ActionModal .modal-body').html(res);
            $('#ActionModal').modal('show');
        });
    });

    // Form submit
    $(document).on('submit', '#usermodalForm', function(e) {
        e.preventDefault();
        let form = $(this);
        let url = form.attr('action');
        let method = form.attr('method') || 'POST';
        let formData = new FormData(form[0]);
        let submitBtn = form.find('#submitBtn');
        let spinner = submitBtn.find('.spinner-border');

        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');

        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();

        $.ajax({
            url: url,
            method: method,
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#ActionModal').modal('hide');
                Swal.fire('Success', response.message, 'success');
                datatable.ajax.reload(null, false);
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let allMessages = [];

                    for (let key in errors) {
                        let input = form.find(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        if (input.next('.invalid-feedback').length === 0) {
                            input.after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                        }
                        allMessages.push(errors[key][0]);
                    }

                    if (allMessages.length) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: allMessages.join('\n'),
                            timer: 4000
                        });
                    }
                } else {
                    let errorMessage = 'Something went wrong';
                    if (xhr.responseJSON?.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire('Error', errorMessage, 'error');
                }
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });

    // Delete button click
    $(document).on('click', '.deleteBtn', function() {
        let url = $(this).data('url');
        Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'post',
                    success: function(res) {
                        Swal.fire('Deleted!', res.message, 'success');
                        datatable.ajax.reload(null, false);
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to delete user.', 'error');
                    }
                });
            }
        });
    });
});

    </script>
@endsection
