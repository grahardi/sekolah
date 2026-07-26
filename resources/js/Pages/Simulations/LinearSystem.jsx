import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function LinearSystem({ category = 'Matematika' }) {
    const canvasRef = useRef(null);
    const [m1, setM1] = useState(1);
    const [c1, setC1] = useState(2);
    const [m2, setM2] = useState(-0.5);
    const [c2, setC2] = useState(-1);

    // Titik potong: m1 x + c1 = m2 x + c2
    const hasSolution = m1 !== m2;
    const xSolution = hasSolution ? (c2 - c1) / (m1 - m2) : null;
    const ySolution = hasSolution ? m1 * xSolution + c1 : null;

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const originX = canvas.width / 2;
        const originY = canvas.height / 2;
        const scale = 18;
        const toPx = (x, y) => [originX + x * scale, originY - y * scale];

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.strokeStyle = '#7C8AA5';
        ctx.beginPath(); ctx.moveTo(0, originY); ctx.lineTo(canvas.width, originY); ctx.stroke();
        ctx.beginPath(); ctx.moveTo(originX, 0); ctx.lineTo(originX, canvas.height); ctx.stroke();

        const drawLine = (m, c, color) => {
            ctx.strokeStyle = color;
            ctx.lineWidth = 2.5;
            ctx.beginPath();
            const x1 = -20, x2 = 20;
            const [px1, py1] = toPx(x1, m * x1 + c);
            const [px2, py2] = toPx(x2, m * x2 + c);
            ctx.moveTo(px1, py1);
            ctx.lineTo(px2, py2);
            ctx.stroke();
        };

        drawLine(m1, c1, '#8FD3FE');
        drawLine(m2, c2, '#FBBF24');

        if (hasSolution) {
            const [sx, sy] = toPx(xSolution, ySolution);
            ctx.fillStyle = '#E63946';
            ctx.beginPath(); ctx.arc(sx, sy, 6, 0, Math.PI * 2); ctx.fill();
        }
    }, [m1, c1, m2, c2, hasSolution, xSolution, ySolution]);

    return (
        <LabLayout title="Persamaan Linear Dua Variabel" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={480} height={400} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <p className="text-xs" style={{ color: '#8FD3FE' }}>Garis 1: y = m₁x + c₁</p>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>m₁</span><span className="dial-readout">{m1}</span></div>
                        <input type="range" min={-3} max={3} step={0.25} value={m1} onChange={(e) => setM1(Number(e.target.value))} className="w-full accent-amber" />
                        <div className="flex justify-between text-slate mb-1 mt-2"><span>c₁</span><span className="dial-readout">{c1}</span></div>
                        <input type="range" min={-10} max={10} value={c1} onChange={(e) => setC1(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <p className="text-xs" style={{ color: '#FBBF24' }}>Garis 2: y = m₂x + c₂</p>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>m₂</span><span className="dial-readout">{m2}</span></div>
                        <input type="range" min={-3} max={3} step={0.25} value={m2} onChange={(e) => setM2(Number(e.target.value))} className="w-full accent-amber" />
                        <div className="flex justify-between text-slate mb-1 mt-2"><span>c₂</span><span className="dial-readout">{c2}</span></div>
                        <input type="range" min={-10} max={10} value={c2} onChange={(e) => setC2(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15">
                        <p className="text-xs text-alert mb-1">Titik potong (solusi sistem)</p>
                        {hasSolution ? (
                            <div className="flex justify-between"><span className="text-slate">(x, y)</span><span className="dial-readout">({xSolution.toFixed(1)}, {ySolution.toFixed(1)})</span></div>
                        ) : (
                            <p className="text-slate text-sm">Garis sejajar - tidak ada solusi tunggal.</p>
                        )}
                    </div>
                </div>
            </div>
        </LabLayout>
    );
}
