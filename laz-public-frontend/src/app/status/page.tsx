'use client';

import { useState } from 'react';
import { useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import { apiClient } from '@/lib/api';
import { Search, AlertCircle, CheckCircle, Clock, FileText, User } from 'lucide-react';
import clsx from 'clsx';

const statusSchema = z.object({
    code: z.string().min(5, 'Kode tiket tidak valid'),
    national_id: z.string().min(16, 'NIK harus 16 digit').max(16, 'NIK harus 16 digit'),
});

type StatusForm = z.infer<typeof statusSchema>;

interface ApplicationData {
    code: string;
    applicant_name: string;
    program_name: string;
    status: string;
    status_label: string;
    created_at: string;
    updated_at: string;
}

export default function StatusPage() {
    const [result, setResult] = useState<ApplicationData | null>(null);
    const [error, setError] = useState<string | null>(null);
    const [loading, setLoading] = useState(false);

    const { register, handleSubmit, formState: { errors } } = useForm<StatusForm>({
        resolver: zodResolver(statusSchema)
    });

    const onSubmit = async (data: StatusForm) => {
        setLoading(true);
        setError(null);
        setResult(null);

        try {
            const response = await apiClient.post('/check-status', data);
            setResult(response.data.data);
        } catch (err: any) {
            console.error(err);
            setError(err.response?.data?.message || 'Data tidak ditemukan. Periksa kembali Kode Tiket dan NIK Anda.');
        } finally {
            setLoading(false);
        }
    };

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'approved': return 'bg-laz-green-100 text-laz-green-700 border-laz-green-200';
            case 'rejected': return 'bg-red-100 text-red-700 border-red-200';
            case 'completed': return 'bg-blue-100 text-blue-700 border-blue-200';
            default: return 'bg-amber-100 text-amber-700 border-amber-200';
        }
    };

    return (
        <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div className="text-center mb-12">
                <h1 className="text-3xl font-bold text-slate-900 mb-4">Cek Status Permohonan</h1>
                <p className="text-slate-600">
                    Masukkan Kode Tiket dan NIK Anda untuk melihat perkembangan terbaru dari permohonan bantuan Anda.
                </p>
            </div>

            <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 sm:p-8 mb-8">
                <form onSubmit={handleSubmit(onSubmit)} className="grid sm:grid-cols-3 gap-4">
                    <div className="sm:col-span-1">
                        <label className="block text-sm font-medium text-slate-700 mb-1">Kode Tiket</label>
                        <input
                            type="text"
                            {...register('code')}
                            placeholder="APP-2025-XXXXX"
                            className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 uppercase"
                        />
                        {errors.code && <p className="text-red-500 text-xs mt-1">{errors.code.message}</p>}
                    </div>
                    <div className="sm:col-span-1">
                        <label className="block text-sm font-medium text-slate-700 mb-1">NIK Pemohon</label>
                        <input
                            type="text"
                            {...register('national_id')}
                            placeholder="16 digit NIK"
                            className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500"
                        />
                        {errors.national_id && <p className="text-red-500 text-xs mt-1">{errors.national_id.message}</p>}
                    </div>
                    <div className="sm:col-span-1 flex items-end">
                        <button
                            type="submit"
                            disabled={loading}
                            className="w-full bg-laz-green-500 hover:bg-laz-green-600 text-white font-medium py-2.5 rounded-lg transition-colors flex items-center justify-center gap-2 disabled:opacity-70"
                        >
                            {loading ? 'Mencari...' : <><Search className="w-4 h-4" /> Cek Status</>}
                        </button>
                    </div>
                </form>
            </div>

            {error && (
                <div className="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl flex items-center gap-3 animate-in fade-in slide-in-from-top-2">
                    <AlertCircle className="w-5 h-5 flex-shrink-0" />
                    <p>{error}</p>
                </div>
            )}

            {result && (
                <div className="bg-white rounded-2xl shadow-lg border border-slate-100 overflow-hidden animate-in fade-in slide-in-from-bottom-4">
                    <div className="bg-slate-50 px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                        <h3 className="font-semibold text-slate-900">Detail Permohonan</h3>
                        <span className={clsx("px-3 py-1 rounded-full text-xs font-medium border", getStatusColor(result.status))}>
                            {result.status_label}
                        </span>
                    </div>
                    <div className="p-6 space-y-6">
                        <div className="grid sm:grid-cols-2 gap-6">
                            <div className="flex items-start gap-3">
                                <div className="p-2 bg-laz-green-50 text-laz-green-600 rounded-lg">
                                    <FileText className="w-5 h-5" />
                                </div>
                                <div>
                                    <p className="text-sm text-slate-500">Program Bantuan</p>
                                    <p className="font-medium text-slate-900">{result.program_name}</p>
                                    <p className="text-xs text-slate-400 mt-1">Kode: {result.code}</p>
                                </div>
                            </div>
                            <div className="flex items-start gap-3">
                                <div className="p-2 bg-laz-green-50 text-laz-green-600 rounded-lg">
                                    <User className="w-5 h-5" />
                                </div>
                                <div>
                                    <p className="text-sm text-slate-500">Nama Pemohon</p>
                                    <p className="font-medium text-slate-900">{result.applicant_name}</p>
                                </div>
                            </div>
                            <div className="flex items-start gap-3">
                                <div className="p-2 bg-laz-green-50 text-laz-green-600 rounded-lg">
                                    <Clock className="w-5 h-5" />
                                </div>
                                <div>
                                    <p className="text-sm text-slate-500">Tanggal Pengajuan</p>
                                    <p className="font-medium text-slate-900">{result.created_at}</p>
                                </div>
                            </div>
                            <div className="flex items-start gap-3">
                                <div className="p-2 bg-laz-green-50 text-laz-green-600 rounded-lg">
                                    <CheckCircle className="w-5 h-5" />
                                </div>
                                <div>
                                    <p className="text-sm text-slate-500">Update Terakhir</p>
                                    <p className="font-medium text-slate-900">{result.updated_at}</p>
                                </div>
                            </div>
                        </div>

                        <div className="bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-800">
                            <p className="font-medium mb-1">Informasi Tambahan:</p>
                            <p>
                                Status permohonan Anda saat ini adalah <strong>{result.status_label}</strong>.
                                Tim kami akan menghubungi Anda melalui nomor HP/WA yang terdaftar jika diperlukan verifikasi lebih lanjut.
                            </p>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
