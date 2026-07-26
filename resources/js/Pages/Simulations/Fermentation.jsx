import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function Fermentation({ category = 'Biologi' }) {
    const canvasRef = useRef(null);
    const bubblesRef = useRef([]);
    const [temperature, setTemperature] = useState(30);
    const [sugar, setSugar] = useState(50);

    const co2Rate = Math.max(0, ((temperature - 15) / 30) * (sugar / 100) * 10);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        let raf;
        const jarTop = 40, jarBottom = 260, jarLeft = 100, jarRight = 260;

        const draw = () => {
            if (Math.random() < co2Rate / 40) {
                bubblesRef.current.push({ x: jarLeft + Math.random() * (jarRight - jarLeft), y: jarBottom - 10, r: 2 + Math.random() * 3 });
            }

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Toples
            ctx.strokeStyle = '#7C8AA5';
            ctx.lineWidth = 3;
            ctx.strokeRect(jarLeft, jarTop, jarRight - jarLeft, jarBottom - jarTop);

            // Cairan fermentasi
            ctx.fillStyle = 'rgba(251,191,36,0.3)';
            ctx.fillRect(jarLeft, jarTop + 60, jarRight - jarLeft, jarBottom - jarTop - 60);

            bubblesRef.current = bubblesRef.current.filter((b) => b.y > jarTop + 60);
            bubblesRef.current.forEach((b) => {
                b.y -= 1.5;
                ctx.strokeStyle = '#F7F5EF';
                ctx.beginPath();
                ctx.arc(b.x, b.y, b.r, 0, Math.PI * 2);
                ctx.stroke();
            });

            raf = requestAnimationFrame(draw);
        };
        raf = requestAnimationFrame(draw);
        return () => cancelAnimationFrame(raf);
    }, [temperature, sugar, co2Rate]);

    return (
        <LabLayout title="Bioteknologi Sederhana (Fermentasi)" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={360} height={300} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Suhu fermentasi</span><span className="dial-readout">{temperature}°C</span></div>
                        <input type="range" min={10} max={45} value={temperature} onChange={(e) => setTemperature(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Konsentrasi gula</span><span className="dial-readout">{sugar}%</span></div>
                        <input type="range" min={0} max={100} value={sugar} onChange={(e) => setSugar(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15">
                        <div className="flex justify-between"><span className="text-slate">Laju produksi CO₂</span><span className="dial-readout">{co2Rate.toFixed(1)}</span></div>
                    </div>
                    <p className="text-xs text-slate/70">
                        Ragi (khamir) memfermentasi gula menjadi alkohol dan gas CO₂ (gelembung). Ini prinsip
                        pembuatan tapai, tempe, dan roti.
                    </p>
                </div>
            </div>
        </LabLayout>
    );
}
