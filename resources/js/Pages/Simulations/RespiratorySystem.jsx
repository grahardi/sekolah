import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function RespiratorySystem({ category = 'Biologi' }) {
    const canvasRef = useRef(null);
    const tRef = useRef(0);
    const [breathRate, setBreathRate] = useState(15);
    const [phase, setPhase] = useState('Menarik napas');

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        let raf;

        const draw = () => {
            tRef.current += (breathRate / 60) * (1 / 20);
            const cyclePos = tRef.current % 1;
            const scale = 0.7 + 0.3 * Math.sin(cyclePos * Math.PI * 2);
            setPhase(Math.sin(cyclePos * Math.PI * 2) > 0 ? 'Menarik napas (inspirasi)' : 'Menghembus napas (ekspirasi)');

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            const cx = canvas.width / 2;
            const cy = canvas.height / 2;

            // Trakea
            ctx.strokeStyle = '#7C8AA5';
            ctx.lineWidth = 8;
            ctx.beginPath();
            ctx.moveTo(cx, cy - 120);
            ctx.lineTo(cx, cy - 40);
            ctx.stroke();

            // Paru-paru kiri & kanan, membesar-mengecil sesuai fase napas
            [-1, 1].forEach((side) => {
                ctx.fillStyle = `rgba(143,211,254,${0.35 + scale * 0.3})`;
                ctx.beginPath();
                ctx.ellipse(cx + side * 55, cy + 30, 45 * scale, 85 * scale, 0, 0, Math.PI * 2);
                ctx.fill();
            });

            ctx.fillStyle = '#F7F5EF';
            ctx.font = '12px monospace';
            ctx.fillText('Trakea', cx + 10, cy - 80);

            raf = requestAnimationFrame(draw);
        };
        raf = requestAnimationFrame(draw);
        return () => cancelAnimationFrame(raf);
    }, [breathRate]);

    return (
        <LabLayout title="Sistem Pernapasan" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={360} height={360} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Laju napas</span><span className="dial-readout">{breathRate}/menit</span></div>
                        <input type="range" min={8} max={40} value={breathRate} onChange={(e) => setBreathRate(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15">
                        <div className="flex justify-between"><span className="text-slate">Fase saat ini</span><span className="dial-readout text-sm">{phase}</span></div>
                    </div>
                    <p className="text-xs text-slate/70">Laju napas normal orang dewasa istirahat: 12-20 kali/menit.</p>
                </div>
            </div>
        </LabLayout>
    );
}
