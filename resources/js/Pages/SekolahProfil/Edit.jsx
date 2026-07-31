import { useForm, Link } from '@inertiajs/react';
import PortalLayout from '../../Layouts/PortalLayout';

export default function Edit({ sekolah }) {
    const { data, setData, put, processing, errors } = useForm({
        nama: sekolah?.nama ?? '',
        alamat: sekolah?.alamat ?? '',
        kecamatan: sekolah?.kecamatan ?? '',
        kabupaten_kota: sekolah?.kabupaten_kota ?? '',
        provinsi: sekolah?.provinsi ?? '',
        status_sekolah: sekolah?.status_sekolah ?? '',
        bentuk_pendidikan: sekolah?.bentuk_pendidikan ?? '',
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
