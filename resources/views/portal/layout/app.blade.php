

<!DOCTYPE html>

<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr"
	data-theme="theme-default" data-assets-path="{{ url('/portal') }}/assets/" data-template="vertical-menu-template">

<head>
	<meta charset="utf-8" />
	<meta name="viewport"
		content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
	<title>{{ env('APP_NAME') }} | Dashboard</title>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="description" content="" />
	@include('portal.layout.partial.css-links')
	<style>
		.note-editor.fullscreen {
			background-color: #ffffff !important;
			/* Set to white */
		}

		.note-editing-area {
			background-color: #ffffff !important;
			/* Editor's text area */
		}

		.note-editor.note-frame.fullscreen {
			z-index: 1090 !important;
			/* Ensure it has a higher z-index */
		}

		.fullscreen-loader {
			position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: rgba(255, 255, 255, 0.8);
			z-index: 9999;
		}

		.note-group-select-from-files {
			display: none !important;
		}


		.loader-spinner {
			border: 5px solid #f3f3f3;
			border-radius: 50%;
			border-top: 5px solid #3498db;
			width: 50px;
			height: 50px;
			position: fixed;
			top: 50%;
			left: 50%;
			animation: spin 1s linear infinite;
		}

		@keyframes spin {
			0% {
				transform: rotate(0deg);
			}

			100% {
				transform: rotate(360deg);
			}
		}

		#fullscreen-loader {
			display: none;
			/* other styles */
		}
	</style>
</head>

<body>
	<!-- Layout wrapper -->
	<div class="layout-wrapper layout-content-navbar">
		<div class="layout-container">
			<div id="fullscreen-loader" class="fullscreen-loader">
				<div class="loader-content">
					<div class="loader-spinner"></div>
					<!-- Optional: Add loading text -->
					<!-- <div class="loader-text">Loading...</div> -->
				</div>
			</div>
			<div id="loader"
				style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(255,255,255,0.7); z-index:9999; text-align:center;">
				<div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%);">
					<div class="spinner-border text-primary" role="status">
						<span class="visually-hidden">Loading...</span>
					</div>
				</div>
			</div>
			<!-- Menu -->

			@include ('portal.layout.partial.sidebar')
			<!-- / Menu -->
			<!-- Layout container -->
			<div class="layout-page">
				<!-- Navbar -->
				@include ('portal.layout.partial.navbar')
				<!-- / Navbar -->
				<!-- Content wrapper -->
				<div class="content-wrapper">
					<!-- Content -->
					@yield('content')
					<!-- / Content -->
				</div>
				<!-- Modal to show details -->
				<div class="modal fade" id="DetailModal" tabindex="-1" aria-labelledby="DetailModalLabel"
					aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="DetailModalLabel">Details</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal"
									aria-label="Close"></button>
							</div>
							<div class="modal-body" id="detail-content">
								<!-- Content will be injected dynamically -->
							</div>
						</div>
					</div>
				</div>

				<div class="modal fade" id="ActionModal" tabindex="-1" aria-labelledby="ActionModalLabel"
					aria-hidden="true">
					<div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="ActionModalLabel"></h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal"
									aria-label="Close"></button>
							</div>
							<div class="modal-body" id="modal-content">
								<!-- Content will be injected dynamically -->
							</div>
						</div>
					</div>
				</div>

				<div class="modal fade" id="changePasswordModal" tabindex="-1"
					aria-labelledby="changePasswordModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<form id="changePasswordForm" method="POST">
								@csrf
								<div class="modal-header">
									<h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
									<button type="button" class="btn-close" data-bs-dismiss="modal"
										aria-label="Close"></button>
								</div>
								<div class="modal-body">
									<div class="mb-3">
										<label for="password" class="form-label">New Password</label>
										<input type="password" class="form-control" id="password" name="password"
											required>
									</div>
									<div class="mb-3">
										<label for="confirmPassword" class="form-label">Confirm Password</label>
										<input type="password" class="form-control" id="confirmPassword"
											name="confirmPassword" required>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary"
										data-bs-dismiss="modal">Close</button>
									<button type="submit" class="btn btn-primary">Change Password</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div id="cropModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static"
					data-keyboard="false">
					<div class="modal-dialog modal-md" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Crop Image</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body">
								<div class="img-container">
									<img id="crop-image" style="width: 100%;" />
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" id="crop-cancel"
									data-dismiss="modal">Cancel</button>
								<button type="button" class="btn btn-primary" id="crop-save">Crop & Upload</button>
							</div>
						</div>
					</div>
				</div>
				<!-- Content wrapper -->
			</div>
			<!-- / Layout page -->
		</div>
	</div>
	<!-- / Layout wrapper -->


	@include('portal.layout.partial.js-links')

	<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-lite.min.js"></script>
	<script>
		$(document).ready(function () {
			$('#fullscreen-loader').hide();

			$.ajaxSetup({
				beforeSend: function (xhr, settings) {
					$('#fullscreen-loader').fadeOut(100);
					if (settings.headers && settings.headers.Loader == true) {
						$('#fullscreen-loader').fadeIn(100);
					} else {
						$('#fullscreen-loader').fadeOut(100);
					}
				},
				complete: function () {
					$('#fullscreen-loader').fadeOut(100);
				}
			});

			// Hide loader on AJAX complete
			$(document).ajaxStop(function () {
				$('#fullscreen-loader').fadeOut(100);
			});

			// Optional: Hide loader on AJAX error
			$(document).ajaxError(function () {
				$('#fullscreen-loader').fadeOut(100);
			});
		});
	</script>
	<script>
		$('.summernote').summernote({
			placeholder: 'Type your content here...',
			tabsize: 2,
			height: null,
			toolbar: [
				['style', ['style']],
				['font', ['bold', 'underline', 'clear']],
				['color', ['color']],
				['para', ['ul', 'ol', 'paragraph']],
				['table', ['table']],
				['insert', ['link', 'picture', 'video']],
				['view', ['fullscreen', 'codeview', 'help']]
			]

		});

		$(document).on('click', '.viewDetailBtn', function (e) {
			e.preventDefault();
			let userId = $(this).data('id');
			let url = $(this).data('url');

			// Fetch details
			$.ajax({
				url: url,
				type: 'GET',
				headers: { 'Loader': true },
				success: function (response) {
					// Inject the response (HTML) into the modal body
					$('#detail-content').html(response);

					// Show the modal
					$('#DetailModal').modal('show');
				},
				error: function (xhr, status, error) {
					console.error("Error fetching details:", error);
				}
			});
		});
		$(document).on('click', '.deleteBtn', function (e) {
			e.preventDefault();
			let url = $(this).data('url');

			Swal.fire({
				title: 'Are you sure?',
				text: "You want to delete this record? This action cannot be undone.",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#111111',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Yes, delete it!'
			}).then((result) => {
				if (result.isConfirmed) {
					$.ajax({
						url: url,
						method: 'POST',
						headers: { 'Loader': true },
						data: {
							_token: '{{ csrf_token() }}',
						},
						success: function (response) {
							Swal.fire('Deleted!', 'Record has been deleted.',
								'success');
							datatable.ajax.reload(null, false); // Reload the table
						},
						error: function (xhr, status, error) {
							console.error("Error deleting record:", error);
							Swal.fire('Error!', 'Failed to delete record.', 'error');
						}
					});
				}
			});
		});

		// Function to handle opening the modal
		function openModal(title, url) {
			$('#ActionModalLabel').text(title); // Set the modal title
			$.ajax({
				url: url,
				type: 'GET',
				headers: { 'Loader': true },
				success: function (response) {
					$('#modal-content').html(response); // Inject form content into modal
					$('#ActionModal').modal('show'); // Show the modal
					initCropperModal();
				},
				error: function (xhr, status, error) {
					console.error(`Error loading ${title} form:`, error);
				}
			});
		}

		// Add button click handler
		$('#addButton').on('click', function (e) {
			e.preventDefault();
			let title = $(this).data('title');
			let url = $(this).data('url');
			openModal(title, url);
		});

		// Edit button click handler
		$(document).on('click', '.editBtn', function (e) {
			e.preventDefault();
			let title = $(this).data('title');
			let url = $(this).data('url');
			openModal(title, url);
		});


		// Handle change password button click
		$(document).on('click', '.changePasswordBtn', function () {
			const url = $(this).data('url');
			$('.confirm-password-error').remove();
			$('#changePasswordForm')[0].reset();
			$('#changePasswordModal').modal('show');
			$('#changePasswordForm').attr('action', url);
		});


		// Handle form submission via AJAX
		$(document).on('submit', '#changePasswordForm', function (e) {
			e.preventDefault();
			let formData = new FormData(this); // Use FormData to handle file uploads
			let actionUrl = $(this).attr('action'); // Get the form's action URL
			const password = $('#password').val();
			const confirmPassword = $('#confirmPassword').val();
			// Check if the passwords match
			if (password !== confirmPassword) {

				$('#confirmPassword').after('<span class="text-danger confirm-password-error">Passwords do not match.</span>');
				return; // Stop form submission
			}

			$.ajax({
				url: actionUrl,
				type: 'POST',
				headers: { 'Loader': true },
				data: formData,
				processData: false,  // Prevent jQuery from processing the data
				contentType: false,  // Prevent jQuery from setting contentType
				success: function (response) {
					Swal.fire('Success!', response.message, 'success');
					$('#ActionModal').modal('hide'); // Close the modal
					datatable.ajax.reload(null, false); // Reload the DataTable
				},
				error: function (xhr, status, error) {
					$('#changePasswordModal').modal('hide');
					if (xhr.status === 422) { // Validation error
						let errorMessages = xhr.responseJSON
							.errors; // Extract the 'errors' object
						let alertMessage = '';

						for (let field in errorMessages) {
							if (errorMessages.hasOwnProperty(field)) {
								alertMessage +=
									`${errorMessages[field].join(', ')}\n`; // Concatenate error messages
							}
						}

						Swal.fire('Validation Error!', alertMessage, 'error');
					} else {
						console.error("Error submitting form:", error);
						Swal.fire('Error!', 'Failed to save changes.', 'error');
					}
				}
			});
			$('#changePasswordModal').modal('hide');
		});

		// Handle form submission via AJAX
		$(document).on('submit', '#modalForm', function (e) {
			e.preventDefault();


			let formData = new FormData(this); // Use FormData to handle file uploads
			let actionUrl = $(this).attr('action'); // Get the form's action URL


			$.ajax({
				url: actionUrl,
				type: 'POST',
				headers: { 'Loader': true },
				data: formData,
				processData: false,  // Prevent jQuery from processing the data
				contentType: false,  // Prevent jQuery from setting contentType
				success: function (response) {
					Swal.fire('Success!', response.message, 'success');
					$('#fullscreen-loader').hide();
					 if (response.redirectURL) {
            window.location.href = response.redirectURL;
            return; 
        }
					$('#ActionModal').modal('hide');
					form[0].reset();  // Close the modal
					datatable.ajax.reload(null, false); // Reload the DataTable
				},
				error: function (xhr, status, error) {
					if (xhr.status === 422) { // Validation error
						let errorMessages = xhr.responseJSON
							.errors; // Extract the 'errors' object
						let alertMessage = '';

						for (let field in errorMessages) {
							if (errorMessages.hasOwnProperty(field)) {
								alertMessage +=
									`${errorMessages[field].join(', ')}\n`; // Concatenate error messages
							}
						}
						$('#ActionModal').modal('hide');
						Swal.fire('Validation Error!', alertMessage, 'error');
					} else {
						console.error("Error submitting form:", error);
						Swal.fire('Error!', 'Failed to save changes.', 'error');
					}
				}
			});
		});

	</script>
	@yield('script')
	<script>
		function initCropperModal() {
			let cropper;
			let selectedFile;
			let currentField = null;

			function bindCropperInput(inputId, previewId, hiddenInputId, existingImageClass) {
				$(inputId).off("change").on("change", function (event) {
					let files = event.target.files;
					if (files && files.length > 0) {
						selectedFile = files[0];
						currentField = { inputId, previewId, hiddenInputId, existingImageClass };

						let reader = new FileReader();
						reader.onload = function (e) {
							$("#crop-image").attr("src", e.target.result);
							let aspectRatio = $(inputId).data("aspect-ratio") || 1;
							$("#cropModal").data("aspect-ratio", aspectRatio);
							$("#cropModal").modal("show");
						};
						reader.readAsDataURL(selectedFile);
					}
				});
			}

			// Setup both image and thumbnail cropper triggers
			bindCropperInput("#image", "#image-preview", "#cropped_image", ".existing-image");
			bindCropperInput("#thumbnail", "#thumbnail-preview", "#cropped_thumbnail", ".existing-thumbnail");

			$("#cropModal").off("shown.bs.modal").on("shown.bs.modal", function () {
				let image = document.getElementById("crop-image");
				let aspectRatio = $(this).data("aspect-ratio") || 1;

				if (cropper) cropper.destroy();
				cropper = new Cropper(image, {
					aspectRatio: aspectRatio,
					viewMode: 2,
					autoCropArea: 1,
					responsive: true,
					imageSmoothingEnabled: true,
					imageSmoothingQuality: "high",
				});
			});

			$("#cropModal").off("hidden.bs.modal").on("hidden.bs.modal", function () {
				if (cropper) {
					cropper.destroy();
					cropper = null;
				}
				$("#crop-image").attr("src", "");
			});

			$(document).off("click", "#crop-cancel, .close").on("click", "#crop-cancel, .close", function () {
				$("#cropModal").modal("hide");
			});

			$(document).off("click", "#crop-save").on("click", "#crop-save", function () {
				if (!cropper || !currentField) return;

				let canvas = cropper.getCroppedCanvas({
					width: 1920,
					height: 1080,
					imageSmoothingEnabled: true,
					imageSmoothingQuality: "high",
				});

				canvas.toBlob(function (blob) {
					let formData = new FormData();
					formData.append("cropped_image", blob);
					formData.append("_token", "{{ csrf_token() }}");

					$.ajax({
						url: "{{ route('media_gallery.uploadCroppedImage') }}",
						type: "POST",
						data: formData,
						headers: { Loader: false },
						contentType: false,
						processData: false,
						success: function (response) {
							$('#path').val(response.path);
							$(currentField.previewId).attr("src", response.image_url).show();
							$(currentField.hiddenInputId).val(response.image_path);
							$(currentField.existingImageClass).remove();
							$("#cropModal").modal("hide");
						},
						error: function () {
							alert("Something went wrong!");
						},
					});
				}, "image/jpeg");
			});
		}

	</script>
</body>

</html>