import Link from "next/link";
import { ArrowLeft } from "lucide-react";

export default function TermsPage() {
    return (
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <Link href="/" className="inline-flex items-center text-slate-500 hover:text-laz-green-600 mb-8 transition-colors">
                <ArrowLeft className="w-4 h-4 mr-2" /> Kembali ke Beranda
            </Link>

            <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 md:p-12">
                <h1 className="text-3xl font-bold text-slate-900 mb-8">Syarat dan Ketentuan</h1>

                <div className="prose prose-slate max-w-none">
                    <p className="lead text-lg text-slate-600 mb-6">
                        Selamat datang di portal permohonan bantuan LAZ Sidogiri. Dengan menggunakan layanan ini, Anda setuju untuk terikat oleh Syarat dan Ketentuan berikut.
                    </p>

                    <h3 className="text-xl font-bold text-slate-900 mt-8 mb-4">1. Kelayakan Pemohon</h3>
                    <ul className="list-disc pl-5 space-y-2 mb-6">
                        <li>Pemohon harus merupakan Warga Negara Indonesia (WNI).</li>
                        <li>Pemohon harus termasuk dalam asnaf zakat (fakir, miskin, amil, mualaf, riqab, gharim, fisabilillah, atau ibnu sabil).</li>
                        <li>Pemohon tidak sedang menerima bantuan serupa dari lembaga lain untuk keperluan yang sama (kecuali dinyatakan lain).</li>
                    </ul>

                    <h3 className="text-xl font-bold text-slate-900 mt-8 mb-4">2. Kebenaran Data</h3>
                    <p className="mb-6">
                        Anda menjamin bahwa semua informasi dan dokumen yang Anda berikan adalah benar, akurat, dan terbaru. Pemalsuan data dapat mengakibatkan pembatalan permohonan dan tindakan hukum sesuai peraturan yang berlaku.
                    </p>

                    <h3 className="text-xl font-bold text-slate-900 mt-8 mb-4">3. Proses Verifikasi</h3>
                    <p className="mb-6">
                        LAZ Sidogiri berhak melakukan verifikasi faktual, termasuk survei ke lokasi tempat tinggal atau usaha pemohon, untuk memastikan kelayakan penerima bantuan.
                    </p>

                    <h3 className="text-xl font-bold text-slate-900 mt-8 mb-4">4. Keputusan Bantuan</h3>
                    <p className="mb-6">
                        Keputusan pemberian bantuan sepenuhnya merupakan hak prerogatif LAZ Sidogiri berdasarkan hasil asesmen dan ketersediaan dana. Keputusan ini bersifat mutlak dan tidak dapat diganggu gugat.
                    </p>

                    <h3 className="text-xl font-bold text-slate-900 mt-8 mb-4">5. Kewajiban Penerima</h3>
                    <p className="mb-6">
                        Penerima bantuan wajib menggunakan dana atau barang yang diterima sesuai dengan peruntukan yang disetujui dan bersedia memberikan laporan penggunaan jika diminta.
                    </p>

                    <h3 className="text-xl font-bold text-slate-900 mt-8 mb-4">6. Lain-lain</h3>
                    <p>
                        Syarat dan ketentuan ini dapat berubah sewaktu-waktu tanpa pemberitahuan sebelumnya. Penggunaan berkelanjutan atas layanan ini dianggap sebagai persetujuan terhadap perubahan tersebut.
                    </p>
                </div>
            </div>
        </div>
    );
}
