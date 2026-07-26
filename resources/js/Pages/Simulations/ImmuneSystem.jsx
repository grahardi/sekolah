import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function ImmuneSystem({ category = 'Biologi' }) {
    const canvasRef = useRef(null);
    const stateRef = useRef({ pathogens: [], antibodies: [] });
    const [immuneStrength, setImmuneStrength] = useState(60);
    const [infected, setInfected] = useState(0);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        let raf;

        const draw = () => {
            const s = stateRef.current;

            if (Math.random() < 0.05 && s.pathogens.length < 15) {
                s.pathogens.push({ x: Math.random() * canvas.width, y: Math.random() * canvas.height, alive: true });
            }
            if (Math.random() < immuneStrength / 500) {
                s.antibodies.push({ x: canvas.width / 2, y: canvas.height / 2, target: null });
            }

            s.antibodies.forEach((ab) => {
                if (!ab.target || !ab.target.alive) {
                    ab.target = s.pathogens.find((p) => p.alive) || null;
                }
                if (ab.target) {
                    const dx = ab.target.x - ab.x, dy = ab.target.y - ab.y;
                    const dist = Math.hypot(dx, dy);
                    if (dist < 8) {
                        ab.target.alive = false;
                    } else {
                        ab.x += (dx / dist) * 2.5;
                        ab.y += (dy / dist) * 2.5;
                    }
                }
            });
            s.pathogens = s.pathogens.filter((p) => p.alive);
            s.antibodies = s.antibodies.filter((ab) => ab.target && ab.target.alive).slice(0, 10);

            setInfected(s.pathogens.length);

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            s.pathogens.forEach((p) => {
                ctx.fillStyle = '#E63946';
                ctx.beginPath();
                ctx.arc(p.x, p.y, 7, 0, Math.PI * 2);
                ctx.fill();
            });
            s.antibodies.forEach((ab) => {
                ctx.fillStyle = '#8FD3FE';
                ctx.beginPath();
                ctx.arc(ab.x, ab.y, 5, 0, Math.PI * 2);
                ctx.fill();
            });

            raf = requestAnimationFrame(draw);
        };
        raf = requestAnimationFrame(draw);
        return () => cancelAnimationFrame(raf);
    }, [immuneStrength]);

    return (
        <LabLayout title="Sistem Kekebalan Tubuh (Imun)" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={400} height={300} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Kekuatan imun</span><span className="dial-readout">{immuneStrength}%</span></div>
                        <input type="range" min={10} max={100} value={immuneStrength} onChange={(e) => setImmuneStrength(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15">
                        <div className="flex justify-between"><span className="text-slate">Patogen aktif</span><span className="dial-readout">{infected}</span></div>
                    </div>
                    <p className="text-xs text-slate/70">
                        Titik merah = patogen (virus/bakteri). Titik biru = antibodi/sel imun yang mengejar dan
                        menetralkan patogen. Makin kuat imun, makin cepat patogen dibersihkan.
                    </p>
                </div>
            </div>
        </LabLayout>
    );
}
