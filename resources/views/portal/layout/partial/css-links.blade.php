 <!-- Favicon -->
 <link rel="icon" type="image/x-icon" href="{{ url('/portal') }}/assets/img/favicon/favicon.ico" />

 <!-- Fonts -->
 <link rel="preconnect" href="https://fonts.googleapis.com" />
 <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
 <link
     href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
     rel="stylesheet" />

 <!-- Icons -->
 <link rel="stylesheet" href="{{ url('/portal') }}/assets/vendor/fonts/fontawesome.css" />
 <link rel="stylesheet" href="{{ url('/portal') }}/assets/vendor/fonts/tabler-icons.css" />
 <link rel="stylesheet" href="{{ url('/portal') }}/assets/vendor/fonts/flag-icons.css" />

 <!-- Core CSS -->
 <link rel="stylesheet" href="{{ url('/portal') }}/assets/vendor/css/rtl/core.css"
     class="template-customizer-core-css" />
 <link rel="stylesheet" href="{{ url('/portal') }}/assets/vendor/css/rtl/theme-default.css"
     class="template-customizer-theme-css" />
 <link rel="stylesheet" href="{{ url('/portal') }}/assets/css/demo.css" />

 <!-- Vendors CSS -->
 <link rel="stylesheet" href="{{ url('/portal') }}/assets/vendor/libs/node-waves/node-waves.css" />
 <link rel="stylesheet" href="{{ url('/portal') }}/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
 <link rel="stylesheet" href="{{ url('/portal') }}/assets/vendor/libs/typeahead-js/typeahead.css" />
 <link rel="stylesheet" href="{{ url('/portal') }}/assets/vendor/libs/apex-charts/apex-charts.css" />
 <link rel="stylesheet" href="{{ url('/portal') }}/assets/vendor/libs/swiper/swiper.css" />
 <link rel="stylesheet" href="{{ url('/portal') }}/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
 <link rel="stylesheet" href="{{ url('/portal') }}/assets/vendor/libs/select2/select2.css" />
 <link rel="stylesheet"
     href="{{ url('/portal') }}/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
 <link rel="stylesheet"
     href="{{ url('/portal') }}/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />


     <!-- Cropper.js CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
<!-- Cropper.js JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
 <!-- Page CSS -->
 <link rel="stylesheet" href="{{ url('/portal') }}/assets/vendor/css/pages/cards-advance.css" />
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">


<!-- Quill -->
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<!-- Jodit -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jodit/build/jodit.min.css">
<!-- Trix -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css">

 <!-- Helpers -->
 <script src="{{ url('/portal') }}/assets/vendor/js/helpers.js"></script>
 <script src="{{ url('/portal') }}/assets/js/config.js"></script>

 <!-- Toastr -->
 <link rel="stylesheet" href="{{ url('/portal') }}/assets/vendor/libs/toastr/toastr.css">
 <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowreorder/1.2.8/css/rowReorder.dataTables.min.css">
 <style>
     /* For the first table with reordering-table class */
     .reordering-table tbody td:first-child {
         font-weight: bold;
         background-color: #cecece;
         cursor: pointer;
     }

     .reordering-table thead th:first-child {
         font-weight: bold;
         background-color: #cecece;
     }

     /* For the second table with reordering-table-v2 class */
     .reordering-table-v2 tbody td:nth-child(2) {
         font-weight: bold;
         background-color: #cecece;
         cursor: pointer;
     }

     .reordering-table-v2 thead th:nth-child(2) {
         font-weight: bold;
         background-color: #cecece;
     }


     .required:after {
         color: red;
         content: " *";
     }

     .note-modal-content {
         padding-bottom: 10px !important;
     }

     .nowrap-actions {
         white-space: nowrap;
     }

     .swal-popup-zindex {
         z-index: 1061 !important;
         /* Higher than Bootstrap modal (1055) */
     }
     .d-none{
        display: none;
     }

     /* ── Icon Circle (Dashboard stat cards) ─────────── */
     .icon-circle {
         width: 48px;
         height: 48px;
         border-radius: 12px;
         display: flex;
         align-items: center;
         justify-content: center;
         flex-shrink: 0;
         font-size: 1.35rem;
     }
     .icon-circle.bg-label-primary  { background: rgba(105,108,255,.15); color: #696cff; }
     .icon-circle.bg-label-success  { background: rgba(113,221,55,.15);  color: #71dd37; }
     .icon-circle.bg-label-warning  { background: rgba(255,171,0,.15);   color: #ffab00; }
     .icon-circle.bg-label-danger   { background: rgba(255,62,29,.15);   color: #ff3e1d; }
     .icon-circle.bg-label-info     { background: rgba(3,195,236,.15);   color: #03c3ec; }
     .icon-circle.bg-label-secondary{ background: rgba(133,146,163,.15); color: #8592a3; }

     /* ── Stat card polish ───────────────────────────── */
     .stat-card .card-body { padding: 1.25rem 1.5rem; }
     .stat-card .stat-label {
         font-size: 0.78rem;
         font-weight: 600;
         text-transform: uppercase;
         letter-spacing: 0.04em;
         color: #8592a3;
         margin-bottom: 0.35rem;
     }
     .stat-card .stat-value {
         font-size: 1.65rem;
         font-weight: 700;
         line-height: 1;
         color: #566a7f;
     }

     /* ── Page header ────────────────────────────────── */
     .page-header {
         display: flex;
         align-items: center;
         justify-content: space-between;
         flex-wrap: wrap;
         gap: 0.75rem;
         margin-bottom: 1.5rem;
     }
     .page-header .page-title {
         font-size: 1.125rem;
         font-weight: 600;
         color: #566a7f;
         margin: 0;
     }
     .page-header .breadcrumb {
         margin: 0;
         font-size: 0.8125rem;
     }

     /* ── Table card header ──────────────────────────── */
     .card-header-toolbar {
         display: flex;
         align-items: center;
         gap: 0.5rem;
         flex-wrap: wrap;
     }
 </style>
