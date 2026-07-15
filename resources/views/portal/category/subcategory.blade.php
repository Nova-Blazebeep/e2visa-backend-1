@extends('portal.layout.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="mb-0">Subcategories List</h5>
                <button class="btn btn-success btn-sm" id="addCategoryButton" data-title="Add Subcategory"
                    data-url="{{ route('portal.subcategories.create') }}">
                    Add Subcategory
                </button>
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="table table-bordered" id="categoryTable" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category</th>
                            <th>Subcategory</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="categoryForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="categoryModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="categoryModalBody">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="categorySaveBtn">Save</button>
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
            let categoryTable = $('#categoryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('portal.subcategories.list') }}",
                responsive: true,
                stateSave: true,
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'category'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            });

            function openCategoryModal(url, title) {
                $('#categoryModalLabel').text(title);
                $.get(url, function(html) {
                    $('#categoryModalBody').html(html);
                    let actionUrl = url.includes('/edit') ?
                        url.replace('/edit', '/store-or-update') :
                        "{{ route('portal.subcategories.storeOrUpdate') }}";

                    $('#categoryForm').attr('action', actionUrl);
                    $('#categoryModal').modal('show');
                });
            }

            $('#addCategoryButton').click(function() {
                openCategoryModal($(this).data('url'), $(this).data('title'));
            });

            $('#categoryTable').on('click', '.editCategoryBtn', function() {
                openCategoryModal($(this).data('url'), $(this).data('title'));
            });

            $('#categoryModal').on('submit', '#categoryForm', function(e) {
                e.preventDefault();
                var modalEl = document.getElementById('categoryModal');
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
                        categoryTable.ajax.reload(null, false);
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

            $('#categoryTable').on('click', '.deleteCategoryBtn', function() {
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
                            categoryTable.ajax.reload(null, false);
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
