<!DOCTYPE html>
    <html lang="{{ app()->getLocale() }}">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="asset('images/icons/favicon.ico')"/>

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- vendor css -->
        <link href="{{ asset('theme/lib/font-awesome/css/font-awesome.css') }}" rel="stylesheet">
        <link href="{{ asset('theme/lib/Ionicons/css/ionicons.css') }}" rel="stylesheet">
        <link href="{{ asset('theme/lib/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet">
        <link href="{{ asset('theme/lib/jquery-switchbutton/jquery.switchButton.css') }}" rel="stylesheet">
        <link href="{{ asset('theme/lib/highlightjs/github.css') }}" rel="stylesheet">
        <link href="{{ asset('theme/lib/datatables/jquery.dataTables.css') }}" rel="stylesheet">
        <link href="{{ asset('theme/lib/select2/css/select2.min.css') }}" rel="stylesheet">

        <!-- notificacion -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

        <!-- Bracket CSS -->
        <link rel="stylesheet" href="{{ asset('theme/bracket.css') }}">
        <link rel="stylesheet" href="{{ asset('css/portal/style.css') }}">

        <!-- Css contenct -->
        @yield('css_custom')

        

    </head>
    <body>

    @yield('content')
 
        <!-- Scripts generales -->
        <script src="{{ asset('theme/lib/jquery/jquery.js') }}"></script>
        <script src="{{ asset('theme/lib/jquery-ui/jquery-ui.js') }}"></script>
        <script src="{{ asset('theme/lib/popper.js/popper.js') }}"></script>
        <script src="{{ asset('theme/lib/bootstrap/bootstrap.js') }}"></script>
        <script src="{{ asset('theme/lib/perfect-scrollbar/js/perfect-scrollbar.jquery.js') }}"></script>
        <script src="{{ asset('theme/lib/moment/moment.js') }}"></script>

        
        <script src="{{ asset('theme/lib/jquery-switchbutton/jquery.switchButton.js') }}"></script>
        <script src="{{ asset('theme/lib/peity/jquery.peity.js') }}"></script>
        {{-- <script src="{{ asset('theme/lib/highlightjs/highlight.pack.js') }}"></script> --}}
        <!-- <script src="{{ asset('theme/lib/select2/js/select2.min.js') }}"></script>-->

        <!-- plugins de validacion de campos del formulario  -->
        <script src="{{asset("js/plugins/jquery-validation/jquery.validate.min.js")}}"></script>
        <script src="{{asset("js/plugins/jquery-validation/localization/messages_es.min.js")}}"></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

        @yield("scriptsPlugins")

        <!-- Notificaciones -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

        <!-- JS THEM -->
        <script src="{{ asset('theme/bracket.js') }}"></script>

        {{-- JS CUSTOM  --}}
        <script src="{{ asset('js/portal/scripts.js') }}"></script>
        <script src="{{ asset('js/portal/comunes.js') }}"></script>   

        <!-- SCRIPT PAGE -->
        @yield('scripts_content')
        <!-- FIN SCRIPT PAGE -->

        
        
    </body>
</html>