@extends('portal.layout.app')

@section('title') Blogs @endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Card Component -->
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="mb-0">Blogs</h5>
            <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('portal.blog-categories.index') }}" class="btn btn-outline-secondary btn-sm">Blog categories</a>
            <a href="{{ route('portal.blog.add','jodit') }}" class="btn btn-success btn-sm">Add Blog</a>
            </div>
        </div>
        <div class="card-body">
           

            <div class="table-responsive">
 
                <table id="blog_table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Banner</th>
                            <th>Content</th>
                            <th>Publish Date</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="photoModalLabel">Banner Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalPhoto" src="" alt="Blog Banner" class="img-fluid" style="max-width: 100%; max-height: 80vh; object-fit: contain;" />
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script type="text/javascript">
$(function () {
    var table = $('#blog_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('portal.blog.list') }}",
            data: function (d) {
                d.status = $('#statusFilter').val();  // pass filter value
            }
        },
        responsive: true,
        columns: [
            { data: null, render: (data, type, row, meta) => meta.row + 1, orderable: false },
            { data: 'title' },
            { data: 'category' },
            {
                data: 'banner',
                render: data => data
                    ? `<a href="#" class="view-photo" data-photo="${data}"><i class="fas fa-image"></i></a>`
                    : 'NA'
            },
            {
                data: 'content',
                render: function (data) {
                    const plainText = $('<div>').html(data).text().trim();
                    const words = plainText.match(/\b(\w+)\b/g);
                    let truncated = words ? words.slice(0, 3).join(' ') : '';
                    if (words && words.length > 5) truncated += '...';
                    return truncated;
                },
                orderable: false,
            },
            { data: 'active_date' },
            { data: 'is_active' },
            { data: 'created_at' },
            { data: 'action', orderable: false },
        ],
        order: [[5, 'desc']],
        initComplete: function () {
            // insert dropdown inside the search div
            let $filter = $('#blog_table_filter');

            const dropdown = `
                <select id="statusFilter" class="form-select form-select-sm" style="width:auto; display:inline-block; margin-right:10px;">
                    <option value="">All Status</option>
                    <option value="0">Draft</option>
                    <option value="1">Published</option>
                    <option value="2">Scheduled</option>
                </select>
            `;

            // prepend the dropdown before the Search label
            $filter.prepend(dropdown);

            // attach change event
            $('#statusFilter').on('change', function () {
                table.ajax.reload();
            });
        }
    });

    // View photo modal
    $('#blog_table tbody').on('click', 'a.view-photo', function (e) {
        e.preventDefault();
        $('#modalPhoto').attr('src', $(this).data('photo'));
        $('#photoModal').modal('show');
    });
});




document.getElementById('modalForm').addEventListener('submit', function(e) {
        const activeDateInput = document.getElementById('active_date');
        if (activeDateInput.value) {
            const selectedDate = new Date(activeDateInput.value);
            const today = new Date();
            today.setHours(0,0,0,0);
            if (selectedDate < today) {
                e.preventDefault();
                alert('Please select today or a future date.');
                activeDateInput.focus();
            }
        }
    });


document.getElementById('editForm').addEventListener('submit', function(e) {
        const activeDateInput = document.getElementById('active_date');
        if (activeDateInput.value) {
            const selectedDate = new Date(activeDateInput.value);
            const today = new Date();
            today.setHours(0,0,0,0);
            if (selectedDate < today) {
                e.preventDefault();
                alert('Please select today or a future date.');
                activeDateInput.focus();
            }
        }
    });

</script>
@endsection
