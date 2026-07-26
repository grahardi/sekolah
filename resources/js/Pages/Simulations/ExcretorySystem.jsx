import { useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function ExcretorySystem({ category = 'Biologi' }) {
    const [waterIntake, setWaterIntake] = useState(2000);

    // Ilustratif: makin banyak air masuk, makin banyak urin dihasilkan, konsentrasi makin encer
    const urineOutput = Math.round(waterIntake * 0.6);
    const concentration = Math.max(5, Math.round(100 - (waterIntake / 4000) * 80));

    return (
        <LabLayout title="Sistem Ekskresi (Ginjal)" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <svg viewBox="0 0 300 300" className="w-full max-w-sm">
                        {/* Ginjal */}
                        <ellipse cx="120" cy="120" rx="55" ry="80" fill="#E63946" fillOpacity="0.5" />
                        <ellipse cx="120" cy="120" rx="30" ry="50" fill="#7C8AA5" fillOpacity="0.6" />
                        {/* Ureter */}
                        <line x1="120" y1="200" x2="150" y2="260" stroke="#7C8AA5" strokeWidth="6" />
                        {/* Kandung kemih */}
                        <circle
                            cx="170" cy="270" r={16 + (urineOutput / 3000) * 20}
                            fill="#FBBF24" fillOpacity="0.6"
                        />
                        <text x="60" y="30" fill="#F7F5EF" fontSize="12">Ginjal (penyaring darah)</text>
                        <text x="175" y="285" fill="#F7F5EF" fontSize="11">Kandung kemih</text>
                    </svg>
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Asupan air harian</span><span className="dial-readout">{waterIntake} ml</span></div>
                        <input type="range" min={500} max={4000} step={100} value={waterIntake} onChange={(e) => setWaterIntake(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15 space-y-1">
                        <div className="flex justify-between"><span className="text-slate">Volume urin (ilustratif)</span><span className="dial-readout">{urineOutput} ml</span></div>
                        <div className="flex justify-between"><span className="text-slate">Konsentrasi urin</span><span className="dial-readout">{concentration}%</span></div>
                    </div>
                    <p className="text-xs text-slate/70">
                        Ginjal menyaring darah dan mengatur keseimbangan air tubuh. Makin banyak air diminum,
                        urin makin encer dan volumenya makin banyak.
                    </p>
                </div>
            </div>
        </LabLayout>
    );
}
