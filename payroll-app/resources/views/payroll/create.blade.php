<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buat Periode Payroll</h2>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto space-y-4">
        <div class="bg-blue-50 border border-blue-200 rounded p-4 text-sm text-blue-800">
            <h3 class="font-bold mb-1">Panduan:</h3>
            <p>Buat periode baru untuk memulai perhitungan gaji. Tentukan tanggal mulai dan akhir (biasanya 1 bulan penuh, misal tgl 1 s/d 30/31, atau tgl 26 s/d 25 bulan berikutnya tergantung kebijakan).</p>
        </div>

        <div class="bg-white shadow-sm rounded p-6">
            <form method="post" action="{{ route('payroll.periods.store') }}" class="space-y-4">
                @csrf
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm">Perusahaan</label>
                        <select name="company_id" class="w-full border rounded px-3 py-2" required>
                            <option value="">Pilih</option>
                            @foreach($companies as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-sm">Kode</label>
                        <input name="code" class="w-full border rounded px-3 py-2" required placeholder="2025-01">
                    </div>
                    <div>
                        <label class="text-sm">Nama</label>
                        <input name="name" class="w-full border rounded px-3 py-2" placeholder="Januari 2025">
                    </div>
                    <div>
                        <label class="text-sm">Mulai</label>
                        <input type="date" name="start_date" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="text-sm">Selesai</label>
                        <input type="date" name="end_date" class="w-full border rounded px-3 py-2" required>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                    <a href="{{ route('payroll.periods.index') }}" class="text-slate-600 px-4 py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
