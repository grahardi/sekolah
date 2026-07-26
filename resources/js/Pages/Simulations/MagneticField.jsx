import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function MagneticField({ category = 'Fisika' }) {
    const canvasRef = useRef(null);
    const offsetRef = useRef(0);
    const [current, setCurrent] = useState(5);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const cx = canvas.width / 2;
        const cy = canvas.height / 2;
        let raf;

        const draw = () => {
            offsetRef.current += (current / 10) * 0.6;

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Kawat (titik = arus keluar bidang)
            ctx.fillStyle = '#FBBF24';
            ctx.beginPath();
            ctx.arc(cx, cy, 8, 0, Math.PI * 2);
            ctx.fill();
            ctx.strokeStyle = '#FBBF24';
            ctx.beginPath();
            ctx.arc(cx, cy, 4, 0, Math.PI * 2);
            ctx.stroke();
            ctx.fillStyle = '#F7F5EF';
            ctx.font = '12px monospace';
            ctx.fillText('I keluar bidang', cx - 45, cy - 20);

            // Garis medan melingkar konsentris, dianimasikan sesuai arah arus
            const radii = [40, 70, 100, 130];
            radii.forEach((r) => {
                ctx.strokeStyle = 'rgba(143,211,254,0.6)';
                ctx.lineWidth = 1.5;
                ctx.beginPath();
                ctx.arc(cx, cy, r, 0, Math.PI * 2);
                ctx.stroke();

                // Panah arah medan pada lingkaran
                const dir = current >= 0 ? 1 : -1;
                const angle = (offsetRef.current * dir) % (Math.PI * 2);
                const ax = cx + r * Math.cos(angle);
                const ay = cy + r * Math.sin(angle);
                const tangentAngle = angle + (dir * Math.PI) / 2;
                ctx.fillStyle = '#8FD3FE';
                ctx.beginPath();
                ctx.arc(ax, ay, 4, 0, Math.PI * 2);
                ctx.fill();
            });

            raf = requestAnimationFrame(draw);
        };
        raf = requestAnimationFrame(draw);
        return () => cancelAnimationFrame(raf);
    }, [current]);

    return (
        <LabLayout title="Medan Magnet" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={360} height={360} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Arus (I)</span><span className="dial-readout">{current} A</span></div>
                        <input type="range" min={-10} max={10} value={current} onChange={(e) => setCurrent(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <p className="text-xs text-slate/70">
                        Nilai positif = arus keluar bidang gambar, negatif = arus masuk bidang.
                        Arah putaran titik biru mengikuti kaidah tangan kanan.
                    </p>
                </div>
            </div>
        </LabLayout>
    );
}
