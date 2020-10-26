<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- Meta --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- Icons --}}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ URL::asset('vendor/bazar/img/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ URL::asset('vendor/bazar/img/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ URL::asset('vendor/bazar/img/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ URL::asset('vendor/bazar/img/favicon/site.webmanifest') }}">

    {{-- Styles --}}
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600&display=swap" rel="stylesheet">
    <link href="{{ URL::asset('vendor/bazar/app.css') }}" rel="stylesheet">

    {{-- Scripts --}}
    <script>
        window.initialPage = {!! json_encode($page) !!};
        window.translations = {!! json_encode($translations) !!};
    </script>
    <script src="{{ URL::asset('vendor/bazar/app.js') }}" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Bazar.boot();
        });
    </script>

    {{-- Title --}}
    <title>Bazar</title>
</head>
<body>
    <div id="app"></div>

    @include ('bazar::layout.svg-icons')
</body>
</html>
