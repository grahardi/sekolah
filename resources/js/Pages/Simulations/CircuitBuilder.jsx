import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function CircuitBuilder({ category = 'Fisika' }) {
    const canvasRef = useRef(null);
    const [voltage, setVoltage] = useState(9);
    const [resistance, setResistance] = useState(30);
    const offsetRef = useRef(0);

    const current = voltage / resistance; // Hukum Ohm: I = V / R

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        let raf;

        // Titik-titik jalur rangkaian seri persegi panjang
        const path = [
            [80, 80], [420, 80], [420, 320], [80, 320], [80, 80],
        ];
        const pathLength = () => {
            let len = 0;
            for (let i = 0; i < path.length - 1; i++) {
                const [x1, y1] = path[i];
                const [x2, y2] = path[i + 1];
                len += Math.hypot(x2 - x1, y2 - y1);
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

        const total = pathLength();

        const draw = () => {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Kawat
            ctx.strokeStyle = '#7C8AA5';
            ctx.lineWidth = 3;
            ctx.beginPath();
            path.forEach(([x, y], i) => (i === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y)));
            ctx.stroke();

            // Simbol baterai di sisi kiri
            ctx.fillStyle = '#F7F5EF';
            ctx.font = '13px "IBM Plex Mono", monospace';
            ctx.fillText(`${voltage.toFixed(1)} V`, 20, 205);
            ctx.fillRect(74, 190, 12, 30);

            // Simbol resistor di sisi atas (zig-zag)
            ctx.strokeStyle = '#F4A300';
            ctx.beginPath();
            const zz = 200;
            for (let i = 0; i <= 8; i++) {
                const x = zz + i * 10;
                const y = 80 + (i % 2 === 0 ? -8 : 8);
                i === 0 ? ctx.moveTo(x, 80) : ctx.lineTo(x, y);
            }
            ctx.stroke();
            ctx.fillStyle = '#F4A300';
            ctx.fillText(`${resistance} Ω`, zz + 10, 60);

            // Elektron bergerak sepanjang jalur - kecepatan sebanding dengan arus
            offsetRef.current += current * 1.4;
            const spacing = 45;
            const count = Math.floor(total / spacing);
            ctx.fillStyle = '#8FD3FE';
            for (let i = 0; i < count; i++) {
                const d = (i * spacing + offsetRef.current) % total;
                const [x, y] = pointAt(d);
                ctx.beginPath();
                ctx.arc(x, y, 4, 0, Math.PI * 2);
                ctx.fill();
            }

            raf = requestAnimationFrame(draw);
        };

        raf = requestAnimationFrame(draw);
        return () => cancelAnimationFrame(raf);
    }, [voltage, resistance, current]);

    return (
        <LabLayout title="Rangkaian Listrik Seri" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={500} height={400} className="max-w-full" />
                </div>

                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1">
                            <span>Tegangan (V)</span>
                            <span className="dial-readout">{voltage.toFixed(1)} V</span>
                        </div>
                        <input
                            type="range" min={1} max={24} step={0.5} value={voltage}
                            onChange={(e) => setVoltage(Number(e.target.value))}
                            className="w-full accent-amber"
                        />
                    </div>

                    <div>
                        <div className="flex justify-between text-slate mb-1">
                            <span>Hambatan (R)</span>
                            <span className="dial-readout">{resistance} Ω</span>
                        </div>
                        <input
                            type="range" min={5} max={100} value={resistance}
                            onChange={(e) => setResistance(Number(e.target.value))}
                            className="w-full accent-amber"
                        />
                    </div>

                    <div className="pt-2 border-t border-slate/15 space-y-1">
                        <div className="flex justify-between">
                            <span className="text-slate">Arus (I = V/R)</span>
                            <span className="dial-readout">{current.toFixed(2)} A</span>
                        </div>
                        <p className="text-xs text-slate/70">
                            Titik biru = elektron. Makin besar arus, makin cepat mengalir.
                        </p>
                    </div>
                </div>
            </div>
        </LabLayout>
    );
}
