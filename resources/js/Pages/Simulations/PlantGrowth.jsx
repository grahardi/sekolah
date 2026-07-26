import { useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function PlantGrowth({ category = 'Biologi' }) {
    const [sunlight, setSunlight] = useState(70);
    const [water, setWater] = useState(60);
    const [nutrient, setNutrient] = useState(50);

    const growthScore = (sunlight + water + nutrient) / 3;
    const height = 40 + (growthScore / 100) * 160;
    const leafCount = Math.max(2, Math.round(growthScore / 15));

    return (
        <LabLayout title="Pertumbuhan & Perkembangan Tanaman" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-end justify-center p-4">
                    <svg viewBox="0 0 200 260" className="h-full max-h-[260px]">
                        {/* Tanah */}
                        <rect x="0" y="240" width="200" height="20" fill="#7C8AA5" />
                        {/* Batang */}
                        <line x1="100" y1="240" x2="100" y2={240 - height} stroke="#3ABF6B" strokeWidth="6" />
                        {/* Daun */}
                        {Array.from({ length: leafCount }).map((_, i) => {
                            const y = 240 - (height / leafCount) * (i + 0.5);
                            const side = i % 2 === 0 ? 1 : -1;
                            return (
                                <ellipse key={i} cx={100 + side * 20} cy={y} rx="18" ry="9"
                                    fill="#4ACB7A"
                                    transform={`rotate(${side * 20}, ${100 + side * 20}, ${y})`} />
                            );
                        })}
                    </svg>
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Cahaya matahari</span><span className="dial-readout">{sunlight}%</span></div>
                        <input type="range" min={0} max={100} value={sunlight} onChange={(e) => setSunlight(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Air</span><span className="dial-readout">{water}%</span></div>
                        <input type="range" min={0} max={100} value={water} onChange={(e) => setWater(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Nutrisi tanah</span><span className="dial-readout">{nutrient}%</span></div>
                        <input type="range" min={0} max={100} value={nutrient} onChange={(e) => setNutrient(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15">
                        <div className="flex justify-between"><span className="text-slate">Skor pertumbuhan</span><span className="dial-readout">{growthScore.toFixed(0)}</span></div>
                    </div>
                </div>
            </div>
        </LabLayout>
    );
}
