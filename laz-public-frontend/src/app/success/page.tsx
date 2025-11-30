import { CheckCircle } from 'lucide-react';
import Link from 'next/link';

export default function SuccessPage() {
    return (
        <div className="min-h-[60vh] flex items-center justify-center px-4">
            <div className="text-center max-w-lg">
                <div className="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <CheckCircle className="w-10 h-10" />
                </div>
                <h1 className="text-3xl font-bold text-slate-900 mb-4">Permohonan Berhasil Dikirim!</h1>
                <p className="text-slate-600 mb-8 leading-relaxed">
                    Terima kasih telah mengajukan permohonan. Data Anda telah kami terima dan akan segera diproses.
                    Silakan simpan kode tiket Anda untuk pengecekan status secara berkala.
                </p>

                <div className="bg-slate-50 border border-slate-200 rounded-lg p-4 mb-8">
                    <p className="text-sm text-slate-500 mb-1">Kode Tiket Anda (Simpan ini!)</p>
                    <p className="text-2xl font-mono font-bold text-slate-900 tracking-wider">APP-2025...</p>
                    <p className="text-xs text-slate-400 mt-2">*Kode lengkap ditampilkan di notifikasi atau email jika ada</p>
                </div>

                <div className="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        href="/status"
                        className="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-lg font-medium transition-colors"
                    >
                        Cek Status
                    </Link>
                    <Link
                        href="/"
                        className="bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 px-6 py-3 rounded-lg font-medium transition-colors"
                    >
                        Kembali ke Beranda
                    </Link>
                </div>
            </div>
        </div>
    );
}
