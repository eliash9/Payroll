
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
                            <li><a href="/programs" className="hover:text-white transition-colors">Program Bantuan</a></li>
                            <li><a href="/status" className="hover:text-white transition-colors">Cek Status Permohonan</a></li>
                            <li><a href="/guide" className="hover:text-white transition-colors">Panduan Pengajuan</a></li>
                            <li><a href="/privacy" className="hover:text-white transition-colors">Kebijakan Privasi</a></li>
                            <li><a href="/terms" className="hover:text-white transition-colors">Syarat & Ketentuan</a></li>
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

