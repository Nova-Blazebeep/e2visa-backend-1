@extends('portal.layout.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- Page Header --}}
        <div class="page-header">
            <div>
                <h4 class="page-title">Manage Real-Estate</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Real-Estate Listings</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('real-estate.show.form') }}" class="btn btn-primary">
                <i class="ti ti-plus me-1"></i> Add Real-Estate
            </a>
        </div>

        <!-- DataTable with Buttons -->
        <div class="card">
            <div class="card-datatable table-responsive pt-0">
                <table class="table table-bordered" id="table1" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Real Estate Type</th>
                            <th>Status</th>
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
    <!-- DataTables RowReorder CSS & JS -->
    <script>
document.addEventListener('DOMContentLoaded', function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    });

    let datatable = $('#table1').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('real-estate.list') }}",
            type: 'GET',
            data: function(d) {
                d.is_publish = $('#statusFilter').val();
                d.listing_type = $('#listingTypeFilter').val(); // new filter
            }
        },
        columns: [
            { data: 'id' },
            { data: 'business_name' },
            { data: 'listing_type' },
            { data: 'is_published' },
            { data: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        initComplete: function() {
            let $filter = $('#table1_filter');

            // Listing type dropdown
            let listingTypeDropdown = `
                <select id="listingTypeFilter" class="form-select form-select-sm"
                    style="width:150px; display:inline-block; margin-right:10px;">
                    <option value="">All Types</option>

                    @foreach ($listing_types->skip(2) as $type)
                        <option value="{{ $type->id }}">{{ $type->listing_type }}</option>
                    @endforeach
                </select>
            `;

            // Status dropdown
            let statusDropdown = `
                <select id="statusFilter" class="form-select form-select-sm" style="width:auto; display:inline-block; margin-right:10px;">
                    <option value="">All Status</option>
                    <option value="0">Draft</option>
                    <option value="1">Published</option>
                </select>
            `;

            // Append both before search
            $filter.prepend(statusDropdown);
            $filter.prepend(listingTypeDropdown);

            // Bind events
            $('#listingTypeFilter, #statusFilter').on('change', function() {
                datatable.ajax.reload();
            });
        }
    });

    // Delete button logic
    $(document).on('click', '.deleteBtn', function() {
        const id = $(this).data('id');
        if (!id) {
            Swal.fire('Error', 'Invalid Data', 'error');
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/business/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire('Deleted!', response.message || 'Deleted.', 'success');
                        datatable.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Failed to delete', 'error');
                        console.error('Error deleting:', xhr.responseText);
                    }
                });
            }
        });
    });

    // Session notifications
    @if (session(SUCCESS_CODE))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '{{ session(SUCCESS_CODE) }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    @elseif (session(FAILURE_CODE))
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: '{{ session(FAILURE_CODE) }}',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    @endif
});
</script>

@endsection
