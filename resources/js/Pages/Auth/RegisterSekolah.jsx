import { useState } from 'react';
import { useForm, Link } from '@inertiajs/react';
import PublicNavbar from '../../Components/PublicNavbar';
import PublicFooter from '../../Components/PublicFooter';

export default function RegisterSekolah() {
    const [step, setStep] = useState(1);
    const [npsnInput, setNpsnInput] = useState('');
    const [searching, setSearching] = useState(false);
    const [searchError, setSearchError] = useState(null);
    const [schoolData, setSchoolData] = useState(null);

    const { data, setData, post, processing, errors, reset } = useForm({
        npsn: '',
        nama_sekolah: '',
        alamat: '',
        kecamatan: '',
        kabupaten_kota: '',
        provinsi: '',
        status_sekolah: '',
        bentuk_pendidikan: '',
        jenjang_pendidikan: '',
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const searchNpsn = async () => {
        setSearching(true);
        setSearchError(null);
        setSchoolData(null);
        try {
            const res = await fetch(`/npsn-lookup?npsn=${encodeURIComponent(npsnInput)}`, {
                headers: { Accept: 'application/json' },
            });
            const json = await res.json();
            if (!res.ok || !json.found) {
                setSearchError(json.message || 'NPSN tidak ditemukan.');
            } else {
                setSchoolData(json.data);
            }
        } catch (e) {
            setSearchError('Gagal menghubungi server. Coba lagi.');
        } finally {
            setSearching(false);
        }
    };

    const confirmSchool = () => {
        setData({
            ...data,
            npsn: schoolData.npsn,
            nama_sekolah: schoolData.nama || '',
            alamat: schoolData.alamat || '',
            kecamatan: schoolData.kecamatan || '',
            kabupaten_kota: schoolData.kabupaten_kota || '',
            provinsi: schoolData.provinsi || '',
            status_sekolah: schoolData.status_sekolah || '',
            bentuk_pendidikan: schoolData.bentuk_pendidikan || '',
            jenjang_pendidikan: schoolData.jenjang_pendidikan || '',
        });
        setStep(2);
    };

    const submit = (e) => {
        e.preventDefault();
        post('/registrasi-sekolah', { onFinish: () => reset('password', 'password_confirmation') });
    };

    return (
        <div className="min-h-screen bg-cream text-navy flex flex-col">
            <PublicNavbar />

            <div className="flex-1 max-w-2xl w-full mx-auto px-6 py-12">
                <h1 className="font-display font-700 text-2xl lg:text-3xl mb-2">Daftarkan Sekolahmu</h1>
                <p className="text-navy/60 mb-8">
                    Langkah 1: masukkan NPSN, kami ambilkan data sekolahmu secara otomatis dari
                    data referensi resmi Kemendikdasmen.
                </p>

                {step === 1 && (
                    <div className="rounded-2xl bg-white border border-navy/10 p-6">
                        <label className="text-sm font-medium text-navy mb-1.5 block">Nomor Pokok Sekolah Nasional (NPSN)</label>
                        <div className="flex gap-2">
                            <input
                                type="text"
                                inputMode="numeric"
                                value={npsnInput}
                                onChange={(e) => setNpsnInput(e.target.value.replace(/\D/g, ''))}
                                placeholder="Contoh: 20539267"
                                maxLength={10}
                                className="flex-1 rounded-lg border border-navy/15 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal/40"
                            />
                            <button
                                onClick={searchNpsn}
                                disabled={searching || npsnInput.length < 6}
                                className="bg-teal text-white font-medium rounded-lg px-5 py-2.5 text-sm disabled:opacity-50 hover:brightness-110"
                            >
                                {searching ? 'Mencari...' : 'Cari'}
                            </button>
                        </div>
                        <p className="text-xs text-navy/40 mt-2">
                            Tidak tahu NPSN sekolahmu? Cek di{' '}
                            <a href="https://referensi.data.kemendikdasmen.go.id/" target="_blank" rel="noopener noreferrer" className="text-teal underline">
                                referensi.data.kemendikdasmen.go.id
                            </a>
                        </p>

                        {searchError && (
                            <p className="text-sm text-alert mt-4 bg-alert/10 rounded-lg px-3 py-2">{searchError}</p>
                        )}

                        {schoolData && (
                            <div className="mt-5 rounded-xl bg-teal-light p-4">
                                <p className="text-xs font-mono text-teal uppercase tracking-wide mb-2">Data ditemukan</p>
                                <h3 className="font-display font-600 text-navy">{schoolData.nama}</h3>
                                <p className="text-sm text-navy/70 mt-1">
                                    {[schoolData.alamat, schoolData.kecamatan, schoolData.kabupaten_kota, schoolData.provinsi].filter(Boolean).join(', ')}
                                </p>
                                <p className="text-xs text-navy/50 mt-1">
                                    {[schoolData.bentuk_pendidikan, schoolData.status_sekolah].filter(Boolean).join(' · ')}
                                </p>
                                <button
                                    onClick={confirmSchool}
                                    className="mt-4 bg-coral text-navy font-medium rounded-lg px-5 py-2.5 text-sm hover:brightness-95"
                                >
                                    Ya, ini sekolah saya - Lanjutkan
                                </button>
                            </div>
                        )}
                    </div>
                )}

                {step === 2 && (
                    <form onSubmit={submit} className="rounded-2xl bg-white border border-navy/10 p-6 space-y-4">
                        <div className="rounded-xl bg-teal-light p-4 mb-2">
                            <p className="text-xs font-mono text-teal uppercase tracking-wide mb-1">Sekolah terpilih</p>
                            <p className="font-display font-600 text-navy">{data.nama_sekolah}</p>
                            <button type="button" onClick={() => setStep(1)} className="text-xs text-teal underline mt-1">
                                Ganti NPSN
                            </button>
                        </div>

                        <div>
                            <label className="text-sm font-medium text-navy mb-1 block">Nama Lengkap (Admin Sekolah)</label>
                            <input
                                type="text" value={data.name} onChange={(e) => setData('name', e.target.value)}
                                className="w-full rounded-lg border border-navy/15 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal/40"
                            />
                            {errors.name && <p className="text-xs text-alert mt-1">{errors.name}</p>}
                        </div>
                        <div>
                            <label className="text-sm font-medium text-navy mb-1 block">Email</label>
                            <input
                                type="email" value={data.email} onChange={(e) => setData('email', e.target.value)}
                                className="w-full rounded-lg border border-navy/15 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal/40"
                            />
                            {errors.email && <p className="text-xs text-alert mt-1">{errors.email}</p>}
                        </div>
                        <div>
                            <label className="text-sm font-medium text-navy mb-1 block">Kata Sandi</label>
                            <input
                                type="password" value={data.password} onChange={(e) => setData('password', e.target.value)}
                                className="w-full rounded-lg border border-navy/15 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal/40"
                            />
                            {errors.password && <p className="text-xs text-alert mt-1">{errors.password}</p>}
                        </div>
                        <div>
                            <label className="text-sm font-medium text-navy mb-1 block">Konfirmasi Kata Sandi</label>
                            <input
                                type="password" value={data.password_confirmation} onChange={(e) => setData('password_confirmation', e.target.value)}
                                className="w-full rounded-lg border border-navy/15 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal/40"
                            />
                        </div>

                        <button
                            type="submit" disabled={processing}
                            className="w-full bg-teal text-white font-medium rounded-lg py-3 text-sm disabled:opacity-50 hover:brightness-110"
                        >
                            {processing ? 'Mendaftarkan...' : 'Daftarkan Sekolah & Buat Akun'}
                        </button>
                    </form>
                )}

                <p className="text-sm text-navy/50 mt-6">
                    Sudah punya akun? <Link href="/login" className="text-teal font-medium">Masuk di sini</Link>
                </p>
            </div>

            <PublicFooter />
        </div>
    );
}
