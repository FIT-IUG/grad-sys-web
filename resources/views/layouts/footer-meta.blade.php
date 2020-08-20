<!-- jQuery -->
<script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- Select2 -->
<script src="{{asset('assets/plugins/select2/select2.full.min.js')}}"></script>
<!-- Morris.js charts -->
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>--}}
{{--<script src="{{asset('assets/plugins/morris/morris.min.js')}}"></script>--}}
<!-- ChartJS -->
{{--<script src="{{asset('assets/plugins/chart.js/Chart.min.js')}}"></script>--}}
<!-- Sparkline -->
{{--<script src="{{asset('assets/plugins/sparkline/jquery.sparkline.min.js')}}"></script>--}}
<!-- jvectormap -->
{{--<script src="{{asset('assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js')}}"></script>--}}
{{--<script src="{{asset('assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js')}}"></script>--}}
<!-- jQuery Knob Chart -->
{{--<script src="{{asset('assets/plugins/knob/jquery.knob.js')}}"></script>--}}
<!-- daterangepicker -->
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>--}}
{{--<script src="{{asset('assets/plugins/daterangepicker/daterangepicker.js')}}"></script>--}}
<!-- datepicker -->
{{--<script src="{{asset('assets/plugins/datepicker/bootstrap-datepicker.js')}}"></script>--}}
<!-- Bootstrap WYSIHTML5 -->
{{--<script src="{{asset('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js')}}"></script>--}}
<!-- Slimscroll -->
{{--<script src="{{asset('assets/plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>--}}
<!-- FastClick -->
{{--<script src="{{asset('assets/plugins/fastclick/fastclick.js')}}"></script>--}}
<!-- bs-custom-file-input -->
<script src="https://adminlte.io/themes/dev/AdminLTE/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- SweetAlert -->
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<!-- DataTables -->
<script src="{{asset('assets/plugins/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('assets/plugins/datatables/dataTables.bootstrap4.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('assets/dist/js/adminlte.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{asset('assets/dist/js/pages/dashboard.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/dist/js/demo.js')}}"></script>
<script src="{{asset('assets/bootstrap-notify/bootstrap-notify-3.1.3/dist/bootstrap-notify.js')}}"></script>

<!-- FLOT CHARTS -->
<script src="{{asset('assets/plugins/flot/jquery.flot.js')}}"></script>
<!-- FLOT RESIZE PLUGIN - allows the chart to redraw when the window is resized -->
<script src="{{asset('assets/plugins/flot-old/jquery.flot.resize.min.js')}}"></script>


{{-- select 2 things --}}
<script>
    $(function () {
        //Initialize Select2 Elements
        $('.select2').select2();

        $(function () {
            $("#data-table").DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Arabic.json",
                }
            });
        });
    })
</script>

