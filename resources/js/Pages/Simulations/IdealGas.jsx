import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

const N_PARTICLES = 24;

export default function IdealGas({ category = 'Fisika' }) {
    const canvasRef = useRef(null);
    const particlesRef = useRef(
        Array.from({ length: N_PARTICLES }, () => ({
            x: Math.random(), y: Math.random(),
            vx: (Math.random() - 0.5), vy: (Math.random() - 0.5),
        }))
    );

    const [temperature, setTemperature] = useState(50);
    const [volume, setVolume] = useState(70);

    // Tekanan relatif, mengikuti bentuk hukum gas ideal P ∝ T / V (ilustratif)
    const relativePressure = ((temperature / 100) / (volume / 100)).toFixed(2);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        let raf;
        const speed = 0.5 + (temperature / 100) * 3;

        const draw = () => {
            const boxW = (volume / 100) * (canvas.width - 40);
            const boxH = canvas.height - 40;
            const boxX = (canvas.width - boxW) / 2;
            const boxY = 20;

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.strokeStyle = '#FBBF24';
            ctx.lineWidth = 2;
            ctx.strokeRect(boxX, boxY, boxW, boxH);

            particlesRef.current.forEach((p) => {
                p.x += p.vx * speed * 0.01;
                p.y += p.vy * speed * 0.01;
                if (p.x <= 0 || p.x >= 1) p.vx *= -1;
                if (p.y <= 0 || p.y >= 1) p.vy *= -1;
                p.x = Math.min(1, Math.max(0, p.x));
                p.y = Math.min(1, Math.max(0, p.y));

                const px = boxX + p.x * boxW;
                const py = boxY + p.y * boxH;
                ctx.fillStyle = '#8FD3FE';
                ctx.beginPath();
                ctx.arc(px, py, 5, 0, Math.PI * 2);
                ctx.fill();
            });

            raf = requestAnimationFrame(draw);
        };
        raf = requestAnimationFrame(draw);
        return () => cancelAnimationFrame(raf);
    }, [temperature, volume]);

    return (
        <LabLayout title="Termodinamika Gas Ideal" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={420} height={340} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Suhu</span><span className="dial-readout">{temperature}%</span></div>
                        <input type="range" min={10} max={100} value={temperature} onChange={(e) => setTemperature(Number(e.target.value))} className="w-full accent-amber" />
                        <p className="text-xs text-slate/70 mt-1">Suhu naik → partikel bergerak lebih cepat.</p>
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Volume</span><span className="dial-readout">{volume}%</span></div>
                        <input type="range" min={30} max={100} value={volume} onChange={(e) => setVolume(Number(e.target.value))} className="w-full accent-amber" />
                        <p className="text-xs text-slate/70 mt-1">Volume mengecil → partikel lebih rapat.</p>
                    </div>
                    <div className="pt-2 border-t border-slate/15">
                        <div className="flex justify-between"><span className="text-slate">Tekanan relatif (P ∝ T/V)</span><span className="dial-readout">{relativePressure}</span></div>
                    </div>
                </div>
            </div>
        </LabLayout>
    );
}
