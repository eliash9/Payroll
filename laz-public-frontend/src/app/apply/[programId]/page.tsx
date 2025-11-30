'use client';

import { useEffect, useState } from 'react';
import { useForm, useFieldArray } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import { apiClient, fetcher } from '@/lib/api';
import { useRouter, useParams } from 'next/navigation';
import { AlertCircle, Upload, X } from 'lucide-react';

// Schema Validation
const applicationSchema = z.object({
    program_period_id: z.string().min(1, 'Pilih periode program'),
    national_id: z.string().min(16, 'NIK harus 16 digit').max(16, 'NIK harus 16 digit'),
    full_name: z.string().min(3, 'Nama lengkap wajib diisi'),
    birth_date: z.string().min(1, 'Tanggal lahir wajib diisi'),
    address: z.string().min(10, 'Alamat lengkap wajib diisi'),
    phone: z.string().min(10, 'Nomor HP tidak valid'),
    email: z.string().email('Email tidak valid').optional().or(z.literal('')),
    requested_amount: z.string().min(1, 'Jumlah wajib diisi').refine((val) => !isNaN(Number(val)) && Number(val) > 0, 'Jumlah harus lebih dari 0'),
    need_description: z.string().min(20, 'Jelaskan kebutuhan Anda minimal 20 karakter'),
    location_province: z.string().min(1, 'Provinsi wajib diisi'),
    location_regency: z.string().min(1, 'Kabupaten/Kota wajib diisi'),
    documents: z.array(z.object({
        type: z.string().min(1, 'Jenis dokumen wajib dipilih'),
        file: z.any().refine((files) => files?.length === 1, 'File wajib diunggah')
    })).min(1, 'Minimal unggah 1 dokumen pendukung'),
    website: z.string().optional(), // Honeypot field
});

type ApplicationForm = z.infer<typeof applicationSchema>;

interface Program {
    id: number;
    name: string;
    active_periods: { id: number; name: string }[];
}

export default function ApplyPage() {
    const params = useParams();
    const router = useRouter();
    const [program, setProgram] = useState<Program | null>(null);
    const [loading, setLoading] = useState(true);
    const [submitting, setSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const { register, control, handleSubmit, formState: { errors }, setValue, watch } = useForm<ApplicationForm>({
        resolver: zodResolver(applicationSchema),
        defaultValues: {
            documents: [{ type: 'KTP', file: undefined }]
        }
    });

    const { fields, append, remove } = useFieldArray({
        control,
        name: "documents"
    });

    useEffect(() => {
        if (params.programId) {
            fetcher(`/programs/${params.programId}`)
                .then((data) => {
                    setProgram(data.data);
                    setLoading(false);
                })
                .catch((err) => {
                    console.error(err);
                    setError('Gagal memuat data program');
                    setLoading(false);
                });
        }
    }, [params.programId]);

    const onSubmit = async (data: ApplicationForm) => {
        // Honeypot check
        if (data.website) {
            console.log('Bot detected');
            return;
        }

        setSubmitting(true);
        setError(null);

        try {
            const formData = new FormData();
            formData.append('program_id', params.programId as string);
            formData.append('program_period_id', data.program_period_id);
            formData.append('national_id', data.national_id);
            formData.append('full_name', data.full_name);
            formData.append('birth_date', data.birth_date);
            formData.append('address', data.address);
            formData.append('phone', data.phone);
            if (data.email) formData.append('email', data.email);
            formData.append('requested_amount', data.requested_amount.toString());
            formData.append('need_description', data.need_description);
            formData.append('location_province', data.location_province);
            formData.append('location_regency', data.location_regency);

            data.documents.forEach((doc, index) => {
                formData.append(`documents[${index}][type]`, doc.type);
                formData.append(`documents[${index}][file]`, doc.file[0]);
            });

            await apiClient.post('/applications', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            router.push('/success');
        } catch (err: any) {
            console.error(err);
            setError(err.response?.data?.message || 'Terjadi kesalahan saat mengirim permohonan. Silakan coba lagi.');
            setSubmitting(false);
        }
    };

    if (loading) return <div className="py-20 text-center">Memuat...</div>;
    if (!program) return <div className="py-20 text-center">Program tidak ditemukan</div>;

    return (
        <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div className="mb-8">
                <h1 className="text-2xl font-bold text-slate-900">Formulir Pengajuan Bantuan</h1>
                <p className="text-slate-600">Program: <span className="font-semibold text-laz-green-600">{program.name}</span></p>
            </div>

            {error && (
                <div className="bg-red-50 border border-red-200 text-red-700 p-4 rounded-lg mb-6 flex items-start gap-3">
                    <AlertCircle className="w-5 h-5 mt-0.5 flex-shrink-0" />
                    <p>{error}</p>
                </div>
            )}

            <form onSubmit={handleSubmit(onSubmit)} className="space-y-8 bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-100">

                {/* Section 1: Periode */}
                <div className="space-y-4">
                    <h3 className="text-lg font-semibold text-slate-900 border-b pb-2">1. Periode Program</h3>
                    <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Pilih Periode Aktif</label>
                        <select {...register('program_period_id')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4">
                            <option value="">-- Pilih Periode --</option>
                            {program.active_periods.map((p) => (
                                <option key={p.id} value={p.id}>{p.name}</option>
                            ))}
                        </select>
                        {errors.program_period_id && <p className="text-red-500 text-xs mt-1">{errors.program_period_id.message}</p>}
                    </div>
                </div>

                {/* Section 2: Data Diri */}
                <div className="space-y-4">
                    <h3 className="text-lg font-semibold text-slate-900 border-b pb-2">2. Data Diri Pemohon</h3>
                    <div className="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">NIK (KTP)</label>
                            <input type="text" {...register('national_id')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4" placeholder="16 digit NIK" />
                            {errors.national_id && <p className="text-red-500 text-xs mt-1">{errors.national_id.message}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                            <input type="text" {...register('full_name')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4" />
                            {errors.full_name && <p className="text-red-500 text-xs mt-1">{errors.full_name.message}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Tanggal Lahir</label>
                            <input type="date" {...register('birth_date')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4" />
                            {errors.birth_date && <p className="text-red-500 text-xs mt-1">{errors.birth_date.message}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Nomor HP/WA</label>
                            <input type="text" {...register('phone')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4" placeholder="Contoh: 08123456789" />
                            {errors.phone && <p className="text-red-500 text-xs mt-1">{errors.phone.message}</p>}
                        </div>
                        <div className="sm:col-span-2">
                            <label className="block text-sm font-medium text-slate-700 mb-1">Alamat Lengkap</label>
                            <textarea {...register('address')} rows={3} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan"></textarea>
                            {errors.address && <p className="text-red-500 text-xs mt-1">{errors.address.message}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Email (Opsional)</label>
                            <input type="email" {...register('email')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4" />
                            {errors.email && <p className="text-red-500 text-xs mt-1">{errors.email.message}</p>}
                        </div>
                    </div>
                </div>

                {/* Section 3: Detail Permohonan */}
                <div className="space-y-4">
                    <h3 className="text-lg font-semibold text-slate-900 border-b pb-2">3. Detail Permohonan</h3>
                    <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Jumlah Bantuan yang Diajukan (Rp)</label>
                        <input type="number" {...register('requested_amount')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4" placeholder="Contoh: 1000000" />
                        {errors.requested_amount && <p className="text-red-500 text-xs mt-1">{errors.requested_amount.message}</p>}
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Deskripsi Kebutuhan</label>
                        <textarea {...register('need_description')} rows={5} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4" placeholder="Jelaskan secara rinci untuk apa bantuan ini akan digunakan..."></textarea>
                        {errors.need_description && <p className="text-red-500 text-xs mt-1">{errors.need_description.message}</p>}
                    </div>
                    <div className="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Provinsi Lokasi</label>
                            <input type="text" {...register('location_province')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4" />
                            {errors.location_province && <p className="text-red-500 text-xs mt-1">{errors.location_province.message}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Kabupaten/Kota Lokasi</label>
                            <input type="text" {...register('location_regency')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4" />
                            {errors.location_regency && <p className="text-red-500 text-xs mt-1">{errors.location_regency.message}</p>}
                        </div>
                    </div>
                </div>

                {/* Section 4: Dokumen */}
                <div className="space-y-4">
                    <h3 className="text-lg font-semibold text-slate-900 border-b pb-2">4. Dokumen Pendukung</h3>
                    <div className="space-y-3">
                        {fields.map((field, index) => (
                            <div key={field.id} className="flex gap-3 items-start bg-slate-50 p-3 rounded-lg border border-slate-200">
                                <div className="flex-grow grid sm:grid-cols-2 gap-3">
                                    <div>
                                        <label className="block text-xs font-medium text-slate-500 mb-1">Jenis Dokumen</label>
                                        <select {...register(`documents.${index}.type`)} className="w-full text-sm border-slate-300 rounded-md">
                                            <option value="KTP">KTP</option>
                                            <option value="KK">Kartu Keluarga</option>
                                            <option value="SKTM">SKTM</option>
                                            <option value="Proposal">Proposal</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label className="block text-xs font-medium text-slate-500 mb-1">File (Max 5MB)</label>
                                        <input type="file" {...register(`documents.${index}.file`)} className="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-laz-green-50 file:text-laz-green-700 hover:file:bg-laz-green-100" />
                                    </div>
                                </div>
                                {index > 0 && (
                                    <button type="button" onClick={() => remove(index)} className="text-red-500 hover:text-red-700 p-1">
                                        <X className="w-5 h-5" />
                                    </button>
                                )}
                            </div>
                        ))}
                        {errors.documents && <p className="text-red-500 text-xs">{errors.documents.message}</p>}

                        <button
                            type="button"
                            onClick={() => append({ type: 'Lainnya', file: undefined })}
                            className="text-sm text-laz-green-600 hover:text-laz-green-700 font-medium flex items-center gap-1"
                        >
                            + Tambah Dokumen Lain
                        </button>
                    </div>
                </div>

                {/* Honeypot Field (Hidden) */}
                <input type="text" {...register('website')} className="hidden" tabIndex={-1} autoComplete="off" />

                <div className="pt-6 border-t border-slate-100">
                    <button
                        type="submit"
                        disabled={submitting}
                        className="w-full bg-laz-green-500 hover:bg-laz-green-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-laz-green-900/10 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {submitting ? 'Mengirim...' : 'Kirim Permohonan'}
                    </button>
                </div>
            </form>
        </div>
    );
}
