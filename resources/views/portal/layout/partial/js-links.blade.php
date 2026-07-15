<script src="{{ url('/portal') }}/assets/vendor/libs/jquery/jquery.js"></script>
<script src="{{ url('/portal') }}/assets/vendor/libs/popper/popper.js"></script>
<script src="{{ url('/portal') }}/assets/vendor/js/bootstrap.js"></script>
<script src="{{ url('/portal') }}/assets/vendor/libs/node-waves/node-waves.js"></script>
<script src="{{ url('/portal') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="{{ url('/portal') }}/assets/vendor/libs/hammer/hammer.js"></script>
<script src="{{ url('/portal') }}/assets/vendor/libs/i18n/i18n.js"></script>
<script src="{{ url('/portal') }}/assets/vendor/libs/typeahead-js/typeahead.js"></script>
<script src="{{ url('/portal') }}/assets/vendor/js/menu.js"></script>
<script src="{{ url('/portal') }}/assets/vendor/libs/select2/select2.js"></script>

<!-- endbuild -->
<script src="{{ url('/portal') }}/assets/vendor/libs/chartjs/chartjs.js"></script>
<!-- <script src="{{ url('/portal') }}/assets/js/charts-chartjs.js"></script> -->
<!-- Toastr -->
<script src="{{ url('/portal') }}/assets/vendor/libs/toastr/toastr.js"></script>

<!-- Vendors JS -->
<script src="{{ url('/portal') }}/assets/vendor/libs/apex-charts/apexcharts.js"></script>
<script src="{{ url('/portal') }}/assets/vendor/libs/swiper/swiper.js"></script>
<script src="{{ url('/portal') }}/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="https://cdn.tiny.cloud/1/4x1ql8bed9ixorz51eviyloihwcv800m7vy5l5498d8nn3si/tinymce/7/tinymce.min.js"
	referrerpolicy="origin"></script>
<!-- Main JS -->
<script src="{{ url('/portal') }}/assets/js/main.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/rowreorder/1.2.8/js/dataTables.rowReorder.min.js"></script>








<!-- Page JS -->
<script src="{{ url('/portal') }}/assets/js/dashboards-analytics.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
	// Show the loader on form submission
	document.querySelector('.form').addEventListener('submit', function () {
		document.getElementById('loader').style.display = 'block';
	});
</script>


<script>
	var Toast = Swal.mixin({
		toast: true,
		position: 'top',
		showConfirmButton: false,
		timer: 3000
	});

	function toast_error(message) {
		toastr.error(message)
	}

	function toast_success(message) {
		toastr.success(message)
	}

	function toast_info(message) {
		toastr.info(message)
	}

	function sweet_alert_info(message) {
		Toast.fire({
			icon: 'info',
			title: message
		});
	}

	function delete_confirmation(link = "http://www.google.com", text = "You won't be able to revert this!") {
		Swal.fire({
			title: 'Are you sure?',
			text: text,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#111111',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
			if (result.isConfirmed) {
				window.location = link;
			}
		})
	}

	function general_confirmation(link = "http://www.google.com", text = "You won't be able to revert this!") {
		Swal.fire({
			title: 'Are you sure?',
			text: text,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#111111',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, do it!'
		}).then((result) => {
			if (result.isConfirmed) {
				window.location = link;
			}
		})
	}

	function action_confirmation(link = "http://www.google.com", text = "") {
		Swal.fire({
			title: 'Are you sure?',
			text: text,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#111111',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, do it!'
		}).then((result) => {
			if (result.isConfirmed) {
				window.location = link;
			}
		})
	}

	function deleteConfirmation(deleteUrl) {
		Swal.fire({
			title: 'Are you sure?',
			text: "You won't be able to revert this!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#111111',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
			if (result.isConfirmed) {
				// Redirect to the delete URL if confirmed
				window.location.href = deleteUrl;
			}
		});
	}
</script>


@if (session('success'))
	<script type="text/javascript">
		toast_success("{{ session('success') }}")
	</script>
@endif

@if (session('error'))
	<script type="text/javascript">
		toast_error("{{ session('error') }}")
	</script>
@endif

@if ($errors->any())
	@foreach ($errors->all() as $error)
		<script type="text/javascript">
			toast_error("{{ $error }}")
		</script>
	@endforeach
@endif