import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

const BASE_TRIANGLE = [[0, 0], [60, 0], [30, 50]];
const MODES = ['Translasi', 'Rotasi', 'Skala', 'Refleksi'];

export default function GeometricTransform({ category = 'Matematika' }) {
    const canvasRef = useRef(null);
    const [mode, setMode] = useState('Translasi');
    const [tx, setTx] = useState(60);
    const [ty, setTy] = useState(-30);
    const [angle, setAngle] = useState(45);
    const [scale, setScale] = useState(1.4);

    const transform = ([x, y]) => {
        if (mode === 'Translasi') return [x + tx, y + ty];
        if (mode === 'Rotasi') {
            const rad = (angle * Math.PI) / 180;
            return [x * Math.cos(rad) - y * Math.sin(rad), x * Math.sin(rad) + y * Math.cos(rad)];
        }
        if (mode === 'Skala') return [x * scale, y * scale];
        if (mode === 'Refleksi') return [-x, y]; // cermin terhadap sumbu-y
        return [x, y];
    };

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const originX = canvas.width / 2;
        const originY = canvas.height / 2;

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.strokeStyle = 'rgba(124,138,165,0.3)';
        ctx.beginPath(); ctx.moveTo(0, originY); ctx.lineTo(canvas.width, originY); ctx.stroke();
        ctx.beginPath(); ctx.moveTo(originX, 0); ctx.lineTo(originX, canvas.height); ctx.stroke();

        const drawTriangle = (points, color, fill) => {
            ctx.strokeStyle = color;
            ctx.fillStyle = fill;
            ctx.lineWidth = 2;
            ctx.beginPath();
            points.forEach(([x, y], i) => {
                const px = originX + x;
                const py = originY - y;
                i === 0 ? ctx.moveTo(px, py) : ctx.lineTo(px, py);
            });
            ctx.closePath();
            ctx.fill();
            ctx.stroke();
        };

        drawTriangle(BASE_TRIANGLE, '#7C8AA5', 'rgba(124,138,165,0.15)');
        drawTriangle(BASE_TRIANGLE.map(transform), '#FBBF24', 'rgba(251,191,36,0.25)');
    }, [mode, tx, ty, angle, scale]);

    return (
        <LabLayout title="Transformasi Geometri" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={420} height={380} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div className="flex flex-wrap gap-2">
                        {MODES.map((m) => (
                            <button
                                key={m}
                                onClick={() => setMode(m)}
                                className={`text-xs font-mono rounded-full px-3 py-1.5 ${mode === m ? 'bg-amber text-ink' : 'bg-white/10 text-slate'}`}
                            >
                                {m}
                            </button>
                        ))}
                    </div>

                    {mode === 'Translasi' && (
                        <>
                            <div>
                                <div className="flex justify-between text-slate mb-1"><span>Geser X</span><span className="dial-readout">{tx}</span></div>
                                <input type="range" min={-100} max={100} value={tx} onChange={(e) => setTx(Number(e.target.value))} className="w-full accent-amber" />
                            </div>
                            <div>
                                <div className="flex justify-between text-slate mb-1"><span>Geser Y</span><span className="dial-readout">{ty}</span></div>
                                <input type="range" min={-100} max={100} value={ty} onChange={(e) => setTy(Number(e.target.value))} className="w-full accent-amber" />
                            </div>
                        </>
                    )}
                    {mode === 'Rotasi' && (
                        <div>
                            <div className="flex justify-between text-slate mb-1"><span>Sudut rotasi</span><span className="dial-readout">{angle}°</span></div>
                            <input type="range" min={0} max={360} value={angle} onChange={(e) => setAngle(Number(e.target.value))} className="w-full accent-amber" />
                        </div>
                    )}
                    {mode === 'Skala' && (
                        <div>
                            <div className="flex justify-between text-slate mb-1"><span>Faktor skala</span><span className="dial-readout">{scale.toFixed(1)}x</span></div>
                            <input type="range" min={0.3} max={2.5} step={0.1} value={scale} onChange={(e) => setScale(Number(e.target.value))} className="w-full accent-amber" />
                        </div>
                    )}
                    {mode === 'Refleksi' && (
                        <p className="text-xs text-slate/70">Segitiga dicerminkan terhadap sumbu-Y.</p>
                    )}
                    <p className="text-xs text-slate/70 pt-2 border-t border-slate/15">
                        Abu-abu = bangun asli, kuning = hasil transformasi.
                    </p>
                </div>
            </div>
        </LabLayout>
    );
}
