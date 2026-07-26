import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function HarmonicMotion({ category = 'Fisika' }) {
    const canvasRef = useRef(null);
    const tRef = useRef(0);

    const [mass, setMass] = useState(1.0);
    const [stiffness, setStiffness] = useState(20);
    const [amplitude, setAmplitude] = useState(80);
    const [running, setRunning] = useState(true);

    const omega = Math.sqrt(stiffness / mass);
    const period = (2 * Math.PI) / omega;

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const anchorY = 40;
        const restY = 220;
        let raf;

        const draw = () => {
            if (running) tRef.current += 1 / 60;
            const t = tRef.current;
            const x = amplitude * Math.cos(omega * t);
            const massY = restY + x;

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Langit-langit
            ctx.fillStyle = '#7C8AA5';
            ctx.fillRect(canvas.width / 2 - 50, anchorY - 8, 100, 8);

            // Pegas (zig-zag)
            ctx.strokeStyle = '#F7F5EF';
            ctx.lineWidth = 2;
            ctx.beginPath();
            const coils = 10;
            const segLen = (massY - anchorY) / coils;
            ctx.moveTo(canvas.width / 2, anchorY);
            for (let i = 1; i <= coils; i++) {
                const y = anchorY + segLen * i;
                const dx = i % 2 === 0 ? 18 : -18;
                ctx.lineTo(canvas.width / 2 + dx, y);
            }
            ctx.stroke();

            // Massa
            ctx.fillStyle = '#FBBF24';
            ctx.fillRect(canvas.width / 2 - 30, massY, 60, 40);

            raf = requestAnimationFrame(draw);
        };
        raf = requestAnimationFrame(draw);
        return () => cancelAnimationFrame(raf);
    }, [mass, stiffness, amplitude, running, omega]);

    return (
        <LabLayout title="Gerak Harmonik Sederhana" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={420} height={420} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Massa (m)</span><span className="dial-readout">{mass.toFixed(1)} kg</span></div>
                        <input type="range" min={0.2} max={4} step={0.1} value={mass} onChange={(e) => setMass(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Konstanta pegas (k)</span><span className="dial-readout">{stiffness} N/m</span></div>
                        <input type="range" min={5} max={60} value={stiffness} onChange={(e) => setStiffness(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Amplitudo</span><span className="dial-readout">{amplitude} px</span></div>
                        <input type="range" min={20} max={110} value={amplitude} onChange={(e) => setAmplitude(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15">
                        <div className="flex justify-between"><span className="text-slate">Periode (T = 2π√(m/k))</span><span className="dial-readout">{period.toFixed(2)} s</span></div>
                    </div>
                    <button onClick={() => setRunning((r) => !r)} className="w-full rounded-lg bg-amber text-ink font-medium py-2 hover:brightness-95">
                        {running ? 'Jeda' : 'Lanjutkan'}
                    </button>
                </div>
            </div>
        </LabLayout>
    );
}
