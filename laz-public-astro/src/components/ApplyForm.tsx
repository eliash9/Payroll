
import { useState, useEffect } from 'react';
import { useForm, useFieldArray } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import * as z from 'zod';
import { apiClient, fetcher } from '../lib/api';
import { AlertCircle, X } from 'lucide-react';

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
        file: z.any().refine((files: any) => files?.length === 1, 'File wajib diunggah')
    })).min(1, 'Minimal unggah 1 dokumen pendukung'),


    // Beneficiaries Logic
    is_applicant_beneficiary: z.string().or(z.boolean()),
    beneficiaries: z.array(z.object({
        name: z.string().min(1, 'Nama penerima wajib diisi'),
        national_id: z.string().optional(),
        address: z.string().optional(),
        phone: z.string().optional(),
        description: z.string().optional(),
    })).optional(),

    terms_accepted: z.literal(true, {
        errorMap: () => ({ message: 'Anda harus menyetujui Syarat dan Ketentuan' }),
    }),
    website: z.string().optional(), // Honeypot field
}).refine((data) => {
    // Custom validation
    const isSelf = data.is_applicant_beneficiary === 'true' || data.is_applicant_beneficiary === true;
    if (!isSelf && (!data.beneficiaries || data.beneficiaries.length === 0)) {
        return false;
    }
    return true;
}, {
    message: "Wajib mengisi data penerima bantuan jika bukan untuk diri sendiri",
    path: ["beneficiaries"],
});




type ApplicationForm = z.infer<typeof applicationSchema>;


interface Program {
    id: number;
    name: string;
    active_periods: { id: number; name: string }[];
    specific_requirements?: string;
    required_documents?: string[];
}


export default function ApplyForm() {
    const [program, setProgram] = useState<Program | null>(null);
    const [loading, setLoading] = useState(true);
    const [submitting, setSubmitting] = useState(false);
    const [error, setError] = useState<string | null>(null);



    const { register, control, handleSubmit, watch, formState: { errors } } = useForm<ApplicationForm>({
        resolver: zodResolver(applicationSchema),
        defaultValues: {
            is_applicant_beneficiary: "true",
            documents: [{ type: 'KTP', file: undefined }]
        }
    });

    const isApplicantBeneficiary = watch('is_applicant_beneficiary');

    const { fields, append, remove, replace } = useFieldArray({
        control,
        name: "documents"
    });

    const { fields: beneficiaryFields, append: appendBeneficiary, remove: removeBeneficiary } = useFieldArray({
        control,
        name: "beneficiaries"
    });



    useEffect(() => {
        // Get programId from URL search params
        const searchParams = new URLSearchParams(window.location.search);
        const programId = searchParams.get('programId');


        if (programId) {
            fetcher(`/programs/${programId}`)
                .then((data) => {
                    const prog = data.data;
                    setProgram(prog);

                    // Auto-fill required documents if available
                    if (prog.required_documents && Array.isArray(prog.required_documents) && prog.required_documents.length > 0) {
                        // Replace default KTP if there are specific requirements
                        // or just ensure they are present. Here we reset to match requirements.
                        const newDocs = prog.required_documents.map((docType: string) => ({
                            type: docType,
                            file: undefined
                        }));

                        // We need to use setValue properly here, but useFieldArray is controlled.
                        // So we can replace the fields.
                        // This part is tricky with react-hook-form's useFieldArray outside render cycle/event,
                        // but since we just loaded, we can try to reset the form docs or use replace.
                        // However, replace comes from useFieldArray.
                        // Let's defer this logic to a useEffect that watches 'program'.
                    }

                    setLoading(false);
                })
                .catch((err) => {
                    console.error(err);
                    setError('Gagal memuat data program');
                    setLoading(false);
                });
        } else {
            setError('Program tidak ditemukan (ID tidak ada)');
            setLoading(false);
        }
    }, []);

    // Effect to update documents based on program requirements
    useEffect(() => {
        if (program && program.required_documents && Array.isArray(program.required_documents) && program.required_documents.length > 0) {
            const newDocs = program.required_documents.map((docType: string) => ({
                type: docType,
                file: undefined
            }));
            // Use replace from useFieldArray context? We can't access it here easily without destructuring it again or moving this up.
            // Actually 'replace' is returned by useFieldArray.
            replace(newDocs);
        }
    }, [program]); // This consumes the 'program' state when it loads.


    const onSubmit = async (data: ApplicationForm) => {
        // Honeypot check
        if (data.website) {
            console.log('Bot detected');
            return;
        }

        setSubmitting(true);
        setError(null);

        const searchParams = new URLSearchParams(window.location.search);
        const programId = searchParams.get('programId');

        try {
            const formData = new FormData();
            formData.append('program_id', programId as string);
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



            const isApplicantBeneficiaryInfo = data.is_applicant_beneficiary === 'true' || data.is_applicant_beneficiary === true;
            formData.append('is_applicant_beneficiary', isApplicantBeneficiaryInfo ? '1' : '0');

            if (!isApplicantBeneficiaryInfo) {
                data.beneficiaries?.forEach((beneficiary, index) => {
                    formData.append(`beneficiaries[${index}][name]`, beneficiary.name);
                    if (beneficiary.national_id) formData.append(`beneficiaries[${index}][national_id]`, beneficiary.national_id);
                    if (beneficiary.address) formData.append(`beneficiaries[${index}][address]`, beneficiary.address);
                    if (beneficiary.phone) formData.append(`beneficiaries[${index}][phone]`, beneficiary.phone);
                    if (beneficiary.description) formData.append(`beneficiaries[${index}][description]`, beneficiary.description);
                });
            }

            data.documents.forEach((doc, index) => {
                formData.append(`documents[${index}][type]`, doc.type);
                formData.append(`documents[${index}][file]`, doc.file[0]);
            });

            await apiClient.post('/applications', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            window.location.href = '/success';
        } catch (err: any) {
            console.error(err);
            setError(err.response?.data?.message || 'Terjadi kesalahan saat mengirim permohonan. Silakan coba lagi.');
            setSubmitting(false);
        }
    };

    if (loading) return <div className="py-20 text-center">Memuat...</div>;
    if (!program && !loading) return <div className="py-20 text-center text-red-500">Program tidak ditemukan atau terjadi kesalahan. Silakan kembali dan pilih program lagi.</div>;

    return (
        <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div className="mb-8">
                <h1 className="text-2xl font-bold text-slate-900">Formulir Pengajuan Bantuan</h1>
                {program && <p className="text-slate-600">Program: <span className="font-semibold text-laz-green-600">{program.name}</span></p>}
            </div>


            {program?.specific_requirements && (
                <div className="bg-blue-50 border border-blue-200 text-blue-800 p-4 rounded-lg mb-6">
                    <h4 className="font-semibold mb-2 flex items-center gap-2">
                        <AlertCircle className="w-5 h-5" /> Syarat & Ketentuan Khusus
                    </h4>
                    <div className="prose prose-sm prose-blue max-w-none">
                        <p>{program.specific_requirements}</p>
                    </div>
                </div>
            )}

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
                        <select {...register('program_period_id')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4 border">
                            <option value="">-- Pilih Periode --</option>
                            {program?.active_periods.map((p) => (
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
                            <input type="text" {...register('national_id')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4 border" placeholder="16 digit NIK" />
                            {errors.national_id && <p className="text-red-500 text-xs mt-1">{errors.national_id.message}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                            <input type="text" {...register('full_name')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4 border" />
                            {errors.full_name && <p className="text-red-500 text-xs mt-1">{errors.full_name.message}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Tanggal Lahir</label>
                            <input type="date" {...register('birth_date')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4 border" />
                            {errors.birth_date && <p className="text-red-500 text-xs mt-1">{errors.birth_date.message}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Nomor HP/WA</label>
                            <input type="text" {...register('phone')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4 border" placeholder="Contoh: 08123456789" />
                            {errors.phone && <p className="text-red-500 text-xs mt-1">{errors.phone.message}</p>}
                        </div>
                        <div className="sm:col-span-2">
                            <label className="block text-sm font-medium text-slate-700 mb-1">Alamat Lengkap</label>
                            <textarea {...register('address')} rows={3} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4 border" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan"></textarea>
                            {errors.address && <p className="text-red-500 text-xs mt-1">{errors.address.message}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Email (Opsional)</label>
                            <input type="email" {...register('email')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4 border" />
                            {errors.email && <p className="text-red-500 text-xs mt-1">{errors.email.message}</p>}
                        </div>
                    </div>
                </div>


                {/* Section 2.5: Penerima Manfaat */}
                <div className="space-y-4">
                    <h3 className="text-lg font-semibold text-slate-900 border-b pb-2">3. Data Penerima Manfaat</h3>


                    <div className="space-y-4">
                        <label className="block text-sm font-medium text-slate-700">Untuk siapa bantuan ini diajukan?</label>

                        <div className="flex gap-4">
                            <label className={`flex items-center gap-2 cursor-pointer border p-3 rounded-lg flex-1 transition-colors ${isApplicantBeneficiary === 'true' ? 'bg-laz-green-50 border-laz-green-200' : 'hover:bg-slate-50'}`}>
                                <input
                                    type="radio"
                                    value="true"
                                    {...register('is_applicant_beneficiary')}
                                    className="w-4 h-4 text-laz-green-600 focus:ring-laz-green-500"
                                />
                                <span className="text-sm font-medium text-slate-700">Diri Sendiri</span>
                            </label>
                            <label className={`flex items-center gap-2 cursor-pointer border p-3 rounded-lg flex-1 transition-colors ${isApplicantBeneficiary === 'false' ? 'bg-laz-green-50 border-laz-green-200' : 'hover:bg-slate-50'}`}>
                                <input
                                    type="radio"
                                    value="false"
                                    {...register('is_applicant_beneficiary')}
                                    className="w-4 h-4 text-laz-green-600 focus:ring-laz-green-500"
                                />
                                <span className="text-sm font-medium text-slate-700">Orang Lain / Lembaga</span>
                            </label>
                        </div>
                    </div>

                    {isApplicantBeneficiary === 'false' && (
                        <div className="mt-4 space-y-4 border-l-4 border-orange-100 pl-4">
                            <div className="flex justify-between items-center bg-orange-50 p-3 rounded-lg text-orange-800 text-sm">
                                <p>Silakan isi data penerima manfaat di bawah ini.</p>
                            </div>

                            {beneficiaryFields.map((field, index) => (
                                <div key={field.id} className="relative bg-white border border-slate-200 p-4 rounded-xl shadow-sm">
                                    <button type="button" onClick={() => removeBeneficiary(index)} className="absolute top-3 right-3 text-slate-400 hover:text-red-500 transition-colors">
                                        <X className="w-4 h-4" />
                                    </button>

                                    <h4 className="text-sm font-bold text-slate-500 mb-3 uppercase tracking-wide">Penerima #{index + 1}</h4>

                                    <div className="grid sm:grid-cols-2 gap-4">
                                        <div className="sm:col-span-2">
                                            <label className="block text-xs font-medium text-slate-500 mb-1">Nama Lengkap Penerima</label>
                                            <input type="text" {...register(`beneficiaries.${index}.name`)} className="w-full text-sm border-slate-300 rounded-lg p-2.5" placeholder="Nama sesuai KTP" />
                                            {errors.beneficiaries?.[index]?.name && <p className="text-red-500 text-xs mt-1">{errors.beneficiaries[index]?.name?.message}</p>}
                                        </div>
                                        <div>
                                            <label className="block text-xs font-medium text-slate-500 mb-1">NIK (Opsional)</label>
                                            <input type="text" {...register(`beneficiaries.${index}.national_id`)} className="w-full text-sm border-slate-300 rounded-lg p-2.5" placeholder="16 digit NIK" />
                                        </div>
                                        <div>
                                            <label className="block text-xs font-medium text-slate-500 mb-1">No HP (Opsional)</label>
                                            <input type="text" {...register(`beneficiaries.${index}.phone`)} className="w-full text-sm border-slate-300 rounded-lg p-2.5" />
                                        </div>
                                        <div className="sm:col-span-2">
                                            <label className="block text-xs font-medium text-slate-500 mb-1">Alamat Domisili Penerima</label>
                                            <input type="text" {...register(`beneficiaries.${index}.address`)} className="w-full text-sm border-slate-300 rounded-lg p-2.5" />
                                        </div>
                                        <div className="sm:col-span-2">
                                            <label className="block text-xs font-medium text-slate-500 mb-1">Keterangan / Kondisi</label>
                                            <textarea {...register(`beneficiaries.${index}.description`)} rows={2} className="w-full text-sm border-slate-300 rounded-lg p-2.5" placeholder="Contoh: Janda tidak mampu, Anak yatim, dll"></textarea>
                                        </div>
                                    </div>
                                </div>
                            ))}

                            <button
                                type="button"
                                onClick={() => appendBeneficiary({ name: '', national_id: '', address: '', phone: '', description: '' })}
                                className="w-full py-3 border-2 border-dashed border-laz-green-300 text-laz-green-600 rounded-xl hover:bg-laz-green-50 transition-colors font-medium flex items-center justify-center gap-2"
                            >
                                + Tambah Penerima
                            </button>
                            {errors.beneficiaries && <p className="text-red-500 text-sm text-center">{errors.beneficiaries.message}</p>}
                        </div>
                    )}
                </div>

                {/* Section 3: Detail Permohonan */}
                <div className="space-y-4">
                    <h3 className="text-lg font-semibold text-slate-900 border-b pb-2">4. Detail Permohonan</h3>
                    <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Jumlah Bantuan yang Diajukan (Rp)</label>
                        <input type="number" {...register('requested_amount')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4 border" placeholder="Contoh: 1000000" />
                        {errors.requested_amount && <p className="text-red-500 text-xs mt-1">{errors.requested_amount.message}</p>}
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-slate-700 mb-1">Deskripsi Kebutuhan</label>
                        <textarea {...register('need_description')} rows={5} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4 border" placeholder="Jelaskan secara rinci untuk apa bantuan ini akan digunakan..."></textarea>
                        {errors.need_description && <p className="text-red-500 text-xs mt-1">{errors.need_description.message}</p>}
                    </div>
                    <div className="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Provinsi Lokasi</label>
                            <input type="text" {...register('location_province')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4 border" />
                            {errors.location_province && <p className="text-red-500 text-xs mt-1">{errors.location_province.message}</p>}
                        </div>
                        <div>
                            <label className="block text-sm font-medium text-slate-700 mb-1">Kabupaten/Kota Lokasi</label>
                            <input type="text" {...register('location_regency')} className="w-full border-slate-300 rounded-lg shadow-sm focus:border-laz-green-500 focus:ring-laz-green-500 py-3 px-4 border" />
                            {errors.location_regency && <p className="text-red-500 text-xs mt-1">{errors.location_regency.message}</p>}
                        </div>
                    </div>
                </div>


                {/* Section 4: Dokumen */}
                <div className="space-y-4">
                    <h3 className="text-lg font-semibold text-slate-900 border-b pb-2">5. Dokumen Pendukung</h3>
                    <div className="space-y-3">
                        {fields.map((field, index) => (
                            <div key={field.id} className="flex gap-3 items-start bg-slate-50 p-3 rounded-lg border border-slate-200">
                                <div className="flex-grow grid sm:grid-cols-2 gap-3">
                                    <div>
                                        <label className="block text-xs font-medium text-slate-500 mb-1">Jenis Dokumen</label>
                                        <select {...register(`documents.${index}.type`)} className="w-full text-sm border-slate-300 rounded-md border p-1">
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
                            className="text-sm text-laz-green-600 hover:text-laz-green-700 font-medium flex items-center gap-1 cursor-pointer"
                        >
                            + Tambah Dokumen Lain
                        </button>
                    </div>
                </div>


                {/* Honeypot Field (Hidden) */}
                <input type="text" {...register('website')} className="hidden" tabIndex={-1} autoComplete="off" />

                <div className="pt-6 border-t border-slate-100">
                    <div className="mb-6 flex items-start gap-3">
                        <div className="flex items-center h-5">
                            <input
                                id="terms"
                                type="checkbox"
                                {...register('terms_accepted')}
                                className="w-4 h-4 text-laz-green-600 border-gray-300 rounded focus:ring-laz-green-500"
                            />
                        </div>
                        <div className="text-sm">
                            <label htmlFor="terms" className="font-medium text-slate-700">
                                Saya menyetujui Syarat dan Ketentuan
                            </label>
                            <p className="text-slate-500">
                                Saya menyatakan bahwa data yang saya isi adalah benar dan saya telah membaca serta menyetujui <a href="/terms" target="_blank" className="text-laz-green-600 hover:underline">Syarat dan Ketentuan</a> yang berlaku.
                            </p>
                            {errors.terms_accepted && <p className="text-red-500 text-xs mt-1">{errors.terms_accepted.message}</p>}
                        </div>
                    </div>

                    <button
                        type="submit"
                        disabled={submitting}
                        className="w-full bg-laz-green-500 hover:bg-laz-green-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-laz-green-900/10 transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        {submitting ? 'Mengirim...' : 'Kirim Permohonan'}
                    </button>
                </div>

            </form>
        </div>
    );
}
