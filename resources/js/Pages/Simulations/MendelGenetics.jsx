import { useMemo, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

// Contoh sifat: Bulat (B, dominan) vs Keriput (b, resesif)
export default function MendelGenetics({ category = 'Biologi' }) {
    const [parent1, setParent1] = useState('Bb');
    const [parent2, setParent2] = useState('Bb');

    const alleles1 = parent1.split('');
    const alleles2 = parent2.split('');

    const offspring = useMemo(() => {
        const combos = [];
        alleles1.forEach((a1) => {
            alleles2.forEach((a2) => {
                const sorted = [a1, a2].sort((a, b) => (a === a.toUpperCase() ? -1 : 1)).join('');
                combos.push(sorted);
            });
        });
        const counts = {};
        combos.forEach((c) => { counts[c] = (counts[c] || 0) + 1; });
        return counts;
    }, [parent1, parent2]);

    const total = Object.values(offspring).reduce((a, b) => a + b, 0);

    return (
        <LabLayout title="Genetika Persilangan (Hukum Mendel)" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 p-6">
                    <p className="text-paper text-sm mb-4">
                        Sifat contoh: <strong>B</strong> = Bulat (dominan), <strong>b</strong> = Keriput (resesif)
                    </p>
                    <table className="w-full text-center border-collapse">
                        <thead>
                            <tr>
                                <th className="border border-slate/30 p-3 bg-white/5"></th>
                                {alleles2.map((a, i) => (
                                    <th key={i} className="border border-slate/30 p-3 bg-white/5 text-amber">{a}</th>
                                ))}
                            </tr>
                        </thead>
                        <tbody>
                            {alleles1.map((a1, i) => (
                                <tr key={i}>
                                    <td className="border border-slate/30 p-3 bg-white/5 text-amber">{a1}</td>
                                    {alleles2.map((a2, j) => {
                                        const sorted = [a1, a2].sort((a, b) => (a === a.toUpperCase() ? -1 : 1)).join('');
                                        return (
                                            <td key={j} className="border border-slate/30 p-3 text-paper font-mono">
                                                {sorted}
                                            </td>
                                        );
                                    })}
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <p className="text-slate text-sm mb-1">Genotipe induk 1</p>
                        <select value={parent1} onChange={(e) => setParent1(e.target.value)} className="w-full bg-[#0F1729] border border-slate/30 rounded-lg px-3 py-2 text-paper text-sm">
                            <option value="BB">BB (Bulat murni)</option>
                            <option value="Bb">Bb (Bulat heterozigot)</option>
                            <option value="bb">bb (Keriput)</option>
                        </select>
                    </div>
                    <div>
                        <p className="text-slate text-sm mb-1">Genotipe induk 2</p>
                        <select value={parent2} onChange={(e) => setParent2(e.target.value)} className="w-full bg-[#0F1729] border border-slate/30 rounded-lg px-3 py-2 text-paper text-sm">
                            <option value="BB">BB (Bulat murni)</option>
                            <option value="Bb">Bb (Bulat heterozigot)</option>
                            <option value="bb">bb (Keriput)</option>
                        </select>
                    </div>
                    <div className="pt-2 border-t border-slate/15 space-y-1">
                        <p className="text-xs text-slate/70 mb-1">Rasio genotipe keturunan:</p>
                        {Object.entries(offspring).map(([geno, count]) => (
                            <div key={geno} className="flex justify-between">
                                <span className="text-slate font-mono">{geno}</span>
                                <span className="dial-readout">{count}/{total}</span>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </LabLayout>
    );
}
