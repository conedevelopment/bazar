<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', App::getLocale()) }}">
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
    @foreach(Bazar\Support\Facades\Asset::styles() as $style)
        <link href="{{ $style['url'] }}" rel="stylesheet">
    @endforeach

    {{-- Scripts --}}
    <script>
        window.Bazar = {
            app: null,
            pages: {},
            menu: @json($menu),
            translations: @json($translations),
            boot: function () {
                ['booting', '_boot_', 'booted'].forEach(function (event) {
                    document.dispatchEvent(new CustomEvent('bazar:'+event, {
                        detail: { Bazar }
                    }));
                });
            }
        };
    </script>
    <script src="{{ URL::asset('vendor/bazar/app.js') }}" defer></script>
    @foreach(Bazar\Support\Facades\Asset::scripts() as $script)
        <script src="{{ $script['url'] }}" defer></script>
    @endforeach
    <script>document.addEventListener('DOMContentLoaded', Bazar.boot);</script>

    {{-- Title --}}
    <title>Bazar</title>
</head>
<body>
    {{-- App --}}
    <div id="app" data-page="{{ json_encode($page) }}"></div>

    {{-- SVG Icons --}}
    @include('bazar::svg-icons')
</body>
</html>
