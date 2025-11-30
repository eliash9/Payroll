import Link from 'next/link';

export default function Footer() {
    return (
        <footer className="bg-slate-900 text-slate-300 py-12">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="grid md:grid-cols-3 gap-8">
                    <div>
                        <h3 className="text-white text-lg font-bold mb-4">LAZ Sidogiri</h3>
                        <p className="text-sm leading-relaxed">
                            Lembaga Amil Zakat yang berkomitmen untuk memberdayakan umat melalui pengelolaan zakat, infaq, dan sedekah yang profesional dan amanah.
                        </p>
                    </div>
                    <div>
                        <h3 className="text-white text-lg font-bold mb-4">Tautan Cepat</h3>
                        <ul className="space-y-2 text-sm">
                            <li><Link href="/programs" className="hover:text-white transition-colors">Program Bantuan</Link></li>
                            <li><Link href="/status" className="hover:text-white transition-colors">Cek Status Permohonan</Link></li>
                            <li><Link href="/guide" className="hover:text-white transition-colors">Panduan Pengajuan</Link></li>
                            <li><Link href="/privacy" className="hover:text-white transition-colors">Kebijakan Privasi</Link></li>
                            <li><Link href="/terms" className="hover:text-white transition-colors">Syarat & Ketentuan</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h3 className="text-white text-lg font-bold mb-4">Kontak</h3>
                        <ul className="space-y-2 text-sm">
                            <li>Kantor Pusat Sidogiri</li>
                            <li>Jl. Raya Sidogiri No. 1</li>
                            <li>Pasuruan, Jawa Timur</li>
                            <li>Email: info@lazsidogiri.org</li>
                        </ul>
                    </div>
                </div>
                <div className="border-t border-slate-800 mt-12 pt-8 text-center text-sm text-slate-500">
                    &copy; {new Date().getFullYear()} LAZ Sidogiri. All rights reserved.
                </div>
            </div>
        </footer>
    );
}
