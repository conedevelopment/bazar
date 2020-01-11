<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    {{-- Meta --}}
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- Icons --}}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('vendor/bazar/img/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('vendor/bazar/img/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('vendor/bazar/img/favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('vendor/bazar/img/favicon/site.webmanifest') }}">

    {{-- Styles --}}
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600&display=swap" rel="stylesheet">
    <link href="{{ asset(mix('app.css', 'vendor/bazar')) }}" rel="stylesheet">
    @foreach (Bazar\Support\Facades\Asset::styles() as $name => $path)
        <link id="css-{{ $name }}" href="{{ $path }}" rel="stylesheet">
    @endforeach

    {{-- Scripts --}}
    <script>
        window.translations = @json ($translations);
    </script>
    <script src="{{ asset(mix('app.js', 'vendor/bazar')) }}" defer></script>
    @foreach (Bazar\Support\Facades\Asset::scripts() as $name => $path)
        <script id="js-{{ $name }}" src="{{ $path }}" defer></script>
    @endforeach
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Bazar.boot();
        });
    </script>

    {{-- Title --}}
    <title>Bazar</title>
</head>
<body>
    <div id="app" data-page="{{ json_encode($page) }}"></div>

    @include ('bazar::partials.svg-icons')
</body>
</html>

