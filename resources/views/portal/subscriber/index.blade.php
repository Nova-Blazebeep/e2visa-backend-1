@extends('portal.layout.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Page Header --}}
        <div class="page-header">
            <div>
                <h4 class="page-title">Subscribers</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Subscribers</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Subscribers List</h5>
                <div class="card-header-toolbar">
                    <button class="btn btn-outline-danger btn-sm" id="bulkDeleteBtn">
                        <i class="ti ti-trash me-1"></i> Delete Selected
                    </button>
                </div>
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="table table-bordered" id="subscriberTable" width="100%">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="checkAll"></th>
                            <th>Email</th>
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
            let subscriberTable = $('#subscriberTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('subscriber.list') }}",
                columns: [
                    { data: 'checkbox', orderable: false, searchable: false },
                    { data: 'email' },
                    { data: 'action', orderable: false, searchable: false }
                ],
                order: [[1, 'desc']]
            });

            $(document).on('change', '#checkAll', function() {
                $('.subscriberCheckbox').prop('checked', this.checked);
            });

            $('#bulkDeleteBtn').on('click', function() {
                let ids = $('.subscriberCheckbox:checked').map(function() {
                    return $(this).val();
                }).get();

                if (ids.length === 0) {
                    Swal.fire('No Selection', 'Please select at least one subscriber to delete.', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Selected subscribers will be deleted.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete them!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('subscriber.deleteMultiple') }}",
                            method: "POST",
                            data: {
                                _token: '{{ csrf_token() }}',
                                ids: ids
                            },
                            success: function(res) {
                                subscriberTable.ajax.reload(null, false);
                                Swal.fire('Deleted!', res.message, 'success');
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON.message || 'Something went wrong', 'error');
                            }
                        });
                    }
                });
            });

            $('#subscriberTable').on('click', '.deleteSubscriberBtn', function() {
                let url = $(this).data('url');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will permanently delete the subscriber.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(url, {
                            _token: '{{ csrf_token() }}'
                        }, function(res) {
                            subscriberTable.ajax.reload(null, false);
                            Swal.fire('Deleted!', res.message, 'success');
                        }).fail(function(xhr) {
                            Swal.fire('Error', xhr.responseJSON.message || 'Something went wrong', 'error');
                        });
                    }
                });
            });
        });
    </script>
@endsection
