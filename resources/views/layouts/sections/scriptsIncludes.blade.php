<!-- laravel style -->
<script src="{{ asset('assets/vendor/js/helpers.js')}}"></script>

<!-- beautify ignore:start -->
@if ($configData['hasCustomizer'])
  <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
  <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
  <script src="{{ asset('assets/vendor/js/template-customizer.js') }}"></script>
@endif

  <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
  <script src="{{ asset('assets/js/config.js') }}"></script>
  @section('page-script')

<script type="text/javascript">
    
    $(document).ready(function() {
        $('.select2').select2({
            dropdownParent: $('body')
        });
        initDatepicker();
    });

  function initDatepicker() {
    // $('.jalali_datepicker').each(function () {
    //     let input = $(this);
    //     input.datepicker({
    //         cellWidth: 45,
    //         cellHeight: 30,
    //         fontSize: 16,
    //         format: 'YYYY/MM/DD',
    //         onSelect: function () {
    //             let rawDate = input.val();
    //             let parts = rawDate.split('/');
    //             if (parts.length === 3) {
    //                 let year = parts[0];
    //                 let month = parts[1].toString().padStart(2, '0');
    //                 let day = parts[2].toString().padStart(2, '0');
    //                 let formatted = `${year}/${month}/${day}`;
    //                 input.val(formatted);
    //             }
    //         }
    //     });
    // });
}

  </script>



@endsection
<!-- beautify ignore:end -->

