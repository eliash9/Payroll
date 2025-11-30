<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panduan Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-2xl font-semibold mb-4">Panduan Penggunaan Admin</h1>
                <p class="text-slate-600 mb-6">Ringkasan alur kerja untuk peran internal LAZ Sidogiri.</p>

                <div class="space-y-6 text-sm text-slate-700">
                    <div class="border border-slate-200 rounded-xl p-4 bg-white">
                        <h2 class="text-lg font-semibold text-[#1f626f] mb-2">1) Login & Profil</h2>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Masuk melalui menu <strong>Masuk Admin</strong> dengan email dan password.</li>
                            <li>Perbarui profil dan password di menu <strong>Profile</strong> setelah login.</li>
                        </ul>
                    </div>

                    <div class="border border-slate-200 rounded-xl p-4 bg-white">
                        <h2 class="text-lg font-semibold text-[#1f626f] mb-2">2) Program & Periode</h2>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Role: <strong>super_admin</strong>, <strong>admin_pusat</strong>.</li>
                            <li>Buat dan kelola program: nama, kategori, jenis penerima, deskripsi, status aktif/nonaktif.</li>
                            <li>Buat periode per program: nama gelombang, tanggal buka/tutup, status (draft/open/closed/archived), kuota.</li>
                            <li>Hanya program aktif + periode open yang muncul di publik.</li>
                        </ul>
                    </div>

                    <div class="border border-slate-200 rounded-xl p-4 bg-white">
                        <h2 class="text-lg font-semibold text-[#1f626f] mb-2">3) Manajemen Permohonan</h2>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Role: <strong>super_admin</strong>, <strong>admin_pusat</strong>, <strong>admin_cabang</strong> (hanya cabang sendiri), <strong>audit</strong> (lihat saja).</li>
                            <li>Filter berdasarkan program, periode, status, cabang, tanggal.</li>
                            <li>Ubah status screening/survey_assigned/waiting_approval/dll sesuai progres.</li>
                            <li>Admin cabang hanya boleh mengelola permohonan cabang terkait.</li>
                        </ul>
                    </div>

                    <div class="border border-slate-200 rounded-xl p-4 bg-white">
                        <h2 class="text-lg font-semibold text-[#1f626f] mb-2">4) Penugasan & Survey</h2>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Assign surveyor pada detail permohonan (admin pusat/cabang/super_admin).</li>
                            <li>Surveyor mengisi hasil: tanggal, metode, ringkasan, skor 1–5, rekomendasi, foto.</li>
                            <li>Status permohonan otomatis ke <strong>waiting_approval</strong> setelah survey disimpan.</li>
                        </ul>
                    </div>

                    <div class="border border-slate-200 rounded-xl p-4 bg-white">
                        <h2 class="text-lg font-semibold text-[#1f626f] mb-2">5) Persetujuan</h2>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Role: <strong>approver</strong>, <strong>super_admin</strong>.</li>
                            <li>Pilih keputusan: approved/rejected/revision; isi nominal disetujui & bentuk bantuan jika approved.</li>
                            <li>Status permohonan menjadi <strong>approved</strong> atau <strong>rejected</strong> sesuai keputusan.</li>
                        </ul>
                    </div>

                    <div class="border border-slate-200 rounded-xl p-4 bg-white">
                        <h2 class="text-lg font-semibold text-[#1f626f] mb-2">6) Penyaluran</h2>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Role: <strong>keuangan</strong>, <strong>super_admin</strong>.</li>
                            <li>Isi tanggal penyaluran, metode (transfer/cash/goods), total nilai, item detail (opsional), dan upload bukti.</li>
                            <li>Status permohonan menjadi <strong>completed</strong> setelah penyaluran disimpan.</li>
                        </ul>
                    </div>

                    <div class="border border-slate-200 rounded-xl p-4 bg-white">
                        <h2 class="text-lg font-semibold text-[#1f626f] mb-2">7) Laporan & Audit</h2>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Role: <strong>super_admin</strong>, <strong>admin_pusat</strong>, <strong>auditor</strong> (read-only).</li>
                            <li>Lihat rekap per program, per bulan, segmentasi pemohon, dan wilayah.</li>
                        </ul>
                    </div>

                    <div class="border border-slate-200 rounded-xl p-4 bg-white">
                        <h2 class="text-lg font-semibold text-[#1f626f] mb-2">8) Tips Keamanan</h2>
                        <ul class="list-disc ml-5 space-y-1">
                            <li>Ganti password berkala, minimal 10 karakter.</li>
                            <li>Jangan berbagi akun; gunakan peran sesuai tugas.</li>
                            <li>Unduh dokumen hanya dari permohonan valid.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
