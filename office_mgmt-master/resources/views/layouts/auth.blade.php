<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <title>{{ env('APP_NAME', '') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="{{ env('APP_NAME') }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#2d2072">
    <link rel="manifest" href="/manifest.json">

    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">

    <link href="{{ asset('assets/vendor/flaticonn/flaticon-uicons/css/all/all.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/yaireoo/tagify/dist/tagify.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/metismenu/dist/metisMenu.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link class="main-switcher" href="{{ asset('assets/css/switcher.css') }}" rel="stylesheet">

    <link class="main-plugins" href="{{ asset('assets/css/plugins.css') }}" rel="stylesheet">
    <link class="main-css" href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

</head>

<body>
    <div class="auth-wrapper">
        <div class="row">

            @yield('content')

        </div>
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then((reg) => console.log('Service Worker registered successfully.', reg))
                    .catch((err) => console.error('Service Worker registration failed.', err));
            });
        }
    </script>

    <script>
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form && (!form.checkValidity || form.checkValidity())) {
                const buttons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                setTimeout(() => {
                    buttons.forEach(button => {
                        button.disabled = true;
                        if (button.tagName === 'INPUT') {
                            button.value = 'Processing...';
                        } else {
                            button.innerHTML =
                                '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';
                        }
                    });
                }, 10);
            }
        });
    </script>

    <script src="{{ asset('assets/vendor/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/metismenu/dist/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/yaireoo/tagify/dist/tagify.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>
    <script src="{{ asset('assets/js/icnav-init.js') }}"></script>

    <script src="{{ asset('assets/js/switcher/styleSwitcher.js') }}"></script>
    <script src="{{ asset('assets/js/switcher/demo.js') }}"></script>
</body>

</html>
