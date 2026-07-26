import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function MicroorganismGrowth({ category = 'Biologi' }) {
    const canvasRef = useRef(null);
    const [temperature, setTemperature] = useState(30);
    const [nutrient, setNutrient] = useState(70);

    // Model logistik sederhana untuk kurva pertumbuhan bakteri
    const growthRate = Math.max(0.05, ((temperature - 10) / 30) * (nutrient / 100));
    const carryingCapacity = (nutrient / 100) * 100;

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const points = [];
        let pop = 2;
        for (let t = 0; t < 100; t++) {
            pop += growthRate * pop * (1 - pop / carryingCapacity);
            points.push(Math.max(0, pop));
        }

        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.strokeStyle = '#7C8AA5';
        ctx.beginPath();
        ctx.moveTo(0, canvas.height - 20);
        ctx.lineTo(canvas.width, canvas.height - 20);
        ctx.stroke();

        ctx.strokeStyle = '#3ABF6B';
        ctx.lineWidth = 2.5;
        ctx.beginPath();
        points.forEach((p, i) => {
            const x = (i / 100) * canvas.width;
            const y = canvas.height - 20 - (p / 100) * (canvas.height - 40);
            i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
        });
        ctx.stroke();

        ctx.fillStyle = '#F7F5EF';
        ctx.font = '11px monospace';
        ctx.fillText('Waktu →', canvas.width - 60, canvas.height - 5);
        ctx.save();
        ctx.translate(10, 20);
        ctx.fillText('Populasi', 0, 0);
        ctx.restore();
    }, [temperature, nutrient, growthRate, carryingCapacity]);

    return (
        <LabLayout title="Dunia Mikroorganisme (Pertumbuhan Bakteri)" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={440} height={280} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Suhu lingkungan</span><span className="dial-readout">{temperature}°C</span></div>
                        <input type="range" min={10} max={45} value={temperature} onChange={(e) => setTemperature(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Ketersediaan nutrisi</span><span className="dial-readout">{nutrient}%</span></div>
                        <input type="range" min={10} max={100} value={nutrient} onChange={(e) => setNutrient(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <p className="text-xs text-slate/70">
                        Kurva pertumbuhan logistik: populasi bakteri naik cepat lalu melandai saat mendekati
                        batas daya dukung (nutrisi terbatas).
                    </p>
                </div>
            </div>
        </LabLayout>
    );
}
