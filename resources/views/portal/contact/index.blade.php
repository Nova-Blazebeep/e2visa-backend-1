@extends('portal.layout.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Page Header --}}
        <div class="page-header">
            <div>
                <h4 class="page-title">Contact Messages</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Contact</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Contact List</h5>
                <div class="card-header-toolbar">
                    <button class="btn btn-outline-danger btn-sm" id="deleteSelectedBtn">
                        <i class="ti ti-trash me-1"></i> Delete Selected
                    </button>
                </div>
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="table table-bordered" id="categoryTable" width="100%">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAllCheckbox"></th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Contact Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detailsModalBody">
                    <dl class="row">
                        <dt class="col-sm-4">Name:</dt>
                        <dd class="col-sm-8" id="detailName"></dd>

                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8" id="detailEmail"></dd>

                        <dt class="col-sm-4">Subject:</dt>
                        <dd class="col-sm-8" id="detailSubject"></dd>

                        <dt class="col-sm-4">Message:</dt>
                        <dd class="col-sm-8" id="detailMessage"></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            let categoryTable = $('#categoryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('contact.list') }}",
                columns: [{
                        data: 'checkbox',
                        orderable: false,
                        searchable: false,
                        width: '30px',
                        className: 'text-center'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'subject'
                    },
                    {
                        data: 'message'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [1, 'desc']
                ] // Sort by 'name' initially
            });

            // Handle select all checkbox
            $('#selectAllCheckbox').on('change', function() {
                $('.rowCheckbox').prop('checked', this.checked);
            });

            // When any checkbox is unchecked, uncheck "selectAllCheckbox"
            $('#categoryTable tbody').on('change', '.rowCheckbox', function() {
                if (!this.checked) {
                    $('#selectAllCheckbox').prop('checked', false);
                } else {
                    // If all checkboxes are checked, check the selectAllCheckbox
                    if ($('.rowCheckbox:checked').length === $('.rowCheckbox').length) {
                        $('#selectAllCheckbox').prop('checked', true);
                    }
                }
            });

            // View Details button
            $('#categoryTable').on('click', '.viewDetailsBtn', function() {
                let url = $(this).data('url');
                let modal = new bootstrap.Modal(document.getElementById('detailsModal'));
                modal.show();
                $.get(url, function(data) {
                    $('#detailName').text(data.name);
                    $('#detailEmail').text(data.email);
                    $('#detailSubject').text(data.subject);
                    $('#detailMessage').html(data.message);
                }).fail(function() {
                    $('#detailsModalBody').html(
                        '<div class="alert alert-danger">Failed to load details.</div>');
                });
            });

            // Delete Contact button
            $('#categoryTable').on('click', '.deleteContactBtn', function() {
                let url = $(this).data('url');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will permanently delete the contact.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(url, {
                            _token: '{{ csrf_token() }}'
                        }, function(res) {
                            categoryTable.ajax.reload(null, false);
                            Swal.fire('Deleted!', res.message, 'success');
                        }).fail(function(xhr) {
                            Swal.fire('Error', xhr.responseJSON.message ||
                                'Something went wrong', 'error');
                        });
                    }
                });
            });

            // Delete Selected button
            $('#deleteSelectedBtn').on('click', function() {
                let selectedIds = $('.rowCheckbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) {
                    Swal.fire('No Selection', 'Please select at least one contact.', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will delete selected contacts.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete them!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post("{{ route('contact.deleteMultiple') }}", {
                            _token: '{{ csrf_token() }}',
                            ids: selectedIds
                        }, function(res) {
                            categoryTable.ajax.reload(null, false);
                            Swal.fire('Deleted!', res.message, 'success');
                        }).fail(function(xhr) {
                            Swal.fire('Error', xhr.responseJSON.message ||
                                'Something went wrong',
                                'error');
                        });
                    }
                });
            });
        });
    </script>
@endsection
