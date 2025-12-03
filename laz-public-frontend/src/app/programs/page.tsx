'use client';

import { useEffect, useState } from 'react';
import { fetcher } from '@/lib/api';
import Link from 'next/link';
import { Calendar, MapPin, Users } from 'lucide-react';

interface Period {
    id: number;
    name: string;
    open_at: string;
    close_at: string;
    is_open: boolean;
}

interface Program {
    id: number;
    name: string;
    category: string;
    description: string;
    coverage_scope: string;
    allowed_recipient_type: string;
    active_periods: Period[];
}

export default function ProgramsPage() {
    const [programs, setPrograms] = useState<Program[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetcher('/programs')
            .then((data) => {
                setPrograms(data.data);
                setLoading(false);
            })
            .catch((err) => {
                console.error(err);
                setLoading(false);
            });
    }, []);

    if (loading) {
        return (
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div className="animate-pulse space-y-8">
                    <div className="h-8 bg-slate-200 rounded w-1/4"></div>
                    <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {[1, 2, 3].map((i) => (
                            <div key={i} className="h-64 bg-slate-200 rounded-2xl"></div>
                        ))}
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div className="mb-12">
                <h1 className="text-3xl font-bold text-slate-900 mb-4">Program Bantuan</h1>
                <p className="text-slate-600 max-w-2xl">
                    Pilih program bantuan yang sesuai dengan kebutuhan Anda. Pastikan Anda membaca syarat dan ketentuan sebelum mengajukan permohonan.
                </p>
            </div>

            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                {programs.map((program) => (
                    <div key={program.id} className="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow flex flex-col">
                        <div className="p-6 flex-grow">
                            <div className="inline-block px-3 py-1 rounded-full bg-laz-green-50 text-laz-green-700 text-xs font-medium mb-4 uppercase tracking-wide">
                                {program.category}
                            </div>
                            <h3 className="text-xl font-bold text-slate-900 mb-3">{program.name}</h3>
                            <p className="text-slate-600 text-sm mb-6 line-clamp-3">
                                {program.description}
                            </p>

                            <div className="space-y-3">
                                <div className="flex items-center gap-3 text-sm text-slate-500">
                                    <MapPin className="w-4 h-4 text-slate-400" />
                                    <span>Cakupan: {program.coverage_scope}</span>
                                </div>
                                <div className="flex items-center gap-3 text-sm text-slate-500">
                                    <Users className="w-4 h-4 text-slate-400" />
                                    <span>Penerima: {program.allowed_recipient_type === 'individual' ? 'Individu' : program.allowed_recipient_type === 'organization' ? 'Lembaga' : 'Individu & Lembaga'}</span>
                                </div>
                            </div>
                        </div>

                        <div className="p-6 border-t border-slate-50 bg-slate-50/50 rounded-b-2xl">
                            {program.active_periods.length > 0 ? (
                                <div className="space-y-4">
                                    <div className="flex items-center gap-2 text-sm text-laz-green-700 font-medium">
                                        <Calendar className="w-4 h-4" />
                                        <span>{program.active_periods.length} Periode Aktif</span>
                                    </div>
                                    <Link
                                        href={`/apply?programId=${program.id}`}
                                        className="block w-full text-center bg-laz-green-500 hover:bg-laz-green-600 text-white font-medium py-2.5 rounded-lg transition-colors"
                                    >
                                        Ajukan Sekarang
                                    </Link>
                                </div>
                            ) : (
                                <div className="text-center py-2">
                                    <span className="text-slate-400 text-sm font-medium">Belum ada periode aktif</span>
                                </div>
                            )}
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}
