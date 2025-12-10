<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Payroll App') }}</title>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <!-- Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50 text-gray-800 font-figtree">
        <div class="relative min-h-screen flex flex-col justify-center items-center selection:bg-indigo-500 selection:text-white overflow-hidden">
            
            <!-- Navigation -->
            @if (Route::has('login'))
                <div class="fixed top-0 right-0 p-6 text-right z-10 w-full flex justify-between items-center px-4 sm:px-8 bg-white/80 backdrop-blur-md border-b border-gray-100">
                    <div class="text-xl sm:text-2xl font-bold text-indigo-600 tracking-tight flex items-center gap-2">
<img src="{{ asset('images/logo.png') }}" class="w-12 h-6 object-contain" alt="Logo">
                        Payroll App
                    </div>
                    <div class="space-x-4">
                        @auth
                            <div class="flex items-center gap-4">
                                <a href="{{ url('/dashboard') }}" class="font-semibold text-gray-600 hover:text-indigo-600 transition duration-300">Dashboard</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="font-semibold text-red-500 hover:text-red-700 transition duration-300">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="font-semibold text-gray-600 hover:text-indigo-600 transition duration-300">Masuk</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="ml-4 font-semibold text-white bg-indigo-600 px-5 py-2.5 rounded-lg hover:bg-indigo-700 transition duration-300 shadow-lg shadow-indigo-500/30">Daftar</a>
                            @endif
                        @endauth
                    </div>
                </div>
            @endif

            <!-- Hero Section -->
            <div class="max-w-7xl mx-auto p-6 lg:p-8 w-full pt-24 sm:pt-32">
                <div class="flex flex-col items-center justify-center text-center">
                    
                    <div class="mb-8 relative group inline-block">
                        <div class="absolute -inset-1 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                        <div class="relative px-6 py-2 bg-white ring-1 ring-gray-900/5 rounded-full leading-none flex items-center space-x-2">
                            <span class="text-indigo-600 font-bold text-xs uppercase tracking-wider">Baru</span>
                            <span class="text-slate-600 text-sm">Manajemen Payroll Tanpa Hambatan v2.0</span>
                        </div>
                    </div>

                    <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-gray-900 mb-6 leading-tight">
                        Manajemen Payroll <br class="hidden sm:block">
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">Terdepan</span>
                    </h1>
                    
                    <p class="mt-4 text-lg md:text-xl text-gray-600 max-w-2xl mx-auto mb-10 leading-relaxed">
                        Sederhanakan operasional SDM Anda dengan solusi payroll komprehensif kami. Perhitungan otomatis, manajemen data aman, dan pelaporan mendalam di satu tempat.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center w-full sm:w-auto">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-8 py-4 bg-indigo-600 text-white font-bold rounded-full hover:bg-indigo-700 transition duration-300 shadow-xl shadow-indigo-500/30 transform hover:-translate-y-1 text-center">
                                Ke Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-8 py-4 bg-indigo-600 text-white font-bold rounded-full hover:bg-indigo-700 transition duration-300 shadow-xl shadow-indigo-500/30 transform hover:-translate-y-1 text-center">
                                Mulai Sekarang
                            </a>
                            <a href="#features" class="px-8 py-4 bg-white text-indigo-600 font-bold rounded-full border border-indigo-100 hover:border-indigo-300 hover:bg-indigo-50 transition duration-300 shadow-lg shadow-gray-200/50 text-center">
                                Pelajari Lebih Lanjut
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Features Grid -->
                <div id="features" class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-24 px-4">
                    <!-- Feature 1 -->
                    <div class="bg-white/60 backdrop-blur-sm p-8 rounded-2xl shadow-sm hover:shadow-xl transition duration-300 border border-white/20 hover:border-indigo-100 group">
                        <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center mb-6 text-indigo-600 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Perhitungan Cerdas</h3>
                        <p class="text-gray-600 leading-relaxed">Perhitungan pajak, BPJS, dan lembur otomatis. Minimalkan kesalahan dan hemat waktu berharga setiap bulan.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white/60 backdrop-blur-sm p-8 rounded-2xl shadow-sm hover:shadow-xl transition duration-300 border border-white/20 hover:border-purple-100 group">
                        <div class="w-14 h-14 bg-purple-100 rounded-2xl flex items-center justify-center mb-6 text-purple-600 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Pusat Karyawan</h3>
                        <p class="text-gray-600 leading-relaxed">Database terpusat untuk semua informasi karyawan, catatan kehadiran, dan manajemen cuti.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white/60 backdrop-blur-sm p-8 rounded-2xl shadow-sm hover:shadow-xl transition duration-300 border border-white/20 hover:border-pink-100 group">
                        <div class="w-14 h-14 bg-pink-100 rounded-2xl flex items-center justify-center mb-6 text-pink-600 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Aman & Patuh</h3>
                        <p class="text-gray-600 leading-relaxed">Dibangun dengan keamanan sebagai prioritas. Pastikan data Anda aman dan proses Anda mematuhi peraturan lokal.</p>
                    </div>
                </div>

                <div class="mt-24 pb-10 text-center text-sm text-gray-500 border-t border-gray-200 pt-8">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Payroll App') }}. Hak cipta dilindungi undang-undang.
                </div>
            </div>
            
            <!-- Background Elements -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
                <div class="absolute top-[-10%] left-[-10%] w-[40rem] h-[40rem] bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
                <div class="absolute top-[-10%] right-[-10%] w-[40rem] h-[40rem] bg-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
                <div class="absolute bottom-[-20%] left-[20%] w-[40rem] h-[40rem] bg-pink-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
            </div>
        </div>
        
        <style>
            @keyframes blob {
                0% { transform: translate(0px, 0px) scale(1); }
                33% { transform: translate(30px, -50px) scale(1.1); }
                66% { transform: translate(-20px, 20px) scale(0.9); }
                100% { transform: translate(0px, 0px) scale(1); }
            }
            .animate-blob {
                animation: blob 7s infinite;
            }
            .animation-delay-2000 {
                animation-delay: 2s;
            }
            .animation-delay-4000 {
                animation-delay: 4s;
            }
        </style>
    </body>
</html>
