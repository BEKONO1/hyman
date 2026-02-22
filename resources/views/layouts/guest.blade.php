<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="baseUrl" content="{{env('APP_URL')}}" />

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="shortcut icon" class="site_favicon_preview" href="{{ getSingleMedia(imageSession('get'),'favicon',null) }}" />

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
        <!-- Font Awesome CDN -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">


        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/backend.css') }}">
        <link rel="stylesheet" href="{{ asset('css/fronted-custom.css') }}">

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



    </head>
    <body class=" " >

        <div class="wrapper">
            {{ $slot }}
        </div>
         @include('partials._scripts')
    </body>
</html>
