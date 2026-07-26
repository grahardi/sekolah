import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

const f = (x) => 0.02 * x * x * x - 0.3 * x * x + x;
const fPrime = (x) => {
    const h = 0.0001;
    return (f(x + h) - f(x - h)) / (2 * h);
};

export default function DerivativeTangent({ category = 'Matematika' }) {
    const canvasRef = useRef(null);
    const [x0, setX0] = useState(5);

    const slope = fPrime(x0);
    const y0 = f(x0);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const originX = canvas.width / 2;
        const originY = canvas.height / 2;
        const scale = 12;
        const toPx = (x, y) => [originX + x * scale, originY - y * scale];

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.strokeStyle = '#7C8AA5';
        ctx.beginPath(); ctx.moveTo(0, originY); ctx.lineTo(canvas.width, originY); ctx.stroke();
        ctx.beginPath(); ctx.moveTo(originX, 0); ctx.lineTo(originX, canvas.height); ctx.stroke();

        // Kurva
        ctx.strokeStyle = '#8FD3FE';
        ctx.lineWidth = 2;
        ctx.beginPath();
        for (let px = 0; px <= canvas.width; px++) {
            const x = (px - originX) / scale;
            const [, py] = toPx(x, f(x));
            px === 0 ? ctx.moveTo(px, py) : ctx.lineTo(px, py);
        }
        ctx.stroke();

        // Garis singgung
        const [px0, py0] = toPx(x0, y0);
        const dx = 6;
        const [tx1, ty1] = toPx(x0 - dx, y0 - slope * dx);
        const [tx2, ty2] = toPx(x0 + dx, y0 + slope * dx);
        ctx.strokeStyle = '#FBBF24';
        ctx.lineWidth = 2.5;
        ctx.beginPath(); ctx.moveTo(tx1, ty1); ctx.lineTo(tx2, ty2); ctx.stroke();

        // Titik singgung
        ctx.fillStyle = '#E63946';
        ctx.beginPath(); ctx.arc(px0, py0, 5, 0, Math.PI * 2); ctx.fill();
    }, [x0, slope, y0]);

    return (
        <LabLayout title="Turunan & Garis Singgung" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={480} height={380} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <p className="text-paper">f(x) = 0.02x³ − 0.3x² + x</p>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Titik x₀</span><span className="dial-readout">{x0.toFixed(1)}</span></div>
                        <input type="range" min={-5} max={18} step={0.1} value={x0} onChange={(e) => setX0(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15 space-y-1">
                        <div className="flex justify-between"><span className="text-slate">f(x₀)</span><span className="dial-readout">{y0.toFixed(2)}</span></div>
                        <div className="flex justify-between"><span className="text-slate">f'(x₀) - kemiringan</span><span className="dial-readout">{slope.toFixed(2)}</span></div>
                    </div>
                </div>
            </div>
        </LabLayout>
    );
}
