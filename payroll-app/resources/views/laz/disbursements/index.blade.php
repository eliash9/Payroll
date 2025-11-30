<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Penyaluran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-xl font-semibold mb-4">Penyaluran Permohonan</h1>
                <table class="w-full text-sm border">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="p-2 border">Kode</th>
                            <th class="p-2 border">Pemohon</th>
                            <th class="p-2 border">Program</th>
                            <th class="p-2 border">Status</th>
                            <th class="p-2 border">Form Penyaluran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $app)
                            <tr>
                                <td class="p-2 border">{{ $app->code }}</td>
                                <td class="p-2 border">{{ $app->applicant_name }}</td>
                                <td class="p-2 border">{{ $app->program->name }}</td>
                                <td class="p-2 border">{{ $app->status }}</td>
                                <td class="p-2 border">
                                    <form action="{{ route('laz.disbursements.store', $app) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 gap-1 text-xs">
                                        @csrf
                                        <input type="datetime-local" name="disbursed_at" class="border rounded px-2 py-1">
                                        <input name="method" placeholder="Metode" class="border rounded px-2 py-1">
                                        <input name="total_amount" placeholder="Total nilai" class="border rounded px-2 py-1">
                                        <input name="notes" placeholder="Catatan" class="border rounded px-2 py-1">
                                        <input type="file" name="proofs[]" multiple class="border rounded px-2 py-1">
                                        <button class="px-3 py-2 bg-emerald-600 text-white rounded">Catat</button>
                                    </form>
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
