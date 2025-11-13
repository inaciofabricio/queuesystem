<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} {!! empty($subtitle) ? '' : ' &vellip; ' . $subtitle  !!}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/png">

    <link rel="stylesheet" href="{{ asset('assets/fontawesome/css/all.min.css') }}">
    @vite('resources/css/app.css')

    <link rel="stylesheet" href="{{ asset('assets/datatables/datatables.min.css') }}">
    <script src="{{ asset('assets/datatables/datatables.min.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('assets/coloris/coloris.css') }}">
    <script src="{{ asset('assets/coloris/coloris.js') }}"></script>
</head>
<body class="bg-zinc-200">

    <x-layouts.user_top_bar />

    <x-layouts.main_menu />

    <div class="p-8">
        {{ $slot }}
    </div>

</body>
</html>
