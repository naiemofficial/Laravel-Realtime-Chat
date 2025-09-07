<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>{{ config('app.name') }}</title>
    <link rel="shortcut icon" href="{{ url("/assets/icons/logo.png")  }}" type="image/x-icon">

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4" type="text/javascript"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body {{ $attributes }}>
    {{ $slot }}
</body>
</html>
