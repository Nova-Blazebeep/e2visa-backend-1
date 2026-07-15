@extends('portal.layout.app')

@section('title') Manage Article Comments @endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="mb-0">Article Comments</h5>
            <a href="{{ route('portal.blog.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="ti ti-arrow-left me-1"></i> Back to Articles
            </a>
        </div>
        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($comments->isEmpty())
                <p class="text-muted text-center py-4">No comments found.</p>
            @else
                <div class="table-responsive">
                    <table id="comments_table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Article</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Comment</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comments as $i => $comment)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>
                                        <span class="fw-semibold" style="max-width:180px; display:inline-block; word-break:break-word;">
                                            {{ $comment->blog ? $comment->blog->title : '—' }}
                                        </span>
                                    </td>
                                    <td>{{ $comment->name }}</td>
                                    <td>
                                        <a href="mailto:{{ $comment->email }}" class="text-muted small">
                                            {{ $comment->email }}
                                        </a>
                                    </td>
                                    <td style="max-width:300px; word-break:break-word; white-space:pre-wrap;">{{ $comment->comment }}</td>
                                    <td class="text-nowrap">{{ $comment->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="#"
                                           onclick="confirmDelete('{{ route('portal.blog.comments.delete', $comment->id) }}')"
                                           class="text-danger"
                                           title="Delete Comment">
                                            <i class="ti ti-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection

@section('script')
<script>
function confirmDelete(url) {
    Swal.fire({
        title: 'Delete this comment?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it',
    }).then(function(result) {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

$(function () {
    $('#comments_table').DataTable({
        order: [[5, 'desc']],
        pageLength: 25,
        columnDefs: [
            { orderable: false, targets: [6] }
        ]
    });
});
</script>
@endsection
