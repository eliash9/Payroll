'use client';

import Link from 'next/link';
import { Menu, X } from 'lucide-react';
import Image from 'next/image';
import { useState } from 'react';

export default function Navbar() {
    const [isOpen, setIsOpen] = useState(false);

    return (
        <nav className="bg-white border-b border-slate-100 sticky top-0 z-50 shadow-sm">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between h-20">
                    <div className="flex items-center">
                        <Link href="/" className="flex-shrink-0 flex items-center gap-3">
                            <div className="relative h-12 w-40">
                                <Image
                                    src="/logo.png"
                                    alt="LAZ Sidogiri"
                                    fill
                                    sizes="160px"
                                    className="object-contain object-left"
                                    priority
                                />
                            </div>
                        </Link>
                    </div>

                    <div className="hidden md:flex md:items-center md:space-x-8">
                        <Link href="/" className="text-slate-600 hover:text-laz-green-600 px-3 py-2 text-sm font-medium transition-colors">
                            Beranda
                        </Link>
                        <Link href="/programs" className="text-slate-600 hover:text-laz-green-600 px-3 py-2 text-sm font-medium transition-colors">
                            Program
                        </Link>
                        <Link href="/status" className="text-slate-600 hover:text-laz-green-600 px-3 py-2 text-sm font-medium transition-colors">
                            Cek Status
                        </Link>
                        <Link
                            href="/programs"
                            className="bg-laz-green-500 hover:bg-laz-green-600 text-white px-6 py-2.5 rounded-full text-sm font-bold transition-all shadow-lg shadow-laz-green-500/20"
                        >
                            Ajukan Permohonan
                        </Link>
                    </div>

                    <div className="flex items-center md:hidden">
                        <button
                            onClick={() => setIsOpen(!isOpen)}
                            className="p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100"
                        >
                            {isOpen ? <X className="h-6 w-6" /> : <Menu className="h-6 w-6" />}
                        </button>
                    </div>
                </div>
            </div>

            {/* Mobile menu */}
            {isOpen && (
                <div className="md:hidden bg-white border-t border-slate-100">
                    <div className="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                        <Link
                            href="/"
                            className="block px-3 py-2 rounded-md text-base font-medium text-slate-600 hover:text-laz-green-600 hover:bg-slate-50"
                            onClick={() => setIsOpen(false)}
                        >
                            Beranda
                        </Link>
                        <Link
                            href="/programs"
                            className="block px-3 py-2 rounded-md text-base font-medium text-slate-600 hover:text-laz-green-600 hover:bg-slate-50"
                            onClick={() => setIsOpen(false)}
                        >
                            Program
                        </Link>
                        <Link
                            href="/status"
                            className="block px-3 py-2 rounded-md text-base font-medium text-slate-600 hover:text-laz-green-600 hover:bg-slate-50"
                            onClick={() => setIsOpen(false)}
                        >
                            Cek Status
                        </Link>
                        <div className="pt-4 pb-2">
                            <Link
                                href="/programs"
                                className="block w-full text-center bg-laz-green-500 hover:bg-laz-green-600 text-white px-6 py-3 rounded-full text-base font-bold transition-all shadow-lg shadow-laz-green-500/20"
                                onClick={() => setIsOpen(false)}
                            >
                                Ajukan Permohonan
                            </Link>
                        </div>
                    </div>
                </div>
            )}
        </nav>
    );
}
