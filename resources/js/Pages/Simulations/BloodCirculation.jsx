import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function BloodCirculation({ category = 'Biologi' }) {
    const canvasRef = useRef(null);
    const offsetRef = useRef(0);
    const [heartRate, setHeartRate] = useState(75);

    const path = [
        [200, 60], [320, 100], [320, 300], [200, 340], [80, 300], [80, 100], [200, 60],
    ];

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        let raf;

        const pathLen = () => {
            let len = 0;
            for (let i = 0; i < path.length - 1; i++) {
                len += Math.hypot(path[i + 1][0] - path[i][0], path[i + 1][1] - path[i][1]);
            }
            return len;
        };
        const pointAt = (dist) => {
            let remaining = dist;
            for (let i = 0; i < path.length - 1; i++) {
                const [x1, y1] = path[i];
                const [x2, y2] = path[i + 1];
                const segLen = Math.hypot(x2 - x1, y2 - y1);
                if (remaining <= segLen) {
                    const t = remaining / segLen;
                    return [x1 + (x2 - x1) * t, y1 + (y2 - y1) * t];
                }
                remaining -= segLen;
            }
            return path[0];
        };
        const total = pathLen();

        const draw = () => {
            offsetRef.current += (heartRate / 60) * 1.2;

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.strokeStyle = '#7C8AA5';
            ctx.lineWidth = 3;
            ctx.beginPath();
            path.forEach(([x, y], i) => (i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y)));
            ctx.stroke();

            // Jantung sebagai pusat, berdenyut
            const pulse = 1 + 0.15 * Math.sin((offsetRef.current / 6) * Math.PI);
            ctx.fillStyle = '#E63946';
            ctx.beginPath();
            ctx.arc(200, 200, 30 * pulse, 0, Math.PI * 2);
            ctx.fill();
            ctx.fillStyle = '#F7F5EF';
            ctx.font = '12px monospace';
            ctx.fillText('Jantung', 175, 205);

            // Sel darah bergerak sepanjang pembuluh
            const spacing = 35;
            const count = Math.floor(total / spacing);
            for (let i = 0; i < count; i++) {
                const d = (i * spacing + offsetRef.current) % total;
                const [x, y] = pointAt(d);
                ctx.fillStyle = '#E63946';
                ctx.beginPath();
                ctx.arc(x, y, 5, 0, Math.PI * 2);
                ctx.fill();
            }

            raf = requestAnimationFrame(draw);
        };
        raf = requestAnimationFrame(draw);
        return () => cancelAnimationFrame(raf);
    }, [heartRate]);

    return (
        <LabLayout title="Sistem Peredaran Darah" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={400} height={400} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Detak jantung</span><span className="dial-readout">{heartRate} bpm</span></div>
                        <input type="range" min={40} max={180} value={heartRate} onChange={(e) => setHeartRate(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <p className="text-xs text-slate/70">
                        Titik merah = sel darah yang mengalir lewat pembuluh. Makin tinggi detak jantung, makin cepat alirannya.
                    </p>
                </div>
            </div>
        </LabLayout>
    );
}
