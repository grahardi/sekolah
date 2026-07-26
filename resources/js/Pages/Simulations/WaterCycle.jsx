import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function WaterCycle({ category = 'Biologi' }) {
    const canvasRef = useRef(null);
    const particlesRef = useRef([]);
    const [temperature, setTemperature] = useState(28);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        let raf;
        const seaY = canvas.height - 40;
        const evapRate = Math.max(0.2, (temperature - 15) / 30);

        const draw = () => {
            if (Math.random() < evapRate * 0.3) {
                particlesRef.current.push({
                    x: 50 + Math.random() * (canvas.width - 100), y: seaY,
                    vy: -1 - Math.random(), stage: 'evap', life: 200,
                });
            }

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Laut
            ctx.fillStyle = 'rgba(37,99,235,0.5)';
            ctx.fillRect(0, seaY, canvas.width, 40);

            // Awan
            ctx.fillStyle = 'rgba(247,245,239,0.6)';
            ctx.beginPath();
            ctx.ellipse(canvas.width / 2, 50, 90, 30, 0, 0, Math.PI * 2);
            ctx.fill();

            // Gunung sederhana
            ctx.fillStyle = '#1B4332';
            ctx.beginPath();
            ctx.moveTo(canvas.width - 120, seaY);
            ctx.lineTo(canvas.width - 60, seaY - 100);
            ctx.lineTo(canvas.width, seaY);
            ctx.fill();

            particlesRef.current = particlesRef.current.filter((p) => p.life > 0);
            particlesRef.current.forEach((p) => {
                if (p.stage === 'evap') {
                    p.y += p.vy; p.life -= 1;
                    if (p.y < 80) { p.stage = 'rain'; p.vy = 2; }
                    ctx.fillStyle = '#8FD3FE';
                } else {
                    p.y += p.vy; p.life -= 1;
                    ctx.fillStyle = '#2563EB';
                }
                ctx.beginPath();
                ctx.arc(p.x, p.y, 3, 0, Math.PI * 2);
                ctx.fill();
            });

            raf = requestAnimationFrame(draw);
        };
        raf = requestAnimationFrame(draw);
        return () => cancelAnimationFrame(raf);
    }, [temperature]);

    return (
        <LabLayout title="Siklus Air" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={480} height={320} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Suhu</span><span className="dial-readout">{temperature}°C</span></div>
                        <input type="range" min={15} max={40} value={temperature} onChange={(e) => setTemperature(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <p className="text-xs text-slate/70">
                        Titik biru muda = uap air (evaporasi naik ke awan). Titik biru tua = air hujan (presipitasi turun).
                        Makin panas, makin cepat penguapan.
                    </p>
                </div>
            </div>
        </LabLayout>
    );
}
