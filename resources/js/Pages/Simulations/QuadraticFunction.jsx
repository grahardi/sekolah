import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function QuadraticFunction({ category = 'Matematika' }) {
    const canvasRef = useRef(null);
    const [a, setA] = useState(1);
    const [b, setB] = useState(0);
    const [c, setC] = useState(0);

    const discriminant = b * b - 4 * a * c;
    const vertexX = -b / (2 * a);
    const vertexY = a * vertexX * vertexX + b * vertexX + c;

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const originX = canvas.width / 2;
        const originY = canvas.height / 2;
        const scale = 20; // px per unit

        const toPx = (x, y) => [originX + x * scale, originY - y * scale];

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Grid & sumbu
        ctx.strokeStyle = 'rgba(124,138,165,0.2)';
        for (let gx = -10; gx <= 10; gx++) {
            const [px] = toPx(gx, 0);
            ctx.beginPath(); ctx.moveTo(px, 0); ctx.lineTo(px, canvas.height); ctx.stroke();
        }
        for (let gy = -8; gy <= 8; gy++) {
            const [, py] = toPx(0, gy);
            ctx.beginPath(); ctx.moveTo(0, py); ctx.lineTo(canvas.width, py); ctx.stroke();
        }
        ctx.strokeStyle = '#7C8AA5';
        ctx.lineWidth = 1.5;
        ctx.beginPath(); ctx.moveTo(0, originY); ctx.lineTo(canvas.width, originY); ctx.stroke();
        ctx.beginPath(); ctx.moveTo(originX, 0); ctx.lineTo(originX, canvas.height); ctx.stroke();

        // Kurva parabola
        ctx.strokeStyle = '#FBBF24';
        ctx.lineWidth = 2.5;
        ctx.beginPath();
        for (let px = 0; px <= canvas.width; px++) {
            const x = (px - originX) / scale;
            const y = a * x * x + b * x + c;
            const [, py] = toPx(x, y);
            px === 0 ? ctx.moveTo(px, py) : ctx.lineTo(px, py);
        }
        ctx.stroke();

        // Titik puncak
        const [vx, vy] = toPx(vertexX, vertexY);
        ctx.fillStyle = '#8FD3FE';
        ctx.beginPath(); ctx.arc(vx, vy, 5, 0, Math.PI * 2); ctx.fill();

        // Akar-akar (jika D >= 0)
        if (discriminant >= 0 && a !== 0) {
            const r1 = (-b + Math.sqrt(discriminant)) / (2 * a);
            const r2 = (-b - Math.sqrt(discriminant)) / (2 * a);
            ctx.fillStyle = '#E63946';
            [r1, r2].forEach((r) => {
                const [rx, ry] = toPx(r, 0);
                ctx.beginPath(); ctx.arc(rx, ry, 5, 0, Math.PI * 2); ctx.fill();
            });
        }
    }, [a, b, c, discriminant, vertexX, vertexY]);

    return (
        <LabLayout title="Fungsi Kuadrat Interaktif" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={480} height={400} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <p className="text-paper text-center">y = {a}x² + {b}x + {c}</p>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>a</span><span className="dial-readout">{a}</span></div>
                        <input type="range" min={-3} max={3} step={0.5} value={a} onChange={(e) => setA(Number(e.target.value) || 0.1)} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>b</span><span className="dial-readout">{b}</span></div>
                        <input type="range" min={-10} max={10} value={b} onChange={(e) => setB(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>c</span><span className="dial-readout">{c}</span></div>
                        <input type="range" min={-8} max={8} value={c} onChange={(e) => setC(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15 space-y-1">
                        <div className="flex justify-between"><span className="text-slate">Diskriminan</span><span className="dial-readout">{discriminant.toFixed(1)}</span></div>
                        <div className="flex justify-between"><span className="text-slate">Titik puncak</span><span className="dial-readout">({vertexX.toFixed(1)}, {vertexY.toFixed(1)})</span></div>
                    </div>
                </div>
            </div>
        </LabLayout>
    );
}
