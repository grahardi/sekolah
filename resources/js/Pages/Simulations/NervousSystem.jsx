import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function NervousSystem({ category = 'Biologi' }) {
    const canvasRef = useRef(null);
    const offsetRef = useRef(0);
    const [speed, setSpeed] = useState(50);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        let raf;
        const midY = canvas.height / 2;

        const draw = () => {
            offsetRef.current += speed / 20;

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Badan sel (soma)
            ctx.fillStyle = '#8FD3FE';
            ctx.beginPath();
            ctx.arc(60, midY, 30, 0, Math.PI * 2);
            ctx.fill();

            // Dendrit
            ctx.strokeStyle = '#8FD3FE';
            for (let i = -2; i <= 2; i++) {
                ctx.beginPath();
                ctx.moveTo(60 - 25, midY + i * 12);
                ctx.lineTo(20, midY + i * 20);
                ctx.stroke();
            }

            // Akson
            ctx.strokeStyle = '#7C8AA5';
            ctx.lineWidth = 6;
            ctx.beginPath();
            ctx.moveTo(90, midY);
            ctx.lineTo(380, midY);
            ctx.stroke();

            // Selubung mielin (segmen)
            ctx.fillStyle = '#3ABF6B';
            for (let x = 110; x < 370; x += 45) {
                ctx.fillRect(x, midY - 12, 30, 24);
            }

            // Impuls saraf bergerak sepanjang akson
            const impX = 90 + (offsetRef.current % 290);
            ctx.fillStyle = '#FBBF24';
            ctx.beginPath();
            ctx.arc(impX, midY, 8, 0, Math.PI * 2);
            ctx.fill();

            // Ujung akson (terminal)
            ctx.fillStyle = '#8FD3FE';
            ctx.beginPath();
            ctx.arc(390, midY, 12, 0, Math.PI * 2);
            ctx.fill();

            raf = requestAnimationFrame(draw);
        };
        raf = requestAnimationFrame(draw);
        return () => cancelAnimationFrame(raf);
    }, [speed]);

    return (
        <LabLayout title="Sistem Saraf Manusia" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={420} height={240} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Kecepatan impuls</span><span className="dial-readout">{speed}%</span></div>
                        <input type="range" min={10} max={100} value={speed} onChange={(e) => setSpeed(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <p className="text-xs text-slate/70">
                        Titik kuning = impuls saraf yang merambat dari badan sel ke ujung akson. Segmen hijau =
                        selubung mielin yang mempercepat rambatan impuls.
                    </p>
                </div>
            </div>
        </LabLayout>
    );
}
