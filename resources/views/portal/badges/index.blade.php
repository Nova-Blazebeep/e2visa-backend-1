@extends('portal.layout.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="mb-0">Badges List</h5>
                <button class="btn btn-success btn-sm" id="addBadgeButton" data-title="Add Badge"
                    data-url="{{ route('portal.badges.create') }}">
                    Add Badge
                </button>
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="table table-bordered" id="badgesTable" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Badge Title</th>
                            <th>Icon</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

    </div>

    <div class="modal fade" id="badgeModal" tabindex="-1" aria-labelledby="badgeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="badgeForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="badgeModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="badgeModalBody">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="badgeSaveBtn">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            let badgesTable = $('#badgesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('portal.badges.list') }}",
                responsive: true,
                stateSave: true,
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'role_name'
                    },
                    {
                        data: 'icon',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-warning btn-sm editBadgeBtn" data-url="/badges/${row.id}/edit" data-title="Edit Badge">Edit</button>
                                <button class="btn btn-danger btn-sm deleteBadgeBtn" data-url="/badges/${row.id}/delete">Delete</button>
                            `;
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            });

            function openBadgeModal(url, title) {
                $('#badgeModalLabel').text(title);
                $.get(url, function(html) {
                    $('#badgeModalBody').html(html);
                    let actionUrl = url.includes('/edit') ? url.replace('/edit', '/update') :
                        "{{ route('portal.badges.store') }}";
                    $('#badgeForm').attr('action', actionUrl);
                    $('#badgeModal').modal('show');
                });
            }

            $('#addBadgeButton').click(function() {
                openBadgeModal($(this).data('url'), $(this).data('title'));
            });

            $('#badgesTable').on('click', '.editBadgeBtn', function() {
                openBadgeModal($(this).data('url'), $(this).data('title'));
            });

            $('#badgeModal').on('submit', '#badgeForm', function(e) {
                e.preventDefault();
                var modalEl = document.getElementById('badgeModal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();

                let form = this;
                let formData = new FormData(form);
                let actionUrl = $(form).attr('action');

                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        badgesTable.ajax.reload(null, false);
                        Swal.fire('Success', res.message, 'success');
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            let errMsg = '';
                            for (const key in errors) {
                                errMsg += errors[key].join(', ') + '\n';
                            }
                            Swal.fire('Error', errMsg, 'error');
                        } else {
                            Swal.fire('Error', 'Something went wrong', 'error');
                        }
                    }
                });
            });

            $('#badgesTable').on('click', '.deleteBadgeBtn', function() {
                let url = $(this).data('url');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.post(url, {
                            _token: '{{ csrf_token() }}'
                        }, function(res) {
                            badgesTable.ajax.reload(null, false);
                            Swal.fire('Deleted!', res.message, 'success');
                        }).fail(function() {
                            Swal.fire('Error', 'Something went wrong', 'error');
                        });
                    }
                });
            });
        });
    </script>
@endsection
