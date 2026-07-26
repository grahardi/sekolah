import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function UnitCircle({ category = 'Matematika' }) {
    const canvasRef = useRef(null);
    const [angleDeg, setAngleDeg] = useState(45);

    const angleRad = (angleDeg * Math.PI) / 180;
    const cosVal = Math.cos(angleRad);
    const sinVal = Math.sin(angleRad);
    const tanVal = Math.tan(angleRad);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const cx = canvas.width / 2;
        const cy = canvas.height / 2;
        const r = 130;

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Sumbu
        ctx.strokeStyle = '#7C8AA5';
        ctx.beginPath(); ctx.moveTo(0, cy); ctx.lineTo(canvas.width, cy); ctx.stroke();
        ctx.beginPath(); ctx.moveTo(cx, 0); ctx.lineTo(cx, canvas.height); ctx.stroke();

        // Lingkaran satuan
        ctx.strokeStyle = '#7C8AA5';
        ctx.beginPath(); ctx.arc(cx, cy, r, 0, Math.PI * 2); ctx.stroke();

        const px = cx + r * cosVal;
        const py = cy - r * sinVal;

        // Garis cos (horizontal, kuning)
        ctx.strokeStyle = '#FBBF24';
        ctx.lineWidth = 2.5;
        ctx.beginPath(); ctx.moveTo(cx, cy); ctx.lineTo(px, cy); ctx.stroke();

        // Garis sin (vertikal, biru)
        ctx.strokeStyle = '#8FD3FE';
        ctx.beginPath(); ctx.moveTo(px, cy); ctx.lineTo(px, py); ctx.stroke();

        // Jari-jari ke titik sudut
        ctx.strokeStyle = '#F7F5EF';
        ctx.beginPath(); ctx.moveTo(cx, cy); ctx.lineTo(px, py); ctx.stroke();

        // Titik pada lingkaran
        ctx.fillStyle = '#F7F5EF';
        ctx.beginPath(); ctx.arc(px, py, 5, 0, Math.PI * 2); ctx.fill();

        // Busur sudut kecil
        ctx.strokeStyle = '#E63946';
        ctx.beginPath();
        ctx.arc(cx, cy, 25, -angleRad, 0, angleRad > 0);
        ctx.stroke();
    }, [angleRad, cosVal, sinVal]);

    return (
        <LabLayout title="Trigonometri Lingkaran Satuan" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={360} height={360} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Sudut (θ)</span><span className="dial-readout">{angleDeg}°</span></div>
                        <input type="range" min={0} max={360} value={angleDeg} onChange={(e) => setAngleDeg(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15 space-y-1">
                        <div className="flex justify-between"><span className="text-slate">sin θ</span><span className="dial-readout">{sinVal.toFixed(3)}</span></div>
                        <div className="flex justify-between"><span className="text-slate">cos θ</span><span className="dial-readout">{cosVal.toFixed(3)}</span></div>
                        <div className="flex justify-between"><span className="text-slate">tan θ</span><span className="dial-readout">{Number.isFinite(tanVal) ? tanVal.toFixed(3) : '~'}</span></div>
                    </div>
                </div>
            </div>
        </LabLayout>
    );
}
