import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function ProjectileMotion({ category = 'Fisika' }) {
    const canvasRef = useRef(null);
    const [angle, setAngle] = useState(45);
    const [speed, setSpeed] = useState(40);
    const [firing, setFiring] = useState(false);
    const [range, setRange] = useState(0);
    const trailRef = useRef([]);
    const posRef = useRef({ x: 0, y: 0, t: 0 });

    const GROUND_Y = 380;
    const ORIGIN_X = 40;
    const SCALE = 4; // px per meter

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        let raf;

        const draw = () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Tanah
            ctx.strokeStyle = '#7C8AA5';
            ctx.beginPath();
            ctx.moveTo(0, GROUND_Y);
            ctx.lineTo(canvas.width, GROUND_Y);
            ctx.stroke();

            if (firing) {
                const g = 9.8;
                const t = posRef.current.t;
                const rad = (angle * Math.PI) / 180;
                const vx = speed * Math.cos(rad);
                const vy = speed * Math.sin(rad);
                const x = vx * t;
                const y = vy * t - 0.5 * g * t * t;

                const px = ORIGIN_X + x * SCALE;
                const py = GROUND_Y - y * SCALE;

                if (py >= GROUND_Y || px > canvas.width) {
                    setFiring(false);
                    setRange(x.toFixed(1));
                } else {
                    trailRef.current.push([px, py]);
                    posRef.current.t += 1 / 60;
                }
            }

            // Jejak lintasan
            ctx.strokeStyle = 'rgba(244,163,0,0.5)';
            ctx.beginPath();
            trailRef.current.forEach(([tx, ty], i) => {
                if (i === 0) ctx.moveTo(tx, ty);
                else ctx.lineTo(tx, ty);
            });
            ctx.stroke();

            // Proyektil
            const last = trailRef.current[trailRef.current.length - 1] || [ORIGIN_X, GROUND_Y];
            ctx.fillStyle = '#F4A300';
            ctx.beginPath();
            ctx.arc(last[0], last[1], 8, 0, Math.PI * 2);
            ctx.fill();

            // Peluncur
            ctx.strokeStyle = '#F7F5EF';
            ctx.lineWidth = 4;
            const rad = (angle * Math.PI) / 180;
            ctx.beginPath();
            ctx.moveTo(ORIGIN_X, GROUND_Y);
            ctx.lineTo(ORIGIN_X + 30 * Math.cos(rad), GROUND_Y - 30 * Math.sin(rad));
            ctx.stroke();

            raf = requestAnimationFrame(draw);
        };

        raf = requestAnimationFrame(draw);
        return () => cancelAnimationFrame(raf);
    }, [firing, angle, speed]);

    const fire = () => {
        trailRef.current = [];
        posRef.current = { x: 0, y: 0, t: 0 };
        setRange(0);
        setFiring(true);
    };

    return (
        <LabLayout title="Gerak Peluru" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={560} height={420} className="max-w-full" />
                </div>

                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1">
                            <span>Sudut peluncuran</span>
                            <span className="dial-readout">{angle}°</span>
                        </div>
                        <input
                            type="range" min={5} max={85} value={angle} disabled={firing}
                            onChange={(e) => setAngle(Number(e.target.value))}
                            className="w-full accent-amber"
                        />
                    </div>

                    <div>
                        <div className="flex justify-between text-slate mb-1">
                            <span>Kecepatan awal</span>
                            <span className="dial-readout">{speed} m/s</span>
                        </div>
                        <input
                            type="range" min={10} max={70} value={speed} disabled={firing}
                            onChange={(e) => setSpeed(Number(e.target.value))}
                            className="w-full accent-amber"
                        />
                    </div>

                    <div className="pt-2 border-t border-slate/15">
                        <div className="flex justify-between">
                            <span className="text-slate">Jarak jangkau</span>
                            <span className="dial-readout">{range} m</span>
                        </div>
                    </div>

                    <button
                        onClick={fire}
                        disabled={firing}
                        className="w-full rounded-lg bg-amber text-ink font-medium py-2 disabled:opacity-50 hover:brightness-95"
                    >
                        {firing ? 'Meluncur…' : 'Luncurkan'}
                    </button>
                </div>
            </div>
        </LabLayout>
    );
}
