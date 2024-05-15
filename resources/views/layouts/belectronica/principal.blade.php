<!DOCTYPE html>
    <html lang="{{ app()->getLocale() }}">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="{{ asset('img/favicon-3.png') }}"/>

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- vendor css -->
        {{--  <link href="{{ asset('theme/lib/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
        <link href="{{ asset('theme/lib/Ionicons/css/ionicons.css') }}" rel="stylesheet">
        <link href="{{ asset('theme/lib/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet">
        <link href="{{ asset('theme/lib/jquery-switchbutton/jquery.switchButton.css') }}" rel="stylesheet">
        <link href="{{ asset('theme/lib/highlightjs/github.css') }}" rel="stylesheet">
        <link href="{{ asset('theme/lib/datatables/jquery.dataTables.css') }}" rel="stylesheet">
        <link href="{{ asset('theme/lib/select2/css/select2.min.css') }}" rel="stylesheet">  --}}

        <!-- notificacion -->
        {{--  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">  --}}

        <!-- Bracket CSS -->
        {{--  <link rel="stylesheet" href="{{ asset('theme/bracket.css') }}">
        <link rel="stylesheet" href="{{ asset('css/portal/style.css') }}">  --}}

        <!-- Bootstrap -->
        <link href="{{ asset('Gentelella/vendor/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="{{ asset('Gentelella/vendor/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ asset('js/plugins/jquery.autocomplete/css/autocomplete.css') }}" rel="stylesheet">

        <!-- NProgress -->
        {{--  <link href="{{ asset('Gentelella/vendor/nprogress/nprogress.css') }}" rel="stylesheet">  --}}
        <!-- iCheck -->
        <link href="{{ asset('Gentelella/vendor/iCheck/skins/flat/green.css') }}" rel="stylesheet">

        <!-- bootstrap-progressbar -->
        {{--  <link href="{{ asset('Gentelella/vendor/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css') }}" rel="stylesheet">  --}}
        <!-- JQVMap -->
        {{--  <link href="{{ asset('Gentelella/vendor/jqvmap/dist/jqvmap.min.css') }}" rel="stylesheet"/>  --}}
        <!-- bootstrap-daterangepicker -->
        {{--  <link href="{{ asset('Gentelella/vendor/bootstrap-daterangepicker/daterangepicker.css') }}" rel="stylesheet">  --}}

        <!-- notificacion -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

        <!-- bootstrap-data tables -->
        <link href="{{ asset('theme/lib/datatables/jquery.dataTables.css') }}" rel="stylesheet">

        <!-- Custom Theme Style -->
        <link href="{{ asset('Gentelella/build/css/custom.css') }}" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('css/portal/style.css') }}">

        <!-- Css contenct -->
        @yield('css_custom')

        <style>
            .bg-primary {
                background-color: #2a59a5!important;
            }
        </style>



    </head>
    <body class="nav-md">

        <div class="container body">

            <div class="main_container">

                <!-- Procesar petición ajax -->
                @include('admin.includes.procesar')

                <!-- Inicio left panel -->
                @include('layouts.belectronica.left_panel')
                <!-- Fin left panel -->

                <!-- Inicio head panel -->
                @include('layouts.belectronica.head_panel')
                <!-- Fin head panel -->

                <!-- page content -->
                <div class="right_col" role="main">
                    @yield('content')
                </div>
                <!-- /page content -->

                <!-- footer content -->
                <footer>
                    <div class="pull-right">
                        <a href="#">Bamboocloud</a>
                    </div>
                    <div class="clearfix"></div>
                </footer>
                <!-- /footer content -->

            </div>
        </div>

        <!-- Scripts generales -->
        <!-- jQuery -->
        <script src="{{ asset('Gentelella/vendor/jquery/dist/jquery.min.js') }}"></script>
        <!-- Bootstrap -->
        <script src="{{ asset('Gentelella/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
        <!-- FastClick -->
        <script src="{{ asset('theme/lib/popper.js/popper.js') }}"></script>
        <script src="{{ asset('theme/lib/bootstrap/bootstrap.js') }}"></script>
        {{--  <script src="{{ asset('Gentelella/vendor/fastclick/lib/fastclick.js') }}"></script>  --}}
        <!-- NProgress -->
        {{--  <script src="{{ asset('Gentelella/vendor/nprogress/nprogress.js') }}"></script>  --}}
        <!-- Chart.js -->
        {{--  <script src="{{ asset('Gentelella/vendor/Chart.js/dist/Chart.min.js') }}"></script>  --}}
        <!-- gauge.js -->
        {{--  <script src="{{ asset('Gentelella/vendor/gauge.js/dist/gauge.min.js') }}"></script>  --}}
        <!-- bootstrap-progressbar -->
        {{--  <script src="{{ asset('Gentelella/vendor/bootstrap-progressbar/bootstrap-progressbar.min.js') }}"></script>  --}}
        <!-- iCheck -->
        <script src="{{ asset('Gentelella/vendor/iCheck/icheck.js') }}"></script>
        <!-- Skycons -->
        {{--  <script src="{{ asset('Gentelella/vendor/skycons/skycons.js') }}"></script>  --}}
        <!-- Flot -->
        {{--  <script src="{{ asset('Gentelella/vendor/Flot/jquery.flot.js') }}"></script>
        <script src="{{ asset('Gentelella/vendor/Flot/jquery.flot.pie.js') }}"></script>
        <script src="{{ asset('Gentelella/vendor/Flot/jquery.flot.time.js') }}"></script>
        <script src="{{ asset('Gentelella/vendor/Flot/jquery.flot.stack.js') }}"></script>
        <script src="{{ asset('Gentelella/vendor/Flot/jquery.flot.resize.js') }}"></script>  --}}
        <!-- Flot plugins -->
        {{--  <script src="{{ asset('Gentelella/vendor/flot.orderbars/js/jquery.flot.orderBars.js') }}"></script>
        <script src="{{ asset('Gentelella/vendor/flot-spline/js/jquery.flot.spline.min.js') }}"></script>
        <script src="{{ asset('Gentelella/vendor/flot.curvedlines/curvedLines.js') }}"></script>  --}}
        <!-- DateJS -->
        {{--  <script src="{{ asset('Gentelella/vendor/DateJS/build/date.js') }}"></script>  --}}
        <!-- JQVMap -->
        {{--  <script src="{{ asset('Gentelella/vendor/jqvmap/dist/jquery.vmap.js') }}"></script>
        <script src="{{ asset('Gentelella/vendor/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
        <script src="{{ asset('Gentelella/vendor/jqvmap/examples/js/jquery.vmap.sampledata.js') }}"></script>  --}}
        <!-- bootstrap-daterangepicker -->
        {{--  <script src="{{ asset('Gentelella/vendor/moment/min/moment.min.js') }}"></script>
        <script src="{{ asset('Gentelella/vendor/bootstrap-daterangepicker/daterangepicker.js') }}"></script>  --}}


        <script src="{{ asset('theme/lib/popper.js/popper.js') }}"></script>

        <!-- plugins de validacion de campos del formulario  -->
        <script src="{{asset('js/plugins/jquery-validation/jquery.validate.min.js')}}"></script>
        <script src="{{asset('js/plugins/jquery-validation/localization/messages_es.min.js')}}"></script>
        <script src="{{asset('js/plugins/sweetalert/sweetalert.min.js')}}"></script>

        <!-- Notificaciones -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

        <!-- Custom Theme Scripts -->
        <script src="{{ asset('js/portal/scripts.js') }}"></script>
        <script src="{{ asset('js/portal/comunes.js') }}"></script>
        <script src="{{ asset('Gentelella/build/js/custom.min.js') }}"></script>

        @yield("scriptsPlugins")

        @yield('scripts_content')

    </body>
</html>
