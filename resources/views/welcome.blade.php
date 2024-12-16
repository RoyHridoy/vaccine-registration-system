<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900">
    <div class="flex flex-col items-center min-h-screen pt-6 bg-gray-100 sm:justify-center sm:pt-0">
        <div>
            <a href="/" wire:navigate>
                <x-application-logo class="w-20 h-20 text-gray-700 fill-current" />
            </a>
        </div>

        <div class="w-full px-6 py-4 mt-6 overflow-hidden bg-white shadow-md sm:max-w-md sm:rounded-lg">
            <div class="text-black">
                @if (Route::has('login'))
                <livewire:welcome.navigation />
                @endif
            </div>
            <div class="pt-5 mt-2 mb-5 text-center border-t">
                <h3
                    class="relative mb-8 text-xl font-medium text-center after:bg-gray-500 after:left-0 after:right-0 after:mx-auto after:-bottom-3 after:absolute after:w-1/4 after:h-0.5 after:rounded-full">
                    Welcome to Vaccine Registration
                    System</h3>
                <p class="px-10">If you don't have account, register first then login to see your status</p>
            </div>
        </div>
    </div>
</body>

</html>
