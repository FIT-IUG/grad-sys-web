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
<!-- Morris.js charts -->
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>--}}
{{--<script src="{{asset('assets/')}}plugins/morris/morris.min.js"></script>--}}
<!-- Sparkline -->
{{--<script src="{{asset('assets/')}}plugins/sparkline/jquery.sparkline.min.js"></script>--}}
<!-- jvectormap -->
{{--<script src="{{asset('assets/')}}plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>--}}
{{--<script src="{{asset('assets/')}}plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>--}}
<!-- jQuery Knob Chart -->
{{--<script src="{{asset('assets/')}}plugins/knob/jquery.knob.js"></script>--}}
<!-- daterangepicker -->
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js"></script>--}}
{{--<script src="{{asset('assets/')}}plugins/daterangepicker/daterangepicker.js"></script>--}}
<!-- datepicker -->
{{--<script src="{{asset('assets/')}}plugins/datepicker/bootstrap-datepicker.js"></script>--}}
<!-- Bootstrap WYSIHTML5 -->
{{--<script src="{{asset('assets/')}}plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>--}}
<!-- Slimscroll -->
{{--<script src="{{asset('assets/')}}plugins/slimScroll/jquery.slimscroll.min.js"></script>--}}
<!-- FastClick -->
{{--<script src="{{asset('assets/')}}plugins/fastclick/fastclick.js"></script>--}}
<!-- AdminLTE App -->
<script src="{{asset('assets/dist/js/adminlte.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{asset('assets/dist/js/pages/dashboard.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('assets/dist/js/demo.js')}}"></script>

<script src="{{asset('assets/loginAssets/js/main.js')}}"></script>
<script src="{{asset('assets/dist/js/notify.min.js')}}"></script>

@stack('script')
