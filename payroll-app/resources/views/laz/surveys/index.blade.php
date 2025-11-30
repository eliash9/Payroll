<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tugas Survey') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-xl font-semibold mb-4">Survey Permohonan</h1>
                <table class="w-full text-sm border">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="p-2 border">Permohonan</th>
                            <th class="p-2 border">Pemohon</th>
                            <th class="p-2 border">Program</th>
                            <th class="p-2 border">Surveyor</th>
                            <th class="p-2 border">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($surveys as $survey)
                            <tr>
                                <td class="p-2 border"><a class="text-emerald-700" href="{{ route('laz.applications.show', $survey->application) }}">{{ $survey->application->code }}</a></td>
                                <td class="p-2 border">{{ $survey->application->applicant_name }}</td>
                                <td class="p-2 border">{{ $survey->application->program->name }}</td>
                                <td class="p-2 border">{{ $survey->surveyor->name }}</td>
                                <td class="p-2 border">{{ $survey->survey_date?->format('d M Y') ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">{{ $surveys->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
