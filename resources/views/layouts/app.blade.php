<!doctype html>
<html lang="en" class="dark-theme">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/png" />
    <link href="{{ asset('/assets/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('/assets/css/style.css') }}" rel="stylesheet" />
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                @include('layouts.navbar')
                @yield('content')
            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    @yield('scripts')
</body>
</html>
