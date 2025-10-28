<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('triviya-android-chrome-192x192.png') }}">
        <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('triviya-android-chrome-512x512.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('triviya-favicon-16x16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('triviya-favicon-32x32.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('triviya-apple-touch-icon.png') }}">

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet" />


        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead

        @production
            <script type="text/javascript">
                window._mfq = window._mfq || [];
                (function() {
                    var mf = document.createElement("script");
                    mf.type = "text/javascript";
                    mf.defer = true;
                    mf.src = "//cdn.mouseflow.com/projects/043b11ee-9221-4772-902f-e64555edd74d.js";
                    document.getElementsByTagName("head")[0].appendChild(mf);
                })();
            </script>
        @endproduction
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
