import Link from "next/link";
import { ArrowLeft } from "lucide-react";

export default function PrivacyPage() {
    return (
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <Link href="/" className="inline-flex items-center text-slate-500 hover:text-laz-green-600 mb-8 transition-colors">
                <ArrowLeft className="w-4 h-4 mr-2" /> Kembali ke Beranda
            </Link>

            <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 md:p-12">
                <h1 className="text-3xl font-bold text-slate-900 mb-8">Kebijakan Privasi</h1>

                <div className="prose prose-slate max-w-none">
                    <p className="lead text-lg text-slate-600 mb-6">
                        Di LAZ Sidogiri, kami sangat menghargai privasi Anda dan berkomitmen untuk melindungi data pribadi yang Anda bagikan kepada kami. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi Anda.
                    </p>

                    <h3 className="text-xl font-bold text-slate-900 mt-8 mb-4">1. Informasi yang Kami Kumpulkan</h3>
                    <p>
                        Kami mengumpulkan informasi yang Anda berikan secara langsung saat mengajukan permohonan bantuan, termasuk namun tidak terbatas pada:
                    </p>
                    <ul className="list-disc pl-5 space-y-2 mb-6">
                        <li>Identitas pribadi (Nama, NIK, Tanggal Lahir).</li>
                        <li>Informasi kontak (Alamat, Nomor Telepon, Email).</li>
                        <li>Dokumen pendukung (KTP, KK, SKTM, dll).</li>
                        <li>Informasi kondisi ekonomi dan sosial.</li>
                    </ul>

                    <h3 className="text-xl font-bold text-slate-900 mt-8 mb-4">2. Penggunaan Informasi</h3>
                    <p>
                        Informasi yang kami kumpulkan digunakan semata-mata untuk keperluan:
                    </p>
                    <ul className="list-disc pl-5 space-y-2 mb-6">
                        <li>Memproses dan memverifikasi permohonan bantuan Anda.</li>
                        <li>Menghubungi Anda terkait status permohonan.</li>
                        <li>Pelaporan internal dan audit (dengan menyamarkan identitas jika diperlukan).</li>
                        <li>Mematuhi peraturan perundang-undangan yang berlaku.</li>
                    </ul>

                    <h3 className="text-xl font-bold text-slate-900 mt-8 mb-4">3. Keamanan Data</h3>
                    <p className="mb-6">
                        Kami menerapkan langkah-langkah keamanan teknis dan organisasional yang sesuai untuk melindungi data pribadi Anda dari akses, penggunaan, atau pengungkapan yang tidak sah.
                    </p>

                    <h3 className="text-xl font-bold text-slate-900 mt-8 mb-4">4. Berbagi Informasi</h3>
                    <p className="mb-6">
                        Kami tidak akan menjual, menyewakan, atau menukar informasi pribadi Anda dengan pihak ketiga mana pun. Kami hanya dapat membagikan informasi Anda kepada pihak berwenang jika diwajibkan oleh hukum.
                    </p>

                    <h3 className="text-xl font-bold text-slate-900 mt-8 mb-4">5. Perubahan Kebijakan</h3>
                    <p className="mb-6">
                        Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Perubahan akan berlaku segera setelah diposting di halaman ini.
                    </p>

                    <h3 className="text-xl font-bold text-slate-900 mt-8 mb-4">Hubungi Kami</h3>
                    <p>
                        Jika Anda memiliki pertanyaan tentang Kebijakan Privasi ini, silakan hubungi kami melalui email di info@lazsidogiri.org.
                    </p>
                </div>
            </div>
        </div>
    );
}
