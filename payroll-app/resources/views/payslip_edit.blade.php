<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Slip Gaji: {{ $employee->full_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <!-- Info -->
                 <div class="grid grid-cols-2 gap-4 mb-6 text-sm text-gray-600">
                    <div>
                        <span class="block font-bold">Periode:</span>
                        {{ $period->name }} ({{ $period->code }})
                    </div>
                    <div>
                        <span class="block font-bold">Karyawan:</span>
                        {{ $employee->full_name }} ({{ $employee->employee_code }})
                    </div>
                </div>

                <form method="POST" action="{{ route('payslips.update', [$period->id, $employee->id]) }}">
                    @csrf
                    @method('PUT')

                    <h3 class="text-lg font-medium text-gray-900 mb-4">Rincian Komponen</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Hapus</th>
                                    <th scope="col" class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Komponen</th>
                                    <th scope="col" class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Tipe</th>
                                    <th scope="col" class="px-3 py-2 text-right font-medium text-gray-500 uppercase">Qty</th>
                                    <th scope="col" class="px-3 py-2 text-right font-medium text-gray-500 uppercase">Jumlah (Rp)</th>
                                    <th scope="col" class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($details as $detail)
                                    <tr>
                                        <td class="px-3 py-2">
                                            <input type="checkbox" name="remove_details[]" value="{{ $detail->id }}" class="rounded border-gray-300 text-red-600 shadow-sm focus:border-red-300 focus:ring focus:ring-red-200 focus:ring-opacity-50">
                                        </td>
                                        <td class="px-3 py-2">
                                            {{ $detail->name }}
                                            <div class="text-xs text-gray-400">{{ $detail->code }}</div>
                                        </td>
                                        <td class="px-3 py-2">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $detail->type === 'earning' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($detail->type) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" name="details[{{ $detail->id }}][quantity]" value="{{ $detail->quantity }}" class="w-16 border-gray-300 rounded text-right text-xs">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" name="details[{{ $detail->id }}][amount]" value="{{ abs($detail->amount) }}" class="w-32 border-gray-300 rounded text-right text-xs font-semibold">
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="text" name="details[{{ $detail->id }}][remark]" value="{{ $detail->remark }}" class="w-full border-gray-300 rounded text-xs">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-3 py-4 text-center text-gray-500">Tidak ada komponen.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Tambah Komponen Manual</h3>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end bg-gray-50 p-4 rounded-lg">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Komponen</label>
                                <select name="new_component_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                    <option value="">Pilih Komponen...</option>
                                    @foreach($availableComponents as $comp)
                                        <option value="{{ $comp->id }}">[{{ $comp->code }}] {{ $comp->name }} ({{ ucfirst($comp->type) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jumlah (Rp)</label>
                                <input type="number" name="new_amount" placeholder="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Catatan</label>
                                <input type="text" name="new_remark" placeholder="Opsional" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('payroll.periods.show', $period->id) }}" class="px-4 py-2 bg-gray-500 text-white rounded-md text-sm hover:bg-gray-600">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700" onclick="return confirm('Simpan perubahan pada slip gaji ini?')">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
