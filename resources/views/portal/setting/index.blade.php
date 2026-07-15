@extends('portal.layout.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- DataTable with Buttons -->
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="mb-0">Update Password</h5>
        </div>
        <div class="card-body">
            <form id="passwordUpdateForm">
                @csrf
                @method('put')
                <div class="row">
                    <!-- New Password -->
                    <div class="col-md-12 mb-3">
                        <label for="new_password" class="form-label">New Password <span
                                class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>

                    <!-- Confirm Password -->
                    <div class="col-md-12 mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password <span
                                class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                            required>
                    </div>
                </div>
                <div class="row">
                    <!-- Image Upload -->
                    <div class="col-md-12 mb-3">
                        <label for="image" class="form-label">Image</label>
                        <input type="file" class="form-control" id="image" name="image">
                        <img id="image-preview" class="mt-2" width="200" style="display: none;">
                    </div>
                    @if(isset(auth()->user()->avatar))
                    <img src="{{auth()->user()->avatar}}" class="img-fluid existing-image" style="max-width: 200px; height: auto;">
                    @endif
                    <!-- Hidden input for cropped image -->
                    <input type="hidden" id="cropped_image" name="cropped_image">
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>

<!-- jQuery for AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Form submission for password update
    $('#passwordUpdateForm').submit(function(e) {
        e.preventDefault();

        let formData = {
            new_password: $('#new_password').val(),
            confirm_password: $('#confirm_password').val(),
            _token: "{{ csrf_token() }}",
            image:$('#cropped_image').val()
        };

        $.ajax({
            url: "{{ route('setting.update') }}",
            type: "PUT",
            data: formData,
            success: function(response) {
                Swal.fire('Updated !', 'Password Changed Successfully.', 'success');
                $('#passwordUpdateForm')[0].reset(); // Reset form fields
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = "Error updating password!";
                if (errors) {
                    errorMessage = Object.values(errors).join("\n");
                }
                Swal.fire('Error!', 'Failed to Update Password.', 'error');
            }
        });
    });

    // Initialize the cropper for image selection
    initCropperModal();
});

// Initialize Cropper Modal
function initCropperModal() {

    let cropper;
    let selectedFile;

    // When user selects an image
    $("#image").change(function (event) {
        let files = event.target.files;
        if (files && files.length > 0) {
            selectedFile = files[0];
            let reader = new FileReader();

            reader.onload = function (e) {
                // Dynamically append modal
                if ($("#cropModal").length === 0) {
                    $("body").append(`
                        <div id="cropModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
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
                                            <img id="crop-image" style="width: 100%;">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" id="crop-cancel" data-dismiss="modal">Cancel</button>
                                        <button type="button" class="btn btn-primary" id="crop-save">Crop & Upload</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                }

                $("#crop-image").attr("src", e.target.result);
                $("#cropModal").modal("show");

                // Initialize Cropper
                $("#cropModal").on("shown.bs.modal", function () {
                    let image = document.getElementById("crop-image");
                    cropper = new Cropper(image, {
                        aspectRatio: 16 / 9, // Change this for different landscape ratios
                        viewMode: 2,
                        autoCropArea: 1,
                    });
                });

                // Destroy Cropper when modal is closed
                $("#cropModal").on("hidden.bs.modal", function () {
                    if (cropper) {
                        cropper.destroy();
                    }
                    $("#cropModal").remove(); // Remove modal from DOM
                });
            };

            reader.readAsDataURL(selectedFile);
        }
    });

    // Manually close modal when Cancel or Close is clicked
    $(document).on("click", "#crop-cancel, .close", function () {
        $("#cropModal").modal("hide");
    });

    // Handle cropping and uploading
    $(document).on("click", "#crop-save", function () {
        let canvas = cropper.getCroppedCanvas({ width: 300, height: 300 });
        canvas.toBlob(function (blob) {
            let formData = new FormData();
            formData.append("cropped_image", blob);
            formData.append("_token", "{{ csrf_token() }}");

            // Send cropped image to Laravel backend
            $.ajax({
                url: "{{ route('media_gallery.uploadCroppedImage') }}",
                type: "POST",
                data: formData,
                headers: { 'Loader': true },
                contentType: false,
                processData: false,
                success: function (response) {
                    $("#image-preview").attr("src", response.image_url).show();
                    $("#cropped_image").val(response.image_path);
                    // Remove previous uploaded image (if present)
                    $(".existing-image").remove();
                    $("#cropModal").modal("hide");
                },
                error: function () {
                    alert("Something went wrong!");
                }
            });
        }, "image/jpeg");
    });
}
</script>

@endsection
