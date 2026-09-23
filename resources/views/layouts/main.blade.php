<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SI-PITA - @yield('title', 'Diskominfo')</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body { background-color: #eef2ff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card-custom { background: #c7d2fe; border-radius: 20px; border: none; }
        .btn-primary-custom { background-color: #3b82f6; border: none; border-radius: 10px; padding: 10px 24px; font-weight: 600; }
        .btn-primary-custom:hover { background-color: #2563eb; }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    @include('components.navbar')
    @include('components.sidebar')
    <main class="flex-grow-1 d-flex align-items-center justify-content-center py-4">
        @yield('content')
    </main>
    @include('components.footer')
    @stack('scripts')
</body>
</html>
