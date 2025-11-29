@php
    $masterActive = request()->routeIs('companies.*')
        || request()->routeIs('branches.*')
        || request()->routeIs('departments.*')
        || request()->routeIs('positions.*')
        || request()->routeIs('shifts.*')
        || request()->routeIs('leave-types.*')
        || request()->routeIs('payroll-components.*')
        || request()->routeIs('bpjs-rates.*')
        || request()->routeIs('tax-rates.*')
        || request()->routeIs('kpi.*');
    $sdmActive = request()->routeIs('employees.*') || request()->routeIs('employee-kpi.*') || request()->routeIs('employee-bpjs.*') || request()->routeIs('employee-loans.*');
    $attendanceActive = request()->routeIs('attendance.*') || request()->routeIs('leaveovertime.*');
    $payrollActive = request()->routeIs('payroll.periods.*') || request()->routeIs('payslips.*');
    $fundraisingActive = request()->routeIs('fundraising.transactions.*') || request()->routeIs('expense-claims.*');
@endphp

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>
                    <x-dropdown align="left" width="w-64">
                        <x-slot name="trigger">
                            <button class="{{ $masterActive
                                ? 'inline-flex h-full items-center px-1 py-6 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
                                : 'inline-flex h-full items-center px-1 py-6 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out' }}">
                                <span>Master</span>
                                <svg class="ms-1 fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('companies.index')" class="{{ request()->routeIs('companies.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Perusahaan
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('branches.index')" class="{{ request()->routeIs('branches.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Cabang
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('departments.index')" class="{{ request()->routeIs('departments.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Departemen
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('positions.index')" class="{{ request()->routeIs('positions.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Jabatan
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('shifts.index')" class="{{ request()->routeIs('shifts.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Shift
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('leave-types.index')" class="{{ request()->routeIs('leave-types.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Jenis Cuti/Izin
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('payroll-components.index')" class="{{ request()->routeIs('payroll-components.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Komponen Payroll
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('bpjs-rates.index')" class="{{ request()->routeIs('bpjs-rates.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Tarif BPJS
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('tax-rates.index')" class="{{ request()->routeIs('tax-rates.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Tarif Pajak
                            </x-dropdown-link>
                        <x-dropdown-link :href="route('kpi.index')" class="{{ request()->routeIs('kpi.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                            Master KPI
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('users.index')" class="{{ request()->routeIs('users.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                            Pengguna
                        </x-dropdown-link>
                    </x-slot>
                </x-dropdown>
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="{{ $sdmActive
                                ? 'inline-flex h-full items-center px-1 py-6 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
                                : 'inline-flex h-full items-center px-1 py-6 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out' }}">
                                <span>SDM</span>
                                <svg class="ms-1 fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('dashboard.volunteer')" class="{{ request()->routeIs('dashboard.volunteer') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Relawan
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('employees.index')" class="{{ request()->routeIs('employees.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Karyawan
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('employee-kpi.index')" class="{{ request()->routeIs('employee-kpi.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                KPI Karyawan
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('employee-bpjs.index')" class="{{ request()->routeIs('employee-bpjs.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                BPJS Karyawan
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('employee-loans.index')" class="{{ request()->routeIs('employee-loans.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Pinjaman Karyawan
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="{{ $fundraisingActive
                                ? 'inline-flex h-full items-center px-1 py-6 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
                                : 'inline-flex h-full items-center px-1 py-6 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out' }}">
                                <span>Fundraising</span>
                                <svg class="ms-1 fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('fundraising.transactions.index')" class="{{ request()->routeIs('fundraising.transactions.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Transaksi Fundraising
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('expense-claims.index')" class="{{ request()->routeIs('expense-claims.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Klaim Pengeluaran
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="{{ $attendanceActive
                                ? 'inline-flex h-full items-center px-1 py-6 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
                                : 'inline-flex h-full items-center px-1 py-6 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out' }}">
                                <span>Kehadiran</span>
                                <svg class="ms-1 fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('attendance.index')" class="{{ request()->routeIs('attendance.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Absensi
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('leaveovertime.index')" class="{{ request()->routeIs('leaveovertime.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Cuti/Lembur
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="{{ $payrollActive
                                ? 'inline-flex  items-center px-1 py-6 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
                                : 'inline-flex  items-center px-1 py-6 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out' }}">
                                <span>Payroll</span>
                                <svg class="ms-1 fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('payroll.periods.index')" class="{{ request()->routeIs('payroll.periods.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Periode Payroll
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('payslips.index')" class="{{ request()->routeIs('payslips.*') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Payslip
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>

                    <x-dropdown align="left" width="48">
                        <x-slot name="trigger">
                            <button class="{{ request()->routeIs('reports.*')
                                ? 'inline-flex  items-center px-1 py-6 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
                                : 'inline-flex  items-center px-1 py-6 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out' }}">
                                <span>Laporan</span>
                                <svg class="ms-1 fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('reports.payroll')" class="{{ request()->routeIs('reports.payroll') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Laporan Gaji
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('reports.attendance')" class="{{ request()->routeIs('reports.attendance') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Laporan Kehadiran
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('reports.fundraising')" class="{{ request()->routeIs('reports.fundraising') ? 'font-semibold text-indigo-600 bg-gray-50' : '' }}">
                                Laporan Fundraising
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
            <div class="pt-3 border-t border-gray-200 space-y-1">
                <div class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Master</div>
                <x-responsive-nav-link :href="route('companies.index')" :active="request()->routeIs('companies.*')" class="ps-6">
                    Perusahaan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('branches.index')" :active="request()->routeIs('branches.*')" class="ps-6">
                    Cabang
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('departments.index')" :active="request()->routeIs('departments.*')" class="ps-6">
                    Departemen
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('positions.index')" :active="request()->routeIs('positions.*')" class="ps-6">
                    Jabatan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('shifts.index')" :active="request()->routeIs('shifts.*')" class="ps-6">
                    Shift
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('leave-types.index')" :active="request()->routeIs('leave-types.*')" class="ps-6">
                    Jenis Cuti/Izin
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('payroll-components.index')" :active="request()->routeIs('payroll-components.*')" class="ps-6">
                    Komponen Payroll
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('bpjs-rates.index')" :active="request()->routeIs('bpjs-rates.*')" class="ps-6">
                    Tarif BPJS
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tax-rates.index')" :active="request()->routeIs('tax-rates.*')" class="ps-6">
                    Tarif Pajak
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('kpi.index')" :active="request()->routeIs('kpi.*')" class="ps-6">
                    Master KPI
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" class="ps-6">
                    Pengguna
                </x-responsive-nav-link>
            </div>
            <div class="pt-3 border-t border-gray-200 space-y-1">
                <div class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">SDM</div>
                <x-responsive-nav-link :href="route('dashboard.volunteer')" :active="request()->routeIs('dashboard.volunteer')" class="ps-6">
                    Relawan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')" class="ps-6">
                    Karyawan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employee-kpi.index')" :active="request()->routeIs('employee-kpi.*')" class="ps-6">
                    KPI Karyawan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employee-bpjs.index')" :active="request()->routeIs('employee-bpjs.*')" class="ps-6">
                    BPJS Karyawan
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('employee-loans.index')" :active="request()->routeIs('employee-loans.*')" class="ps-6">
                    Pinjaman Karyawan
                </x-responsive-nav-link>
            </div>

            <div class="pt-3 border-t border-gray-200 space-y-1">
                <div class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kehadiran</div>
                <x-responsive-nav-link :href="route('attendance.index')" :active="request()->routeIs('attendance.*')" class="ps-6">
                    Absensi
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('leaveovertime.index')" :active="request()->routeIs('leaveovertime.*')" class="ps-6">
                    Cuti/Lembur
                </x-responsive-nav-link>
            </div>

            <div class="pt-3 border-t border-gray-200 space-y-1">
                <div class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Payroll</div>
                <x-responsive-nav-link :href="route('payroll.periods.index')" :active="request()->routeIs('payroll.periods.*')" class="ps-6">
                    Periode Payroll
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('payslips.index')" :active="request()->routeIs('payslips.*')" class="ps-6">
                    Payslip
                </x-responsive-nav-link>
            </div>

            <div class="pt-3 border-t border-gray-200 space-y-1">
                <div class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Fundraising</div>
                <x-responsive-nav-link :href="route('fundraising.transactions.index')" :active="request()->routeIs('fundraising.transactions.*')" class="ps-6">
                    Transaksi Fundraising
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('expense-claims.index')" :active="request()->routeIs('expense-claims.*')" class="ps-6">
                    Klaim Pengeluaran
                </x-responsive-nav-link>
            </div>

            <div class="pt-3 border-t border-gray-200 space-y-1">
                <div class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Laporan</div>
                <x-responsive-nav-link :href="route('reports.payroll')" :active="request()->routeIs('reports.payroll')" class="ps-6">
                    Laporan Gaji
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports.attendance')" :active="request()->routeIs('reports.attendance')" class="ps-6">
                    Laporan Kehadiran
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('reports.fundraising')" :active="request()->routeIs('reports.fundraising')" class="ps-6">
                    Laporan Fundraising
                </x-responsive-nav-link>
            </div>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
