<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="shortcut icon" href="{{ url("/assets/icons/logo.png")  }}" type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4" type="text/javascript"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div class="max-w-[1400px] w-full mx-auto py-6 px-4 sm:px-6 lg:px-8 min-h-full min-w-[500px] flex flex-col flex-1 justify-center">
                <div class="flex justify-center mb-5">
                    <a href="/">
                        <x-application-logo class="w-20 h-20 fill-current text-gray-500W" />
                    </a>
                </div>

                <div class="content-main w-full">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
