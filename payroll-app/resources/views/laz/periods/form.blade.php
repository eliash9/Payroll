<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $period->exists ? __('Edit Periode') : __('Tambah Periode') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-xl font-semibold mb-4">{{ $period->exists ? 'Edit Periode' : 'Tambah Periode' }}</h1>
                <form action="{{ $period->exists ? route('laz.periods.update', $period) : route('laz.periods.store') }}" method="POST" class="space-y-3">
                    @csrf
                    @if ($period->exists) @method('PUT') @endif
                    <div>
                        <label class="text-sm text-slate-600">Program</label>
                        <select name="program_id" class="mt-1 w-full border rounded px-3 py-2">
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}" @selected(old('program_id', $period->program_id) == $program->id)>{{ $program->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm text-slate-600">Nama periode</label>
                        <input name="name" value="{{ old('name', $period->name) }}" class="mt-1 w-full border rounded px-3 py-2">
                    </div>
                    <div class="grid md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm text-slate-600">Tanggal buka</label>
                            <input type="datetime-local" name="open_at" value="{{ old('open_at', optional($period->open_at)->format('Y-m-d\TH:i')) }}" class="mt-1 w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="text-sm text-slate-600">Tanggal tutup</label>
                            <input type="datetime-local" name="close_at" value="{{ old('close_at', optional($period->close_at)->format('Y-m-d\TH:i')) }}" class="mt-1 w-full border rounded px-3 py-2">
                        </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-3">
                        <div>
                            <label class="text-sm text-slate-600">Kuota permohonan</label>
                            <input type="number" name="application_quota" value="{{ old('application_quota', $period->application_quota) }}" class="mt-1 w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="text-sm text-slate-600">Kuota anggaran</label>
                            <input type="number" name="budget_quota" value="{{ old('budget_quota', $period->budget_quota) }}" class="mt-1 w-full border rounded px-3 py-2">
                        </div>
                    </div>
                    <div>
                        <label class="text-sm text-slate-600">Status</label>
                        <select name="status" class="mt-1 w-full border rounded px-3 py-2">
                            @foreach (['draft','open','closed','archived'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $period->status) === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-3">
                        <button class="px-4 py-2 bg-emerald-600 text-white rounded">Simpan</button>
                        <a href="{{ route('laz.periods.index') }}" class="px-4 py-2 border rounded">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
