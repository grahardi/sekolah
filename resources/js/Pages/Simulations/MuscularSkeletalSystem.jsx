import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function MuscularSkeletalSystem({ category = 'Biologi' }) {
    const canvasRef = useRef(null);
    const [angle, setAngle] = useState(90);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const shoulderX = 100, shoulderY = 60;
        const upperLen = 90;
        const rad = (angle * Math.PI) / 180;
        const elbowX = shoulderX + upperLen * Math.sin(rad * 0 + 0.3);
        const elbowY = shoulderY + upperLen;
        const foreLen = 90;
        const foreRad = ((180 - angle) * Math.PI) / 180;
        const handX = elbowX + foreLen * Math.sin(foreRad - Math.PI / 2 + Math.PI / 2) * Math.cos(0);
        const handY = elbowY - foreLen * Math.cos((angle * Math.PI) / 180);

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Tulang atas (humerus)
        ctx.strokeStyle = '#F7F5EF';
        ctx.lineWidth = 10;
        ctx.lineCap = 'round';
        ctx.beginPath();
        ctx.moveTo(shoulderX, shoulderY);
        ctx.lineTo(elbowX, elbowY);
        ctx.stroke();

        // Tulang bawah (radius/ulna) - sudut siku berubah sesuai slider
        const handXFinal = elbowX + foreLen * Math.sin((angle * Math.PI) / 180);
        const handYFinal = elbowY - foreLen * Math.cos((angle * Math.PI) / 180) * -1 + foreLen * Math.cos((angle * Math.PI) / 180);
        const hx = elbowX + foreLen * Math.cos(((180 - angle) * Math.PI) / 180 - Math.PI / 2);
        const hy = elbowY - foreLen * Math.sin(((180 - angle) * Math.PI) / 180 - Math.PI / 2);
        ctx.beginPath();
        ctx.moveTo(elbowX, elbowY);
        ctx.lineTo(hx, hy);
        ctx.stroke();

        // Sendi siku
        ctx.fillStyle = '#8FD3FE';
        ctx.beginPath();
        ctx.arc(elbowX, elbowY, 8, 0, Math.PI * 2);
        ctx.fill();

        // Otot bisep (mengembang saat siku menekuk)
        const contraction = 1 - angle / 180;
        ctx.fillStyle = `rgba(230,57,70,${0.4 + contraction * 0.4})`;
        ctx.beginPath();
        ctx.ellipse((shoulderX + elbowX) / 2, (shoulderY + elbowY) / 2, 18 + contraction * 12, 12, 0, 0, Math.PI * 2);
        ctx.fill();

        ctx.fillStyle = '#F7F5EF';
        ctx.font = '11px monospace';
        ctx.fillText('Bisep (otot)', shoulderX - 40, shoulderY + 40);
    }, [angle]);

    return (
        <LabLayout title="Sistem Gerak (Otot & Rangka)" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={300} height={260} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Sudut siku</span><span className="dial-readout">{angle}°</span></div>
                        <input type="range" min={20} max={180} value={angle} onChange={(e) => setAngle(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <p className="text-xs text-slate/70">
                        Otot bekerja berpasangan (bisep-trisep) untuk menggerakkan tulang di sekitar sendi.
                        Semakin siku ditekuk, otot bisep semakin mengembang (kontraksi).
                    </p>
                </div>
            </div>
        </LabLayout>
    );
}
