import { useMemo, useState } from 'react';
import { Link } from '@inertiajs/react';
import LabLayout from '../../Layouts/LabLayout';

const CATEGORIES = ['Semua', 'Fisika', 'Matematika', 'Biologi'];

// Palet kartu warna-warni cerah - dirotasi per modul supaya katalog terasa
// hidup, terinspirasi dari tampilan grid simulasi PhET.
const CARD_COLORS = [
    'bg-sky-400', 'bg-amber-400', 'bg-emerald-400', 'bg-rose-400',
    'bg-violet-400', 'bg-lime-400', 'bg-fuchsia-400', 'bg-orange-400',
    'bg-cyan-400', 'bg-pink-400',
];

const MAX_PREVIEW = 3;

export default function Index({ simulations = [] }) {
    const [activeCategory, setActiveCategory] = useState('Semua');

    const grouped = useMemo(() => {
        const byCategory = {};
        simulations.forEach((sim) => {
            byCategory[sim.category] = byCategory[sim.category] || [];
            byCategory[sim.category].push(sim);
        });
        return byCategory;
    }, [simulations]);

    const colorFor = (index) => CARD_COLORS[index % CARD_COLORS.length];

    return (
        <LabLayout title="Simulasi" breadcrumb={['Home', 'Simulasi']}>
            <p className="text-navy/60 mb-6 max-w-2xl">
                Simulasi sains interaktif yang bisa dijelajahi langsung di browser,
                terinspirasi dari PhET Interactive Simulations — tanpa perlu login.
            </p>

            {/* Tab kategori */}
            <div className="flex flex-wrap gap-2 mb-8">
                {CATEGORIES.map((cat) => (
                    <button
                        key={cat}
                        onClick={() => setActiveCategory(cat)}
                        className={`text-sm font-medium rounded-full px-4 py-2 transition-colors ${
                            activeCategory === cat
                                ? 'bg-teal text-white'
                                : 'bg-white border border-navy/15 text-navy/70 hover:border-teal/40'
                        }`}
                    >
                        {cat}
                    </button>
                ))}
            </div>

            {activeCategory === 'Semua' ? (
                // Mode ringkas: maks 3 kartu per kategori + tombol "lihat semua"
                Object.entries(grouped).map(([category, items]) => (
                    <div key={category} className="mb-10">
                        <div className="flex items-center justify-between mb-3">
                            <h2 className="font-display font-600 text-lg text-navy">{category}</h2>
                            {items.length > MAX_PREVIEW && (
                                <button
                                    onClick={() => setActiveCategory(category)}
                                    className="text-sm font-medium text-teal hover:underline"
                                >
                                    Lihat semua ({items.length}) &rarr;
                                </button>
                            )}
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                            {items.slice(0, MAX_PREVIEW).map((sim, i) => (
                                <SimCard key={sim.slug} sim={sim} color={colorFor(i)} />
                            ))}
                        </div>
                    </div>
                ))
            ) : (
                // Mode kategori terpilih: tampilkan semua modul kategori itu
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    {(grouped[activeCategory] || []).map((sim, i) => (
                        <SimCard key={sim.slug} sim={sim} color={colorFor(i)} />
                    ))}
                </div>
            )}
        </LabLayout>
    );
}

function SimCard({ sim, color }) {
    return (
        <Link
            href={`/lab/${sim.slug}`}
            className="group relative overflow-hidden rounded-2xl bg-white border border-navy/10 flex flex-col min-h-[190px] transition-all hover:-translate-y-1 hover:shadow-lg"
        >
            <div className={`h-3 w-full ${color}`} />
            <div className="p-5 flex flex-col flex-1 justify-between">
                <div>
                    <div className="flex items-center justify-between">
                        <span className="text-xs font-mono text-navy/40">{sim.subject}</span>
                        {sim.status === 'segera' && (
                            <span className="text-[10px] font-mono uppercase tracking-wide bg-navy/5 text-navy/40 rounded-full px-2 py-0.5">
                                Segera
                            </span>
                        )}
                    </div>
                    <h3 className="font-display font-600 text-lg text-navy mt-1.5">{sim.title}</h3>
                    <p className="text-sm text-navy/60 mt-2">{sim.desc}</p>
                </div>
                <div className="mt-4">
                    <span className="text-sm font-medium text-teal group-hover:underline">
                        Buka simulasi &rarr;
                    </span>
                </div>
            </div>
        </Link>
    );
}
