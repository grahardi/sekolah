import { useForm, Link } from '@inertiajs/react';
import PortalLayout from '../../Layouts/PortalLayout';

export default function Edit({ sekolah }) {
    const { data, setData, put, processing, errors } = useForm({
        nama: sekolah?.nama ?? '',
        alamat: sekolah?.alamat ?? '',
        telepon: sekolah?.telepon ?? '',
        email: sekolah?.email ?? '',
        website: sekolah?.website ?? '',
        kepala_sekolah_nama: sekolah?.kepala_sekolah_nama ?? '',
        kepala_sekolah_nip: sekolah?.kepala_sekolah_nip ?? '',
        kepala_sekolah_pangkat: sekolah?.kepala_sekolah_pangkat ?? '',
        kecamatan: sekolah?.kecamatan ?? '',
        kabupaten_kota: sekolah?.kabupaten_kota ?? '',
        provinsi: sekolah?.provinsi ?? '',
        status_sekolah: sekolah?.status_sekolah ?? '',
        bentuk_pendidikan: sekolah?.bentuk_pendidikan ?? '',
        kkm: sekolah?.kkm ?? 75,
        gemini_api_key: sekolah?.gemini_api_key ?? '',
    });

    const submit = (e) => {
        e.preventDefault();
        put('/profil-sekolah');
    };

    const field = (label, key, opts = {}) => (
        <div>
            <label className="text-sm font-medium text-navy mb-1.5 block">{label}</label>
            <input
                type="text"
                value={data[key]}
                onChange={(e) => setData(key, e.target.value)}
                className="w-full rounded-lg border border-navy/15 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal/40"
                {...opts}
            />
            {errors[key] && <p className="text-xs text-alert mt-1">{errors[key]}</p>}
        </div>
    );

    return (
        <PortalLayout title="Profil Sekolah" breadcrumb={['Portal', 'Profil Sekolah']}>
            <p className="text-navy/60 mb-6 max-w-xl">
                Data ini ditampilkan di beranda portal. NPSN tidak bisa diubah di sini karena
                itu identitas resmi dari data referensi Kemendikdasmen.
            </p>

            <form onSubmit={submit} className="rounded-2xl bg-white border border-navy/10 p-6 max-w-2xl space-y-4">
                <div>
                    <label className="text-sm font-medium text-navy mb-1.5 block">NPSN</label>
                    <input
                        type="text"
                        value={sekolah?.npsn ?? ''}
                        disabled
                        className="w-full rounded-lg border border-navy/10 bg-navy/5 px-4 py-2.5 text-sm text-navy/50"
                    />
                </div>

                {field('Nama Sekolah', 'nama')}
                {field('Alamat', 'alamat')}

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {field('Kecamatan', 'kecamatan')}
                    {field('Kabupaten/Kota', 'kabupaten_kota')}
                </div>
                {field('Provinsi', 'provinsi')}

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {field('Status Sekolah', 'status_sekolah', { placeholder: 'Negeri / Swasta' })}
                    {field('Bentuk Pendidikan', 'bentuk_pendidikan', { placeholder: 'SMP' })}
                </div>

                <div className="pt-4 mt-2 border-t border-navy/10">
                    <p className="text-sm font-semibold text-navy mb-3">Untuk Kop Surat Rapor</p>
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {field('Telepon Sekolah', 'telepon')}
                    {field('Email Sekolah', 'email')}
                </div>
                {field('Website Sekolah', 'website', { placeholder: 'contoh: sekolahku.sch.id' })}

                <div className="pt-4 mt-2 border-t border-navy/10">
                    <p className="text-sm font-semibold text-navy mb-3">Kepala Sekolah (utk tanda tangan rapor)</p>
                </div>
                {field('Nama Kepala Sekolah', 'kepala_sekolah_nama')}
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {field('NIP', 'kepala_sekolah_nip')}
                    {field('Pangkat/Golongan', 'kepala_sekolah_pangkat', { placeholder: 'mis. Pembina Utama Muda' })}
                </div>

                <div className="pt-4 mt-2 border-t border-navy/10">
                    <p className="text-sm font-semibold text-navy mb-3">Kriteria Ketuntasan Minimal (KKM)</p>
                </div>
                {field('KKM', 'kkm')}

                <div className="pt-4 mt-2 border-t border-navy/10">
                    <p className="text-sm font-semibold text-navy mb-1">Integrasi AI (Scan Dokumen)</p>
                    <p className="text-xs text-navy/50 mb-3">
                        Dipakai untuk fitur Scan KK/Akta otomatis. Kosongkan untuk pakai key default sekolah.co.id,
                        atau isi API key Gemini milikmu sendiri kalau punya.
                    </p>
                </div>
                {field('Gemini API Key (opsional)', 'gemini_api_key', { placeholder: 'Kosongkan untuk pakai default sekolah.co.id', type: 'password' })}

                <div className="flex gap-3 pt-2">
                    <button
                        type="submit"
                        disabled={processing}
                        className="bg-teal text-white font-medium rounded-lg px-5 py-2.5 text-sm hover:brightness-110 disabled:opacity-50"
                    >
                        {processing ? 'Menyimpan...' : 'Simpan Perubahan'}
                    </button>
                    <Link href="/dashboard" className="border border-navy/20 text-navy font-medium rounded-lg px-5 py-2.5 text-sm hover:bg-navy/5">
                        Batal
                    </Link>
                </div>
            </form>
        </PortalLayout>
    );
}
