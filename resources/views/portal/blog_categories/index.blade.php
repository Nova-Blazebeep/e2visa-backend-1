@extends('portal.layout.app')

@section('title') Blog categories @endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="mb-0">Blog categories</h5>
                <button type="button" class="btn btn-success btn-sm" id="addBlogCategoryButton" data-title="Add category"
                    data-url="{{ route('portal.blog-categories.create') }}">
                    Add category
                </button>
            </div>
            <div class="card-datatable table-responsive pt-0">
                <table class="table table-bordered" id="blogCategoriesTable" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Blogs</th>
                            <th>Created at</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="blogCategoryModal" tabindex="-1" aria-labelledby="blogCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="blogCategoryForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="blogCategoryModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="blogCategoryModalBody"></div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="blogCategorySaveBtn">Save</button>
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
            let table = $('#blogCategoriesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('portal.blog-categories.list') }}",
                responsive: true,
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'blogs_count'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            });

            function openBlogCategoryModal(url, title) {
                $('#blogCategoryModalLabel').text(title);
                $.get(url, function(html) {
                    $('#blogCategoryModalBody').html(html);
                    let actionUrl = url.includes('/edit') ?
                        url.replace('/edit', '/update') :
                        "{{ route('portal.blog-categories.store') }}";
                    $('#blogCategoryForm').attr('action', actionUrl);
                    $('#blogCategoryModal').modal('show');
                });
            }

            $('#addBlogCategoryButton').on('click', function() {
                openBlogCategoryModal($(this).data('url'), $(this).data('title'));
            });

            $('#blogCategoriesTable').on('click', '.editBlogCategoryBtn', function() {
                openBlogCategoryModal($(this).data('url'), $(this).data('title'));
            });

            $('#blogCategoryModal').on('submit', '#blogCategoryForm', function(e) {
                e.preventDefault();
                let form = this;
                let actionUrl = $(form).attr('action');
                let modalEl = document.getElementById('blogCategoryModal');
                let modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }

                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: $(form).serialize(),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        table.ajax.reload(null, false);
                        Swal.fire('Success', res.message, 'success');
                    },
                    error: function(xhr) {
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = xhr.responseJSON.errors;
                            let errMsg = '';
                            for (const key in errors) {
                                errMsg += errors[key].join(', ') + '\n';
                            }
                            Swal.fire('Error', errMsg, 'error');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            Swal.fire('Error', xhr.responseJSON.message, 'error');
                        } else {
                            Swal.fire('Error', 'Something went wrong', 'error');
                        }
                    }
                });
            });

            $('#blogCategoriesTable').on('click', '.deleteBlogCategoryBtn', function() {
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
                            table.ajax.reload(null, false);
                            Swal.fire('Done', res.message, 'success');
                        }).fail(function(xhr) {
                            let msg = (xhr.responseJSON && xhr.responseJSON.message) ?
                                xhr.responseJSON.message :
                                'Something went wrong';
                            Swal.fire('Error', msg, 'error');
                        });
                    }
                });
            });
        });
    </script>
@endsection
