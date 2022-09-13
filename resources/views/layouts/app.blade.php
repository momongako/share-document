<!DOCTYPE html>

<html lang="en">

<head>
    <base href="./">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets/img/favicon.ico')}}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="assets/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">

    <!-- use bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <!-- Scripts -->
    <script src="{{ asset('js/header.js') }}"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script>
        window.Laravel = {!! json_encode(
    [
        'csrfToken' => csrf_token(),
        'baseUrl' => url('/'),
    ],
    JSON_UNESCAPED_UNICODE,
) !!};
    </script>
</head>

<body class="c-app">
<div id="app">
    <div class="c-wrapper">
        @include('layouts.header')
        <div class="c-body">
            <main class="c-main c-content-wraper">
                <div class="container-fluid">
                    <h2>Shared Document</h2>
                     @yield('content')
                </div>
            </main>
        </div>
    </div>
</div>
@yield('javascript')
</body>

</html>
