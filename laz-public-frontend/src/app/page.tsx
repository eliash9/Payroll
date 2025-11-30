import Link from "next/link";
import { ArrowRight, CheckCircle, Heart, Shield, FileText, Search, Send } from "lucide-react";
import Image from "next/image";

export default function Home() {
  return (
    <div className="flex flex-col gap-16 pb-16">
      {/* Hero Section */}
      <section className="bg-laz-teal-900 text-white relative overflow-hidden min-h-[600px] flex items-center">
        {/* Background Image */}
        <div className="absolute inset-0 z-0">
          <Image
            src="https://images.unsplash.com/photo-1593113598332-cd288d649433?q=80&w=2070&auto=format&fit=crop"
            alt="Charity Activity"
            fill
            sizes="100vw"
            className="object-cover opacity-20"
            priority
          />
          <div className="absolute inset-0 bg-gradient-to-r from-laz-teal-900/95 via-laz-teal-900/80 to-laz-teal-900/30"></div>
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 w-full">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div className="max-w-2xl">
              <div className="inline-block px-4 py-2 bg-laz-green-500/20 border border-laz-green-400/30 rounded-full text-laz-green-300 font-medium text-sm mb-6 backdrop-blur-sm">
                Lembaga Amil Zakat Sidogiri
              </div>
              <h1 className="text-4xl lg:text-6xl font-bold mb-6 leading-tight">
                Menebar Manfaat, <br />
                <span className="text-laz-green-400">Memberdayakan Umat</span>
              </h1>
              <p className="text-lg text-laz-teal-100 mb-8 leading-relaxed max-w-lg">
                Salurkan kebaikan Anda atau ajukan permohonan bantuan dengan mudah, transparan, dan amanah melalui platform digital kami.
              </p>
              <div className="flex flex-wrap gap-4">
                <Link
                  href="/programs"
                  className="bg-laz-green-500 hover:bg-laz-green-600 text-white px-8 py-4 rounded-full font-bold transition-all shadow-lg shadow-laz-green-900/20 flex items-center gap-2 text-lg"
                >
                  Lihat Program <ArrowRight className="w-5 h-5" />
                </Link>
                <Link
                  href="/status"
                  className="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-8 py-4 rounded-full font-bold transition-all backdrop-blur-sm flex items-center gap-2 text-lg"
                >
                  <Search className="w-5 h-5" /> Cek Status
                </Link>
              </div>
            </div>
            <div className="hidden lg:block relative">
              <div className="relative w-full h-[500px] rounded-3xl overflow-hidden shadow-2xl border-4 border-white/10 transform rotate-2 hover:rotate-0 transition-transform duration-500">
                <Image
                  src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&auto=format&fit=crop"
                  alt="Helping Hands"
                  fill
                  sizes="(max-width: 768px) 100vw, 50vw"
                  className="object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-laz-teal-900/60 to-transparent"></div>
                <div className="absolute bottom-8 left-8 right-8">
                  <div className="bg-white/90 backdrop-blur-md p-4 rounded-xl shadow-lg">
                    <div className="flex items-center gap-4">
                      <div className="w-12 h-12 bg-laz-green-100 rounded-full flex items-center justify-center text-laz-green-600">
                        <Heart className="w-6 h-6 fill-current" />
                      </div>
                      <div>
                        <p className="text-laz-teal-900 font-bold">Terpercaya & Amanah</p>
                        <p className="text-laz-teal-700 text-sm">Menjangkau ribuan penerima manfaat</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* How It Works Section */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="text-center mb-16">
          <h2 className="text-3xl lg:text-4xl font-bold text-slate-900 mb-4">Cara Pengajuan Bantuan</h2>
          <p className="text-slate-600 max-w-2xl mx-auto">
            Ikuti langkah-langkah mudah berikut untuk mengajukan permohonan bantuan di LAZ Sidogiri.
          </p>
        </div>

        <div className="grid md:grid-cols-4 gap-8 relative">
          {/* Connector Line */}
          <div className="hidden md:block absolute top-12 left-0 w-full h-0.5 bg-slate-100 -z-10"></div>

          <div className="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative">
            <div className="w-16 h-16 bg-laz-green-100 text-laz-green-600 rounded-2xl flex items-center justify-center mb-6 text-2xl font-bold mx-auto md:mx-0">
              1
            </div>
            <h3 className="text-xl font-bold text-slate-900 mb-3">Pilih Program</h3>
            <p className="text-slate-600 text-sm leading-relaxed">
              Cari dan pilih program bantuan yang sesuai dengan kebutuhan dan kriteria Anda.
            </p>
          </div>

          <div className="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative">
            <div className="w-16 h-16 bg-laz-teal-100 text-laz-teal-600 rounded-2xl flex items-center justify-center mb-6 text-2xl font-bold mx-auto md:mx-0">
              2
            </div>
            <h3 className="text-xl font-bold text-slate-900 mb-3">Isi Formulir</h3>
            <p className="text-slate-600 text-sm leading-relaxed">
              Lengkapi data diri dan dokumen persyaratan yang diminta secara online.
            </p>
          </div>

          <div className="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative">
            <div className="w-16 h-16 bg-laz-green-100 text-laz-green-600 rounded-2xl flex items-center justify-center mb-6 text-2xl font-bold mx-auto md:mx-0">
              3
            </div>
            <h3 className="text-xl font-bold text-slate-900 mb-3">Verifikasi</h3>
            <p className="text-slate-600 text-sm leading-relaxed">
              Tim kami akan melakukan verifikasi data dan survei lapangan jika diperlukan.
            </p>
          </div>

          <div className="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative">
            <div className="w-16 h-16 bg-laz-teal-100 text-laz-teal-600 rounded-2xl flex items-center justify-center mb-6 text-2xl font-bold mx-auto md:mx-0">
              4
            </div>
            <h3 className="text-xl font-bold text-slate-900 mb-3">Penyaluran</h3>
            <p className="text-slate-600 text-sm leading-relaxed">
              Jika disetujui, bantuan akan segera disalurkan kepada penerima manfaat.
            </p>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="bg-slate-50 py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid md:grid-cols-3 gap-8">
            <div className="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:border-laz-green-200 transition-colors group">
              <div className="w-14 h-14 bg-laz-green-50 text-laz-green-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-laz-green-500 group-hover:text-white transition-colors">
                <Shield className="w-7 h-7" />
              </div>
              <h3 className="text-xl font-bold text-slate-900 mb-3">Terpercaya & Amanah</h3>
              <p className="text-slate-600 leading-relaxed">
                Kami menjunjung tinggi nilai amanah dalam setiap pengelolaan dana zakat, infaq, dan sedekah.
              </p>
            </div>
            <div className="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:border-laz-green-200 transition-colors group">
              <div className="w-14 h-14 bg-laz-teal-50 text-laz-teal-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-laz-teal-600 group-hover:text-white transition-colors">
                <FileText className="w-7 h-7" />
              </div>
              <h3 className="text-xl font-bold text-slate-900 mb-3">Transparan</h3>
              <p className="text-slate-600 leading-relaxed">
                Setiap proses pengajuan dan penyaluran dapat dipantau statusnya secara real-time.
              </p>
            </div>
            <div className="bg-white p-8 rounded-2xl shadow-sm border border-slate-100 hover:border-laz-green-200 transition-colors group">
              <div className="w-14 h-14 bg-laz-green-50 text-laz-green-600 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-laz-green-500 group-hover:text-white transition-colors">
                <Heart className="w-7 h-7" />
              </div>
              <h3 className="text-xl font-bold text-slate-900 mb-3">Tepat Sasaran</h3>
              <p className="text-slate-600 leading-relaxed">
                Melalui proses asesmen yang ketat, kami memastikan bantuan sampai kepada yang benar-benar berhak.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-16">
        <div className="bg-gradient-to-br from-laz-teal-800 to-laz-teal-900 rounded-3xl p-12 text-center relative overflow-hidden shadow-2xl shadow-laz-teal-900/20">
          <div className="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-20 -mt-20"></div>
          <div className="absolute bottom-0 left-0 w-64 h-64 bg-laz-green-500/10 rounded-full blur-3xl -ml-20 -mb-20"></div>

          <div className="relative z-10">
            <h2 className="text-3xl md:text-4xl font-bold text-white mb-6">Siap Mengajukan Permohonan?</h2>
            <p className="text-laz-teal-100 mb-10 max-w-2xl mx-auto text-lg">
              Jangan ragu untuk menghubungi kami jika Anda memiliki pertanyaan seputar program bantuan.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link
                href="/programs"
                className="inline-block bg-laz-green-500 hover:bg-laz-green-400 text-white px-8 py-4 rounded-full font-bold transition-colors shadow-lg shadow-laz-green-900/20"
              >
                Mulai Pengajuan
              </Link>
              <Link
                href="/guide"
                className="inline-block bg-white/10 hover:bg-white/20 text-white border border-white/20 px-8 py-4 rounded-full font-bold transition-colors backdrop-blur-sm"
              >
                Pelajari Panduan
              </Link>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
