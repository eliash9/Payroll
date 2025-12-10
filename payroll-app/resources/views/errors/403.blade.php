<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - 403 Forbidden</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-50">
        <div class="min-h-screen flex flex-col items-center justify-center p-4">
            <div class="max-w-md w-full text-center space-y-8">
                <!-- Icon/Illustration -->
                <div class="relative w-32 h-32 mx-auto">
                    <div class="absolute inset-0 bg-red-100 rounded-full animate-pulse"></div>
                    <div class="absolute inset-2 bg-red-50 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>

                <div class="space-y-2">
                    <h1 class="text-4xl font-bold text-gray-900 tracking-tight">403 Forbidden</h1>
                    <h2 class="text-xl font-medium text-gray-600">Akses Ditolak</h2>
                    <p class="text-gray-500 max-w-sm mx-auto">
                        {{ $exception->getMessage() ?: 'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator jika Anda merasa ini adalah kesalahan.' }}
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-5 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors w-full sm:w-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Kembali ke Halaman Utama
                    </a>
                    
                    <button onclick="history.back()" class="inline-flex items-center justify-center px-5 py-2.5 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors w-full sm:w-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali Sebelumnya
                    </button>
                </div>
                
                <div class="pt-8 border-t border-gray-200">
                    <p class="text-xs text-gray-400">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
