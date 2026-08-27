<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Poultry Management System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased" style="background-color: #f3f4f6;">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4 bg-gray-100">
        <!-- Logo Section -->
        <div class="mb-6">
            <a href="/" class="flex flex-col items-center">
                <!-- Custom Logo Container - Replace with your logo -->
                <div class="flex items-center justify-center mb-3 overflow-hidden">
                    <x-application-logo />
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Poultry Management System</h1>
                <p class="text-sm text-gray-500 mt-1">Streamline your farm operations</p>
            </a>
        </div>

        <!-- Card Container -->
        <div class="w-full sm:max-w-md mt-2">
            <div class="bg-white shadow-2xl rounded-3xl overflow-hidden">
                <!-- Decorative top bar -->
                <div class="h-2 bg-gradient-to-r from-green-500 via-blue-500 to-indigo-500"></div>
                
                <div class="px-8 py-10">
                    {{ $slot }}
                </div>
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-8">
                <p class="text-xs text-gray-500">© {{ date('Y') }} Poultry Management System. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>