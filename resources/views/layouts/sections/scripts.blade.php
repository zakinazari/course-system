<!-- ================== CORE JS ================== -->

<!-- jQuery (همیشه باید اول باشد) -->
<script src="{{ asset('assets/vendor/libs/jquery/jquery.js')}}"></script>
<!-- Popper و Bootstrap -->
<script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>

<!-- Scrollbar و ابزارهای پایه -->
<script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/hammer/hammer.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/i18n/i18n.js') }}"></script>
<script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

<!-- ================== FORM & UI PLUGINS ================== -->

<!-- Stepper -->
<script src="{{ asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>

<!-- Select2 & Bootstrap Select -->
<script src="{{ asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/select2/select2.js')}}"></script>

<!-- Date & Time Pickers -->
<!-- <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script> -->
<!-- <script type="text/javascript" src="{{ asset('assets/js/datepicker/bootstrap-datepicker.js') }}"></script> -->
<!-- <script type="text/javascript" src="{{ asset('assets/js/datepicker/bootstrap-datepicker.fa.min.js') }}"></script> -->

<!-- <script src="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script> -->
  <!-- <script type="text/javascript" src="{{ asset('assets/datepicker/js/persianDatepicker.js') }}"></script> -->
  <!-- <script type="text/javascript" src="{{ asset('assets/persianDatepicker/js/jquery-1.10.1.min.js.min.js') }}"></script> -->
  <script type="text/javascript" src="{{ asset('assets/persianDatepicker/js/persianDatepicker.min.js') }}"></script>
<!-- <script src="{{asset('assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js')}}"></script> -->
<script src="{{asset('assets/vendor/libs/pickr/pickr.js')}}"></script>

<!-- Typeahead -->
<script src="{{ asset('assets/vendor/libs/typeahead-js/typeahead.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/bloodhound/bloodhound.js')}}"></script>

<!-- DataTables -->
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>

<!-- Toastr & SweetAlert -->
<script src="{{ asset('assets/vendor/libs/toastr/toastr.js')}}"></script>
<script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>

<!-- Form Validation -->
<script src="{{ asset('assets/vendor/libs/formvalidation/dist/js/FormValidation.min.js')}}"></script>

<!-- jQuery Repeater -->
<script src="{{ asset('assets/vendor/libs/jquery-repeater/jquery-repeater.js')}}"></script>

<!-- ================== APP & PAGE JS ================== -->

<!-- Wizard, Forms & Selects -->
<script src="{{ asset('assets/js/form-wizard-icons.js')}}"></script>
<script src="{{ asset('assets/js/forms-pickers.js')}}"></script>
<script src="{{ asset('assets/js/forms-selects.js')}}"></script>
<script src="{{ asset('assets/js/forms-typeahead.js')}}"></script>

<!-- Table Plugins -->
<script src="{{ asset('assets/livetable/jquery.tabledit.js')}}"></script>
<script src="{{ asset('assets/js/tables-datatables-advanced.js')}}"></script>

<!-- Notifications & UI -->
<script src="{{ asset('assets/js/ui-toasts.js')}}"></script>

<!-- Print Utilities -->
<script src="{{ asset('assets/jquery-printme.min.js')}}" type="text/javascript"></script>
<script src="{{ asset('assets/js/printThis.js')}}" type="text/javascript"></script>

<!-- Invoice Page -->
<script src="{{ asset('assets/js/app-invoice-edit.js')}}"></script>


@yield('vendor-script')
<!-- ================== THEME MAIN ================== -->
<script src="{{ asset('assets/js/main.js') }}"></script>
<script src="{{ asset('assets/js/form-validation.js')}}"></script>
<script src="{{ asset('assets/js/ui-modals.js')}}"></script>


<!-- ================== STACK / PAGE INJECTIONS ================== -->
@stack('pricing-script')
<!-- <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script> -->
@yield('page-script')
