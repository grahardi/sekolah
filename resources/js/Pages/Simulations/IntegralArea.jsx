import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

const f = (x) => 0.03 * x * x + 1;

export default function IntegralArea({ category = 'Matematika' }) {
    const canvasRef = useRef(null);
    const [lowerBound, setLowerBound] = useState(-6);
    const [upperBound, setUpperBound] = useState(8);
    const [rectCount, setRectCount] = useState(10);

    const approxArea = (() => {
        const width = (upperBound - lowerBound) / rectCount;
        let sum = 0;
        for (let i = 0; i < rectCount; i++) {
            const xMid = lowerBound + width * (i + 0.5);
            sum += f(xMid) * width;
        }
        return sum;
    })();

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const originX = canvas.width / 2;
        const originY = canvas.height - 40;
        const scale = 14;
        const toPx = (x, y) => [originX + x * scale, originY - y * scale];

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.strokeStyle = '#7C8AA5';
        ctx.beginPath(); ctx.moveTo(0, originY); ctx.lineTo(canvas.width, originY); ctx.stroke();
        ctx.beginPath(); ctx.moveTo(originX, 0); ctx.lineTo(originX, canvas.height); ctx.stroke();

        // Persegi panjang Riemann
        const width = (upperBound - lowerBound) / rectCount;
        for (let i = 0; i < rectCount; i++) {
            const xMid = lowerBound + width * (i + 0.5);
            const xLeft = lowerBound + width * i;
            const height = f(xMid);
            const [pxL, pyTop] = toPx(xLeft, height);
            const [pxR, pyBase] = toPx(xLeft + width, 0);
            ctx.fillStyle = 'rgba(251,191,36,0.35)';
            ctx.strokeStyle = '#FBBF24';
            ctx.fillRect(pxL, pyTop, pxR - pxL, pyBase - pyTop);
            ctx.strokeRect(pxL, pyTop, pxR - pxL, pyBase - pyTop);
        }

        // Kurva
        ctx.strokeStyle = '#8FD3FE';
        ctx.lineWidth = 2.5;
        ctx.beginPath();
        for (let px = 0; px <= canvas.width; px++) {
            const x = (px - originX) / scale;
            const [, py] = toPx(x, f(x));
            px === 0 ? ctx.moveTo(px, py) : ctx.lineTo(px, py);
        }
        ctx.stroke();
    }, [lowerBound, upperBound, rectCount]);

    return (
        <LabLayout title="Integral sebagai Luas Daerah" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={480} height={340} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <p className="text-paper">f(x) = 0.03x² + 1</p>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Batas bawah (a)</span><span className="dial-readout">{lowerBound}</span></div>
                        <input type="range" min={-10} max={0} value={lowerBound} onChange={(e) => setLowerBound(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Batas atas (b)</span><span className="dial-readout">{upperBound}</span></div>
                        <input type="range" min={1} max={14} value={upperBound} onChange={(e) => setUpperBound(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Jumlah persegi panjang</span><span className="dial-readout">{rectCount}</span></div>
                        <input type="range" min={2} max={60} value={rectCount} onChange={(e) => setRectCount(Number(e.target.value))} className="w-full accent-amber" />
                        <p className="text-xs text-slate/70 mt-1">Makin banyak persegi, makin dekat ke nilai integral sebenarnya.</p>
                    </div>
                    <div className="pt-2 border-t border-slate/15">
                        <div className="flex justify-between"><span className="text-slate">Perkiraan luas (∫f dx)</span><span className="dial-readout">{approxArea.toFixed(2)}</span></div>
                    </div>
                </div>
            </div>
        </LabLayout>
    );
}
