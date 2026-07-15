@extends('portal.layout.app')

@section('content')
	<div class="container-xxl flex-grow-1 container-p-y">
	<div class="row">
        <!-- Image Upload -->
        <div class="col-md-12 mb-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" class="form-control" id="image" name="image" data-aspect-ratio="3">
            <img id="image-preview" class="mt-2" width="200" style="display: none; height: auto; width: 100%;">
        </div>
        @if(isset($record->image))
        <img src="{{$record->image}}" class="img-fluid existing-image" style="width: 100%px; height: auto; object-fit:contain;">
        @endif
        <!-- Hidden input for cropped image -->
        <input type="hidden" id="cropped_image" name="cropped_image">
    </div>
		<!-- Text Editor Section -->
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0">{{ $record->title }}</h5>
			</div>
			<div class="card-body">
				<textarea class="form-control summernote" name="description">{!! $record->content ?? '' !!}</textarea>
				<button class="btn btn-primary btn-sm mt-3" id="saveChanges">Save Changes</button>
			</div>
		</div>
	</div>

	<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>
	<script>
		$(document).ready(function () {
			// Initialize Summernote for all elements with class 'summernote'
			$('.summernote').summernote({
				placeholder: 'Type your content here...',
				tabsize: 2,
				minHeight: 300,
				maxHeight: 600
			});

			// Save Changes Button Click Event
			$('#saveChanges').on('click', function () {
				let content = $('.summernote').summernote('code');

				$.ajax({
					url: "{{ route('pages.update', $record->slug) }}", // Correctly pass the slug
					method: "POST",
					headers: {
						'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
					},
					data: {
						content: content,
						image: $('#cropped_image').val(),
						slug: "{{ $record->slug }}",
						existing_image:"{{ $record->image  }}",
					},
					success: function (response) {
						if (response.success) {
							Swal.fire('Updated!', 'Updated successfully', 'success');
						} else {
							Swal.fire('Error!', 'Failed to update data.', 'error');
						}
					},
					error: function (xhr) {
						console.error(xhr.responseText);
						Swal.fire('Error!', 'Something went wrong. Try again.', 'error');
					}
				});
			});
		});
	</script>
	<script>
		$(document).ready(function () {
			console.log('Document is ready');
			initCropperModal(aspecRation=21/9); // Initialize the cropper modal
		});
	</script>
@endsection