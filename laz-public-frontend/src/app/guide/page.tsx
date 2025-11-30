import Link from "next/link";
import { ArrowLeft, CheckCircle, FileText, Search, Send } from "lucide-react";

export default function GuidePage() {
    return (
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <Link href="/" className="inline-flex items-center text-slate-500 hover:text-laz-green-600 mb-8 transition-colors">
                <ArrowLeft className="w-4 h-4 mr-2" /> Kembali ke Beranda
            </Link>

            <div className="text-center mb-12">
                <h1 className="text-3xl font-bold text-slate-900 mb-4">Panduan Pengajuan Bantuan</h1>
                <p className="text-slate-600 max-w-2xl mx-auto">
                    Pelajari langkah-langkah mudah untuk mengajukan permohonan bantuan di LAZ Sidogiri.
                </p>
            </div>

            <div className="space-y-12 relative">
                {/* Step 1 */}
                <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 p-4 opacity-5">
                        <Search className="w-32 h-32" />
                    </div>
                    <div className="relative z-10 flex gap-6">
                        <div className="flex-shrink-0 w-12 h-12 bg-laz-green-100 text-laz-green-600 rounded-full flex items-center justify-center font-bold text-xl">
                            1
                        </div>
                        <div>
                            <h3 className="text-xl font-bold text-slate-900 mb-3">Pilih Program Bantuan</h3>
                            <p className="text-slate-600 mb-4 leading-relaxed">
                                Buka halaman <strong>Program</strong> untuk melihat daftar bantuan yang tersedia. Baca dengan teliti deskripsi, kriteria penerima, dan cakupan wilayah untuk memastikan Anda memenuhi syarat.
                            </p>
                            <Link href="/programs" className="text-laz-green-600 font-medium hover:underline">
                                Lihat Daftar Program &rarr;
                            </Link>
                        </div>
                    </div>
                </div>

                {/* Step 2 */}
                <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 p-4 opacity-5">
                        <FileText className="w-32 h-32" />
                    </div>
                    <div className="relative z-10 flex gap-6">
                        <div className="flex-shrink-0 w-12 h-12 bg-laz-teal-100 text-laz-teal-600 rounded-full flex items-center justify-center font-bold text-xl">
                            2
                        </div>
                        <div>
                            <h3 className="text-xl font-bold text-slate-900 mb-3">Siapkan Dokumen & Isi Formulir</h3>
                            <p className="text-slate-600 mb-4 leading-relaxed">
                                Klik tombol <strong>Ajukan Sekarang</strong> pada program yang dipilih. Siapkan dokumen pendukung seperti KTP, KK, dan dokumen lain yang diminta dalam format digital (foto/scan). Isi formulir dengan data yang jujur dan lengkap.
                            </p>
                            <ul className="list-disc pl-5 text-sm text-slate-500 space-y-1">
                                <li>Pastikan foto dokumen jelas dan terbaca.</li>
                                <li>Isi NIK dan data diri sesuai KTP.</li>
                                <li>Jelaskan kebutuhan Anda secara rinci.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                {/* Step 3 */}
                <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 p-4 opacity-5">
                        <Send className="w-32 h-32" />
                    </div>
                    <div className="relative z-10 flex gap-6">
                        <div className="flex-shrink-0 w-12 h-12 bg-laz-green-100 text-laz-green-600 rounded-full flex items-center justify-center font-bold text-xl">
                            3
                        </div>
                        <div>
                            <h3 className="text-xl font-bold text-slate-900 mb-3">Kirim & Simpan Kode Tiket</h3>
                            <p className="text-slate-600 mb-4 leading-relaxed">
                                Setelah mengirim permohonan, Anda akan mendapatkan <strong>Kode Tiket</strong> (contoh: APP-2025-XXXXX). Simpan kode ini baik-baik karena diperlukan untuk mengecek status pengajuan Anda.
                            </p>
                        </div>
                    </div>
                </div>

                {/* Step 4 */}
                <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 relative overflow-hidden">
                    <div className="absolute top-0 right-0 p-4 opacity-5">
                        <CheckCircle className="w-32 h-32" />
                    </div>
                    <div className="relative z-10 flex gap-6">
                        <div className="flex-shrink-0 w-12 h-12 bg-laz-teal-100 text-laz-teal-600 rounded-full flex items-center justify-center font-bold text-xl">
                            4
                        </div>
                        <div>
                            <h3 className="text-xl font-bold text-slate-900 mb-3">Pantau Status & Verifikasi</h3>
                            <p className="text-slate-600 mb-4 leading-relaxed">
                                Tim kami akan memverifikasi data Anda. Anda dapat memantau prosesnya melalui halaman <strong>Cek Status</strong>. Jika diperlukan, petugas kami akan menghubungi Anda untuk survei lapangan.
                            </p>
                            <Link href="/status" className="text-laz-green-600 font-medium hover:underline">
                                Cek Status Permohonan &rarr;
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
