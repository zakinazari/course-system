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


 function printDiv(id, header) {
        var TitleText = '';
        if ($('#' + header).val() != null) {
            TitleText = '<h2 id="printHeader"><strong>' + $('#' + header).val() + '</strong></h2>';
        }
        $('#' + id).printThis({
            debug: false, //* show the iframe for debugging
            importCSS: true, //* import page CSS
            importStyle: true, //* import style tags
            printContainer: true, //* grab outer container as well as the contents of the selector
            loadCSS: false,/* path to additional css file - us an array [] for multiple*/
            pageTitle: "", //* add title to print page
            removeInline: false, //* remove all inline styles from print elements
            printDelay: 333, //* variable print delay; depending on complexity a higher value may be necessary
            header: TitleText, // or null,               //* prefix to html
            formValues: false
        });
    }
    
</script>



@endsection
<!-- beautify ignore:end -->

