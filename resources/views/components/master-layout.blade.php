<!DOCTYPE html>
<!-- Critical Dark Mode Handling -->
<script>
    (function() {
        if (localStorage.getItem('dark') === 'true') {
            document.write('<style>html { background: #1a1a1a !important; }</style>');
        }
    })();
</script>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ session()->has('dir') ? session()->get('dir') : 'ltr' }}">

<head>
    <!-- Immediate Dark Mode Styles -->
    <style>
        html[data-bs-theme="dark"] {
            background-color: #1a1a1a !important;
            color: #ffffff !important;
        }
        html[data-bs-theme="dark"] body {
            background-color: #1a1a1a !important;
            color: #ffffff !important;
        }
    </style>
    <script>
        if (localStorage.getItem('dark') === 'true') {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        }
    </script>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="baseUrl" content="{{ env('APP_URL') }}" />

    <title>{{ config('app.name', 'Laravel') }}</title>
    <script>
        const storedTheme = localStorage.getItem('data-bs-theme') || 'light';
        document.documentElement.setAttribute('data-bs-theme', storedTheme);
    </script>

    <!-- Dynamic Theme Colors -->
    <script>
        // Set primary color immediately on page load
        const savedPrimaryColor = localStorage.getItem('primaryColor');
        if (savedPrimaryColor) {
            const root = document.documentElement;

            // Convert HEX to RGB for primary-rgb
            const hex = savedPrimaryColor.replace('#', '');
            const r = parseInt(hex.substring(0, 2), 16);
            const g = parseInt(hex.substring(2, 4), 16);
            const b = parseInt(hex.substring(4, 6), 16);

            // Set CSS variables for primary color
            root.style.setProperty('--bs-primary', savedPrimaryColor);
            root.style.setProperty('--bs-primary-rgb', `${r}, ${g}, ${b}`);
            root.style.setProperty('--bs-primary-bg-subtle', `rgba(${r}, ${g}, ${b}, 0.09)`);
            root.style.setProperty('--bs-primary-border-subtle', `rgba(${r}, ${g}, ${b}, 0.09)`);
            root.style.setProperty('--bs-primary-hover-bg', `rgba(${r}, ${g}, ${b}, 0.75)`);
            root.style.setProperty('--bs-primary-hover-border', `rgba(${r}, ${g}, ${b}, 0.75)`);
            root.style.setProperty('--bs-primary-active-bg', `rgba(${r}, ${g}, ${b}, 0.75)`);
            root.style.setProperty('--bs-primary-active-border', `rgba(${r}, ${g}, ${b}, 0.75)`);
        }
    </script>

    @include('partials._head') <!-- Your other head includes like CSS files -->
    <script src="https://www.gstatic.com/firebasejs/6.0.2/firebase.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
</head>

<body class="" id="app">
    @include('partials._body') <!-- Your body content -->
</body>
{{-- <script>
    jQuery('.select2').select2({
         width: '100%'
    });
</script> --}}

</html>
