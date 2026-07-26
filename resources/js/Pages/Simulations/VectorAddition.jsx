import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function VectorAddition({ category = 'Matematika' }) {
    const canvasRef = useRef(null);
    const [magA, setMagA] = useState(80);
    const [angleA, setAngleA] = useState(30);
    const [magB, setMagB] = useState(60);
    const [angleB, setAngleB] = useState(120);

    const toVec = (mag, angleDeg) => {
        const rad = (angleDeg * Math.PI) / 180;
        return [mag * Math.cos(rad), -mag * Math.sin(rad)];
    };

    const [ax, ay] = toVec(magA, angleA);
    const [bx, by] = toVec(magB, angleB);
    const rx = ax + bx;
    const ry = ay + by;
    const resultMag = Math.hypot(rx, ry);
    const resultAngle = ((Math.atan2(-ry, rx) * 180) / Math.PI + 360) % 360;

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const originX = canvas.width / 2;
        const originY = canvas.height / 2;

        const drawArrow = (dx, dy, color) => {
            const endX = originX + dx;
            const endY = originY + dy;
            ctx.strokeStyle = color;
            ctx.fillStyle = color;
            ctx.lineWidth = 2.5;
            ctx.beginPath(); ctx.moveTo(originX, originY); ctx.lineTo(endX, endY); ctx.stroke();

            const angle = Math.atan2(dy, dx);
            ctx.beginPath();
            ctx.moveTo(endX, endY);
            ctx.lineTo(endX - 10 * Math.cos(angle - 0.4), endY - 10 * Math.sin(angle - 0.4));
            ctx.lineTo(endX - 10 * Math.cos(angle + 0.4), endY - 10 * Math.sin(angle + 0.4));
            ctx.fill();
        };

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.strokeStyle = 'rgba(124,138,165,0.3)';
        ctx.beginPath(); ctx.moveTo(0, originY); ctx.lineTo(canvas.width, originY); ctx.stroke();
        ctx.beginPath(); ctx.moveTo(originX, 0); ctx.lineTo(originX, canvas.height); ctx.stroke();

        drawArrow(ax, ay, '#8FD3FE');
        drawArrow(bx, by, '#FBBF24');
        drawArrow(rx, ry, '#E63946');
    }, [ax, ay, bx, by, rx, ry]);

    return (
        <LabLayout title="Vektor 2D" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={420} height={420} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <p className="text-xs" style={{ color: '#8FD3FE' }}>Vektor A</p>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Besar A</span><span className="dial-readout">{magA}</span></div>
                        <input type="range" min={10} max={130} value={magA} onChange={(e) => setMagA(Number(e.target.value))} className="w-full accent-amber" />
                        <div className="flex justify-between text-slate mb-1 mt-2"><span>Sudut A</span><span className="dial-readout">{angleA}°</span></div>
                        <input type="range" min={0} max={360} value={angleA} onChange={(e) => setAngleA(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <p className="text-xs" style={{ color: '#FBBF24' }}>Vektor B</p>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Besar B</span><span className="dial-readout">{magB}</span></div>
                        <input type="range" min={10} max={130} value={magB} onChange={(e) => setMagB(Number(e.target.value))} className="w-full accent-amber" />
                        <div className="flex justify-between text-slate mb-1 mt-2"><span>Sudut B</span><span className="dial-readout">{angleB}°</span></div>
                        <input type="range" min={0} max={360} value={angleB} onChange={(e) => setAngleB(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15 space-y-1">
                        <p className="text-xs text-alert">Resultan (A + B)</p>
                        <div className="flex justify-between"><span className="text-slate">Besar</span><span className="dial-readout">{resultMag.toFixed(1)}</span></div>
                        <div className="flex justify-between"><span className="text-slate">Sudut</span><span className="dial-readout">{resultAngle.toFixed(1)}°</span></div>
                    </div>
                </div>
            </div>
        </LabLayout>
    );
}
