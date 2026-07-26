import { useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function Camouflage({ category = 'Biologi' }) {
    const [similarity, setSimilarity] = useState(50);
    const bgColor = '#3ABF6B';
    const animalColor = similarity > 50 ? '#4ACB7A' : '#E63946';
    const opacity = 0.3 + (similarity / 100) * 0.7;

    const survivalChance = Math.min(95, Math.max(5, similarity));

    return (
        <LabLayout title="Adaptasi Makhluk Hidup (Kamuflase)" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl border border-slate/20 flex items-center justify-center p-4" style={{ background: bgColor }}>
                    <svg viewBox="0 0 300 300" className="w-full max-w-sm">
                        {/* Tekstur dedaunan latar */}
                        {Array.from({ length: 20 }).map((_, i) => (
                            <ellipse key={i} cx={(i * 37) % 300} cy={(i * 53) % 300} rx="20" ry="8"
                                fill="#1B4332" fillOpacity="0.3"
                                transform={`rotate(${i * 17}, ${(i * 37) % 300}, ${(i * 53) % 300})`} />
                        ))}
                        {/* Hewan (belalang) */}
                        <ellipse cx="150" cy="150" rx="45" ry="20" fill={animalColor} fillOpacity={opacity} />
                        <circle cx="190" cy="145" r="10" fill={animalColor} fillOpacity={opacity} />
                    </svg>
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Kemiripan warna dgn lingkungan</span><span className="dial-readout">{similarity}%</span></div>
                        <input type="range" min={0} max={100} value={similarity} onChange={(e) => setSimilarity(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15">
                        <div className="flex justify-between"><span className="text-slate">Peluang lolos dari predator</span><span className="dial-readout">{survivalChance}%</span></div>
                    </div>
                    <p className="text-xs text-slate/70">
                        Kamuflase adalah adaptasi bentuk/warna tubuh menyerupai lingkungan untuk menghindari
                        predator. Makin mirip warna hewan dengan lingkungannya, makin sulit terlihat.
                    </p>
                </div>
            </div>
        </LabLayout>
    );
}
