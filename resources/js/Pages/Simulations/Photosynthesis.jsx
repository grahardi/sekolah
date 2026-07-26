import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function Photosynthesis({ category = 'Biologi' }) {
    const canvasRef = useRef(null);
    const particlesRef = useRef([]);
    const [light, setLight] = useState(60);
    const [co2, setCo2] = useState(50);

    const rate = ((light / 100) * (co2 / 100) * 10).toFixed(1);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        let raf;
        const leafX = canvas.width / 2;
        const leafY = canvas.height / 2;

        const spawnRate = Math.max(1, Math.round((light / 100) * (co2 / 100) * 6));

        const draw = () => {
            if (Math.random() < spawnRate / 30) {
                particlesRef.current.push({ x: leafX, y: leafY, vx: (Math.random() - 0.5) * 2, vy: -1 - Math.random(), life: 60, type: 'o2' });
            }
            if (Math.random() < (co2 / 100) * 0.3) {
                particlesRef.current.push({
                    x: Math.random() * canvas.width, y: 0,
                    vx: (leafX - Math.random() * canvas.width) / 80, vy: 1.5,
                    life: 80, type: 'co2',
                });
            }

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Daun sederhana
            ctx.fillStyle = `rgba(58,191,107,${0.4 + (light / 100) * 0.5})`;
            ctx.beginPath();
            ctx.ellipse(leafX, leafY, 70, 40, 0, 0, Math.PI * 2);
            ctx.fill();

            particlesRef.current = particlesRef.current.filter((p) => p.life > 0);
            particlesRef.current.forEach((p) => {
                p.x += p.vx; p.y += p.vy; p.life -= 1;
                ctx.fillStyle = p.type === 'o2' ? '#8FD3FE' : '#7C8AA5';
                ctx.beginPath();
                ctx.arc(p.x, p.y, 4, 0, Math.PI * 2);
                ctx.fill();
            });

            raf = requestAnimationFrame(draw);
        };
        raf = requestAnimationFrame(draw);
        return () => cancelAnimationFrame(raf);
    }, [light, co2]);

    return (
        <LabLayout title="Fotosintesis" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={400} height={340} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Intensitas cahaya</span><span className="dial-readout">{light}%</span></div>
                        <input type="range" min={0} max={100} value={light} onChange={(e) => setLight(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Konsentrasi CO₂</span><span className="dial-readout">{co2}%</span></div>
                        <input type="range" min={0} max={100} value={co2} onChange={(e) => setCo2(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15">
                        <div className="flex justify-between"><span className="text-slate">Laju fotosintesis (relatif)</span><span className="dial-readout">{rate}</span></div>
                    </div>
                    <p className="text-xs text-slate/70">
                        Titik abu-abu = CO₂ masuk, titik biru = O₂ keluar dari daun.
                    </p>
                </div>
            </div>
        </LabLayout>
    );
}
