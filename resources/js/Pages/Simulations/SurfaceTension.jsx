import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function SurfaceTension({ category = 'Fisika' }) {
    const canvasRef = useRef(null);
    const [tension, setTension] = useState(50);
    const [columnHeight, setColumnHeight] = useState(60);

    const density = 1000; // kg/m3, air
    const g = 9.8;
    const pressure = density * g * (columnHeight / 100); // Pa, ilustratif

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const surfaceY = 280;
        const dropX = 140;

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Permukaan meja
        ctx.strokeStyle = '#7C8AA5';
        ctx.beginPath();
        ctx.moveTo(0, surfaceY);
        ctx.lineTo(300, surfaceY);
        ctx.stroke();

        // Tetesan: tegangan rendah = melebar, tinggi = membulat
        const spread = 1 - tension / 130; // 0..~1
        const width = 50 + spread * 40;
        const height = 45 - spread * 20;
        ctx.fillStyle = 'rgba(143,211,254,0.85)';
        ctx.beginPath();
        ctx.ellipse(dropX, surfaceY - height / 2, width / 2, height, 0, 0, Math.PI * 2);
        ctx.fill();

        // Kolom fluida (untuk tekanan hidrostatis)
        const colX = 260;
        const colTop = surfaceY - columnHeight * 2;
        ctx.fillStyle = 'rgba(37,99,235,0.5)';
        ctx.fillRect(colX, colTop, 40, columnHeight * 2);
        ctx.strokeStyle = '#FBBF24';
        ctx.strokeRect(colX, surfaceY - 200, 40, 200);

        ctx.fillStyle = '#F7F5EF';
        ctx.font = '12px monospace';
        ctx.fillText('Tetesan', dropX - 25, surfaceY + 20);
        ctx.fillText('Kolom fluida', colX - 15, surfaceY + 20);
    }, [tension, columnHeight]);

    return (
        <LabLayout title="Tegangan Permukaan & Fluida" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={320} height={320} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Tegangan permukaan</span><span className="dial-readout">{tension}%</span></div>
                        <input type="range" min={0} max={100} value={tension} onChange={(e) => setTension(Number(e.target.value))} className="w-full accent-amber" />
                        <p className="text-xs text-slate/70 mt-1">Makin tinggi, tetesan makin membulat (mendekati bola).</p>
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Tinggi kolom fluida (h)</span><span className="dial-readout">{columnHeight} cm</span></div>
                        <input type="range" min={10} max={100} value={columnHeight} onChange={(e) => setColumnHeight(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15">
                        <div className="flex justify-between"><span className="text-slate">Tekanan hidrostatis (P = ρgh)</span><span className="dial-readout">{pressure.toFixed(0)} Pa</span></div>
                    </div>
                </div>
            </div>
        </LabLayout>
    );
}
