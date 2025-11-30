<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Permohonan Bantuan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-xl font-semibold mb-4">Permohonan Bantuan</h1>

                <form method="GET" class="grid md:grid-cols-5 gap-3 bg-slate-50 border rounded p-3 mb-4 text-sm">
                    <select name="program_id" class="border rounded px-2 py-1">
                        <option value="">Semua Program</option>
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}" @selected(request('program_id')==$program->id)>{{ $program->name }}</option>
                        @endforeach
                    </select>
                    <select name="period_id" class="border rounded px-2 py-1">
                        <option value="">Semua Periode</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}" @selected(request('period_id')==$period->id)>{{ $period->name }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="border rounded px-2 py-1">
                        <option value="">Semua Status</option>
                        @foreach (\App\Models\Application::STATUSES as $status)
                            <option value="{{ $status }}" @selected(request('status')==$status)>{{ $status }}</option>
                        @endforeach
                    </select>
                    <select name="branch_id" class="border rounded px-2 py-1">
                        <option value="">Semua Cabang</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" @selected(request('branch_id')==$branch->id)>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                    <div class="flex gap-2">
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="border rounded px-2 py-1 w-1/2">
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="border rounded px-2 py-1 w-1/2">
                    </div>
                    <div class="md:col-span-5 flex gap-2">
                        <button class="px-3 py-2 bg-emerald-600 text-white rounded">Filter</button>
                        <a href="{{ route('laz.applications.index') }}" class="px-3 py-2 border rounded">Reset</a>
                    </div>
                </form>

                <table class="w-full text-sm border">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="p-2 border">Kode</th>
                            <th class="p-2 border">Pemohon</th>
                            <th class="p-2 border">Program</th>
                            <th class="p-2 border">Periode</th>
                            <th class="p-2 border">Status</th>
                            <th class="p-2 border">Tanggal</th>
                            <th class="p-2 border">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $app)
                            <tr>
                                <td class="p-2 border">{{ $app->code }}</td>
                                <td class="p-2 border">{{ $app->applicant_name }}</td>
                                <td class="p-2 border">{{ $app->program->name ?? '-' }}</td>
                                <td class="p-2 border">{{ $app->period->name ?? '-' }}</td>
                                <td class="p-2 border">{{ $app->status }}</td>
                                <td class="p-2 border">{{ $app->created_at?->format('d/m/Y') }}</td>
                                <td class="p-2 border">
                                    <a class="text-emerald-700" href="{{ route('laz.applications.show', $app) }}">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $applications->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
