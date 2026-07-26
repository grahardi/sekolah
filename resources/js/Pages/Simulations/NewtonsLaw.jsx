import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function NewtonsLaw({ category = 'Fisika' }) {
    const canvasRef = useRef(null);
    const physicsRef = useRef({ x: 60, v: 0 });

    const [force, setForce] = useState(20);
    const [mass, setMass] = useState(5);
    const [friction, setFriction] = useState(2);
    const [running, setRunning] = useState(true);

    const netForce = force - friction * Math.sign(physicsRef.current.v || 1);
    const accel = force / mass;

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const groundY = 300;
        let raf;

        const draw = () => {
            const p = physicsRef.current;
            if (running) {
                const frictionForce = friction * Math.sign(p.v) * -1;
                const a = (force + (p.v !== 0 ? frictionForce : 0)) / mass;
                p.v += a * (1 / 60);
                if (p.v < 0) p.v = 0;
                p.x += p.v * (1 / 60) * 20;
                if (p.x > canvas.width - 60) { p.x = canvas.width - 60; p.v = 0; }
            }

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.strokeStyle = '#7C8AA5';
            ctx.beginPath();
            ctx.moveTo(0, groundY + 40);
            ctx.lineTo(canvas.width, groundY + 40);
            ctx.stroke();

            // Kotak
            ctx.fillStyle = '#FBBF24';
            ctx.fillRect(p.x, groundY, 60, 40);

            // Panah gaya
            ctx.strokeStyle = '#E63946';
            ctx.lineWidth = 3;
            ctx.beginPath();
            ctx.moveTo(p.x + 60, groundY + 20);
            ctx.lineTo(p.x + 60 + force * 1.5, groundY + 20);
            ctx.stroke();
            ctx.fillStyle = '#E63946';
            ctx.beginPath();
            ctx.moveTo(p.x + 60 + force * 1.5, groundY + 20);
            ctx.lineTo(p.x + 60 + force * 1.5 - 8, groundY + 15);
            ctx.lineTo(p.x + 60 + force * 1.5 - 8, groundY + 25);
            ctx.fill();

            raf = requestAnimationFrame(draw);
        };
        raf = requestAnimationFrame(draw);
        return () => cancelAnimationFrame(raf);
    }, [force, mass, friction, running]);

    const reset = () => { physicsRef.current = { x: 60, v: 0 }; };

    return (
        <LabLayout title="Hukum Newton & Gaya" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={480} height={360} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Gaya dorong (F)</span><span className="dial-readout">{force} N</span></div>
                        <input type="range" min={0} max={60} value={force} onChange={(e) => setForce(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Massa (m)</span><span className="dial-readout">{mass} kg</span></div>
                        <input type="range" min={1} max={20} value={mass} onChange={(e) => setMass(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Gaya gesek</span><span className="dial-readout">{friction} N</span></div>
                        <input type="range" min={0} max={15} value={friction} onChange={(e) => setFriction(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15">
                        <div className="flex justify-between"><span className="text-slate">Percepatan (a = F/m)</span><span className="dial-readout">{accel.toFixed(2)} m/s²</span></div>
                    </div>
                    <div className="flex gap-2">
                        <button onClick={() => setRunning((r) => !r)} className="flex-1 rounded-lg bg-amber text-ink font-medium py-2 hover:brightness-95">
                            {running ? 'Jeda' : 'Lanjutkan'}
                        </button>
                        <button onClick={reset} className="flex-1 rounded-lg bg-alert/20 text-alert font-medium py-2 hover:bg-alert/30">Reset</button>
                    </div>
                </div>
            </div>
        </LabLayout>
    );
}
