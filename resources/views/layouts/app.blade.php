<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('img/favicon-3.png') }}"/>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap -->
    <link href="{{ asset('Gentelella/vendor/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('Gentelella/vendor/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{ asset('Gentelella/vendor/nprogress/nprogress.css') }}" rel="stylesheet">
    <!-- Animate.css -->
    <link href="{{ asset('Gentelella/vendor/animate.css/animate.min.css') }}" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="{{ asset('Gentelella/build/css/custom.min.css') }}" rel="stylesheet">

    <style>
        body {
            font-family: Arial, Helvetica, sans-serif !important;
            margin: 0 !important;
            background: url('{{ $configuration ? $configuration->background : '' }}') no-repeat center center fixed;
            background-size: cover;
        }

        .login_wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login_form {
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-control {
            border-radius: 4px;
            margin-bottom: 15px;
            padding: 10px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn-default {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            width: 100%;
            border: none;
            font-size: 16px;
        }

        .btn-default:hover {
            background-color: #0056b3;
        }

        .separator {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .login_content {
            text-align: center;
        }

        .login_content img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    @yield('content')

    <!-- Scripts generales -->
    <script src="{{ asset('theme/lib/jquery/jquery.js') }}"></script>
    <script src="{{ asset('theme/lib/jquery-ui/jquery-ui.js') }}"></script>
    <script src="{{ asset('theme/lib/popper.js/popper.js') }}"></script>
    <script src="{{ asset('theme/lib/bootstrap/bootstrap.js') }}"></script>
    <script src="{{ asset('theme/lib/moment/moment.js') }}"></script>


    <script src="{{ asset('theme/lib/jquery-switchbutton/jquery.switchButton.js') }}"></script>
    <script src="{{ asset('theme/lib/peity/jquery.peity.js') }}"></script>

    <!-- plugins de validacion de campos del formulario  -->
    <script src="{{asset("js/plugins/jquery-validation/jquery.validate.min.js")}}"></script>
    <script src="{{asset("js/plugins/jquery-validation/localization/messages_es.min.js")}}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    @yield("scriptsPlugins")

    <!-- Notificaciones -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>


    {{-- JS CUSTOM  --}}
    <script src="{{ asset('js/portal/scripts.js') }}"></script>
    <script src="{{ asset('js/portal/comunes.js') }}"></script>

    <!-- SCRIPT PAGE -->
    @yield('scripts_content')
    <!-- FIN SCRIPT PAGE -->

</body>
</html>
