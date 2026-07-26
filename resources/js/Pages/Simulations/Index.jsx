import { Link } from '@inertiajs/react';
import PortalLayout from '../../Layouts/PortalLayout';

const SIMULATIONS = [
    {
        slug: 'bandul',
        title: 'Bandul Sederhana',
        subject: 'Fisika · Getaran',
        desc: 'Ubah panjang tali dan sudut awal, amati periode ayunan berubah secara real-time.',
        accent: 'bg-amber',
    },
    {
        slug: 'gerak-peluru',
        title: 'Gerak Peluru',
        subject: 'Fisika · Kinematika',
        desc: 'Atur sudut dan kecepatan awal, lihat lintasan parabola dan jarak jangkau.',
        accent: 'bg-lab',
    },
    {
        slug: 'rangkaian-listrik',
        title: 'Rangkaian Listrik Seri',
        subject: 'Fisika · Listrik',
        desc: 'Geser tegangan dan hambatan, lihat aliran elektron dan hitung arus lewat Hukum Ohm.',
        accent: 'bg-alert',
    },
];

export default function Index({ simulations = SIMULATIONS }) {
    return (
        <PortalLayout title="Lab Interaktif" breadcrumb={['Portal', 'Lab Interaktif']}>
            <p className="text-navy/60 mb-6 max-w-2xl">
                Simulasi sains yang bisa dijelajahi langsung di browser, terinspirasi dari
                PhET Interactive Simulations.
            </p>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                {simulations.map((sim) => (
                    <Link
                        key={sim.slug}
                        href={`/lab/${sim.slug}`}
                        className="group relative overflow-hidden rounded-2xl bg-ink text-paper p-5 flex flex-col justify-between min-h-[180px] transition-transform hover:-translate-y-1"
                    >
                        <div>
                            <span className="text-xs font-mono text-slate">{sim.subject}</span>
                            <h2 className="font-display font-600 text-xl mt-1">{sim.title}</h2>
                            <p className="text-sm text-paper/70 mt-2">{sim.desc}</p>
                        </div>
                        <div className="mt-4 flex items-center justify-between">
                            <span className="text-sm font-medium text-paper group-hover:text-amber transition-colors">
                                Buka simulasi &rarr;
                            </span>
                            <span className={`h-2 w-2 rounded-full ${sim.accent}`} />
                        </div>
                    </Link>
                ))}
            </div>
        </PortalLayout>
    );
}
