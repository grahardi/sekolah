import { Link, usePage } from '@inertiajs/react';
import PortalLayout from '../Layouts/PortalLayout';

const MODULES = [
    {
        key: 'induk', title: 'Buku Induk Digital', desc: 'Terintegrasi Dapodik, siap cetak biodata & kartu siswa.',
        href: '/buku-induk', status: 'aktif', color: 'bg-teal', bg: 'bg-sky-100', external: true,
    },
    {
        key: 'kepegawaian', title: 'Kepegawaian', desc: 'Data pegawai, DUK, Kendali Pangkat, Gaji Berkala.',
        href: '/kepegawaian', status: 'aktif', color: 'bg-teal', bg: 'bg-emerald-100', external: true,
    },
    {
        key: 'bk', title: 'Program BK', desc: 'Survey/asesmen siswa (DCM, AUM), pantau progress pengisian.',
        href: '/bk', status: 'aktif', color: 'bg-teal', bg: 'bg-amber-100', external: true,
    },
    {
        key: 'lab', title: 'Lab Interaktif', desc: 'Simulasi sains interaktif untuk Fisika, Matematika, dan Biologi.',
        href: '/lab', status: 'aktif', color: 'bg-teal', bg: 'bg-violet-100',
    },
    {
        key: 'modul-ajar', title: 'Modul Ajar', desc: 'Perangkat ajar SMP Kurikulum Merdeka, siap unduh.',
        href: '/modul-ajar', status: 'aktif', color: 'bg-teal', bg: 'bg-rose-100',
    },
    {
        key: 'erapor', title: 'E-Rapor', desc: 'Wali kelas, guru pengajar, ekstrakurikuler, dan kokurikuler (P5).',
        href: '/erapor', status: 'aktif', color: 'bg-teal', bg: 'bg-cyan-100', external: true,
    },
    {
        key: 'ujian', title: 'Ujian Digital', desc: 'Ujian online terjadwal dengan bank soal dan pengawasan otomatis.',
        href: '#', status: 'segera', color: 'bg-navy', bg: 'bg-slate-100',
    },
    {
        key: 'manajemen', title: 'Manajemen Sekolah Digital', desc: 'Jadwal, keuangan, dan administrasi umum.',
        href: '#', status: 'segera', color: 'bg-navy', bg: 'bg-slate-100',
    },
    {
        key: 'sarpras', title: 'Program Sarpras', desc: 'Pendataan dan pengelolaan sarana-prasarana sekolah.',
        href: '#', status: 'segera', color: 'bg-navy', bg: 'bg-slate-100',
    },
];

export default function Dashboard({ stats, sekolah }) {
    const { props } = usePage();
    const user = props?.auth?.user;

    const STAT_CARDS = [
        { label: 'Total Siswa', value: stats?.total_siswa ?? 0, href: '/buku-induk', icon: 'ti-users', color: '#2563EB', bg: 'bg-sky-100' },
        { label: 'Total Pegawai', value: stats?.total_pegawai ?? 0, href: '/kepegawaian', icon: 'ti-id-badge-2', color: '#16A34A', bg: 'bg-emerald-100' },
        { label: 'Survey Dibuat', value: stats?.total_survey ?? 0, href: '/bk', icon: 'ti-clipboard-list', color: '#D97706', bg: 'bg-amber-100' },
        { label: 'Akun Pengguna', value: stats?.total_user ?? 0, href: '/pengguna', icon: 'ti-user-shield', color: '#7C3AED', bg: 'bg-violet-100' },
    ];

    return (
        <PortalLayout title="Beranda" breadcrumb={['Portal']}>
            {/* Banner sambutan */}
            <div className="rounded-2xl bg-teal text-cream p-6 lg:p-8 mb-6 relative overflow-hidden">
                <div className="relative z-10 max-w-lg">
                    <p className="text-cream/70 text-sm">Selamat datang kembali,</p>
                    <h2 className="font-display font-700 text-2xl lg:text-3xl mt-1">
                        {user?.name ?? 'Warga Sekolah'} 👋
                    </h2>
                    <p className="text-cream/70 mt-2 max-w-lg text-sm lg:text-base">
                        Semua kebutuhan akademik sekolahmu dalam satu portal.
                    </p>

                    {(() => {
                        const isAdmin = user?.role === 'admin';
                        const Wrapper = isAdmin ? Link : 'div';
                        const wrapperProps = isAdmin ? { href: '/profil-sekolah' } : {};
                        return (
                            <Wrapper
                                {...wrapperProps}
                                className={`block mt-5 bg-white/10 rounded-xl px-4 py-3.5 max-w-lg transition-colors ${isAdmin ? 'hover:bg-white/15' : ''}`}
                            >
                                <div className="flex items-center justify-between">
                                    <p className="font-display font-600 text-base">{sekolah?.nama ?? 'Nama sekolah belum diisi'}</p>
                                    {isAdmin && (
                                        <span className="text-xs text-cream/60 flex items-center gap-1">
                                            <i className="ti ti-pencil" style={{ fontSize: '13px' }} /> Edit
                                        </span>
                                    )}
                                </div>
                                <p className="text-cream/70 text-xs mt-1">
                                    {[sekolah?.alamat, sekolah?.kecamatan, sekolah?.kabupaten_kota, sekolah?.provinsi].filter(Boolean).join(', ') || 'Alamat belum diisi'}
                                </p>
                                <div className="flex gap-2 mt-2">
                                    {sekolah?.npsn && <span className="text-[10px] font-mono bg-white/15 rounded-full px-2 py-0.5">NPSN {sekolah.npsn}</span>}
                                    {sekolah?.status_sekolah && <span className="text-[10px] font-mono bg-white/15 rounded-full px-2 py-0.5">{sekolah.status_sekolah}</span>}
                                    {sekolah?.bentuk_pendidikan && <span className="text-[10px] font-mono bg-white/15 rounded-full px-2 py-0.5">{sekolah.bentuk_pendidikan}</span>}
                                </div>
                            </Wrapper>
                        );
                    })()}

                    {user?.role === 'admin' && (
                        <a href="/erapor/pengaturan-cetak" className="inline-flex items-center gap-1.5 mt-3 text-xs text-cream/80 hover:text-white transition-colors">
                            <i className="ti ti-settings" style={{ fontSize: '13px' }} /> Atur Kop Surat, Logo &amp; Watermark Rapor
                        </a>
                    )}
                </div>
                {/* Aksen dekoratif */}
                <div className="absolute -right-10 -bottom-10 w-56 h-56 rounded-full bg-white/5" />
                <div className="absolute right-16 -top-10 w-28 h-28 rounded-full bg-coral/20" />
            </div>

            {/* Statistik ringkas - data nyata dari sekolah yang login */}
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                {STAT_CARDS.map((s) => (
                    <a
                        key={s.label}
                        href={s.href}
                        className={`rounded-2xl ${s.bg} border border-black/5 p-5 hover:shadow-md hover:-translate-y-0.5 transition-all`}
                    >
                        <div className="h-9 w-9 rounded-lg flex items-center justify-center mb-3 bg-white/70">
                            <i className={`ti ${s.icon}`} style={{ color: s.color, fontSize: '18px' }} />
                        </div>
                        <p className="font-display font-700 text-2xl text-navy">{s.value}</p>
                        <p className="text-xs text-navy/60 mt-0.5">{s.label}</p>
                    </a>
                ))}
            </div>

            {/* Grid modul */}
            <h3 className="font-display font-600 text-lg text-navy mb-3">Modul Sekolah</h3>
            <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">
                {MODULES.map((m) => {
                    const cardClass = `rounded-2xl ${m.bg} border border-black/5 p-5 flex flex-col justify-between min-h-[150px] transition-all hover:shadow-md hover:-translate-y-0.5 ${
                        m.status === 'segera' ? 'opacity-60 pointer-events-none' : ''
                    }`;
                    const cardContent = (
                        <div>
                            <div className="flex items-center justify-between">
                                <span className={`h-2 w-2 rounded-full ${m.color}`} />
                                <span className="text-[10px] font-mono uppercase tracking-wide text-navy/40">
                                    {m.status === 'aktif' ? 'Aktif' : 'Segera hadir'}
                                </span>
                            </div>
                            <h4 className="font-display font-600 text-base text-navy mt-2">{m.title}</h4>
                            <p className="text-sm text-navy/60 mt-1">{m.desc}</p>
                        </div>
                    );

                    return m.external ? (
                        <a key={m.key} href={m.href} className={cardClass}>{cardContent}</a>
                    ) : (
                        <Link key={m.key} href={m.href} className={cardClass}>{cardContent}</Link>
                    );
                })}
            </div>

            {/* Pratinjau simulasi Lab */}
            <h3 className="font-display font-600 text-lg text-navy mb-3">Simulasi Terbaru di Lab</h3>
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {[
                    { slug: 'bandul', title: 'Bandul Sederhana', subject: 'Fisika · Getaran' },
                    { slug: 'gerak-peluru', title: 'Gerak Peluru', subject: 'Fisika · Kinematika' },
                    { slug: 'rangkaian-listrik', title: 'Rangkaian Listrik Seri', subject: 'Fisika · Listrik' },
                ].map((s) => (
                    <Link
                        key={s.slug}
                        href={`/lab/${s.slug}`}
                        className="rounded-2xl bg-ink text-paper p-5 flex flex-col justify-between min-h-[120px] hover:-translate-y-0.5 transition-transform"
                    >
                        <span className="text-xs font-mono text-slate">{s.subject}</span>
                        <div className="flex items-end justify-between mt-3">
                            <h4 className="font-display font-600">{s.title}</h4>
                            <span className="text-amber text-sm">&rarr;</span>
                        </div>
                    </Link>
                ))}
            </div>
        </PortalLayout>
    );
}
