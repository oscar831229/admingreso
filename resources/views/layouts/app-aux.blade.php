<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('img/favicon-3.png') }}"/>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Css contenct -->
    @yield('css_custom')
  

</head>
<body>
    <div id="app">

        <main class="">
            @yield('content')
        </main>

    </div>

    <!-- SCRIPT PAGE -->
        @yield('scripts_content')
    <!-- FIN SCRIPT PAGE -->
    
    

</body>
</html>
