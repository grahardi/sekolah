import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function CarbonCycle({ category = 'Biologi' }) {
    const canvasRef = useRef(null);
    const particlesRef = useRef([]);
    const [deforestation, setDeforestation] = useState(20);

    // Makin tinggi deforestasi, makin sedikit CO2 diserap tumbuhan -> makin banyak menumpuk di atmosfer
    const co2Level = Math.min(100, 30 + deforestation * 0.7);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        let raf;
        const treeX = 300, treeY = 250;
        const factoryX = 80, factoryY = 250;

        const draw = () => {
            if (Math.random() < 0.15) {
                particlesRef.current.push({ x: factoryX, y: factoryY, vy: -1.2, vx: 0.3, life: 150, type: 'co2' });
            }
            const absorbChance = Math.max(0.02, (1 - deforestation / 100) * 0.1);
            if (Math.random() < absorbChance) {
                particlesRef.current.push({ x: 50 + Math.random() * 300, y: 40, vy: 1, vx: (treeX - 200) / 150, life: 150, type: 'absorb' });
            }

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Tanah
            ctx.fillStyle = '#1B4332';
            ctx.fillRect(0, 260, canvas.width, 20);

            // Pabrik
            ctx.fillStyle = '#7C8AA5';
            ctx.fillRect(factoryX - 20, factoryY - 20, 40, 20);

            // Jumlah pohon berkurang sesuai deforestasi
            const treeCount = Math.max(1, Math.round((1 - deforestation / 100) * 4));
            for (let i = 0; i < treeCount; i++) {
                const tx = treeX - i * 40;
                ctx.fillStyle = '#3ABF6B';
                ctx.beginPath();
                ctx.arc(tx, treeY - 20, 22, 0, Math.PI * 2);
                ctx.fill();
                ctx.fillStyle = '#7C8AA5';
                ctx.fillRect(tx - 4, treeY - 5, 8, 20);
            }

            particlesRef.current = particlesRef.current.filter((p) => p.life > 0);
            particlesRef.current.forEach((p) => {
                p.x += p.vx; p.y += p.vy; p.life -= 1;
                ctx.fillStyle = p.type === 'co2' ? '#7C8AA5' : '#3ABF6B';
                ctx.beginPath();
                ctx.arc(p.x, p.y, 4, 0, Math.PI * 2);
                ctx.fill();
            });

            raf = requestAnimationFrame(draw);
        };
        raf = requestAnimationFrame(draw);
        return () => cancelAnimationFrame(raf);
    }, [deforestation]);

    return (
        <LabLayout title="Siklus Karbon & Oksigen" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={400} height={280} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Tingkat deforestasi</span><span className="dial-readout">{deforestation}%</span></div>
                        <input type="range" min={0} max={100} value={deforestation} onChange={(e) => setDeforestation(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15">
                        <div className="flex justify-between"><span className="text-slate">Kadar CO₂ atmosfer (relatif)</span><span className="dial-readout">{co2Level.toFixed(0)}%</span></div>
                    </div>
                    <p className="text-xs text-slate/70">
                        Titik abu-abu = CO₂ dari pembakaran (pabrik). Titik hijau = CO₂ yang diserap tumbuhan.
                        Makin sedikit pohon, makin banyak CO₂ menumpuk.
                    </p>
                </div>
            </div>
        </LabLayout>
    );
}
