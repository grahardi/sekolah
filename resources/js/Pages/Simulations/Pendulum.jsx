import { useEffect, useRef, useState } from 'react';
import AppLayout from '../../Layouts/AppLayout';

// Simulasi bandul sederhana dengan integrasi Euler kecil-kecilan.
// Sengaja tidak pakai physics engine eksternal supaya mudah dibaca & dimodifikasi.
export default function Pendulum() {
    const canvasRef = useRef(null);
    const stateRef = useRef({ angle: Math.PI / 4, angleVel: 0 });

    const [length, setLength] = useState(180); // px, berperan sebagai panjang tali
    const [gravity, setGravity] = useState(9.8);
    const [running, setRunning] = useState(true);
    const [periodDisplay, setPeriodDisplay] = useState(0);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const originX = canvas.width / 2;
        const originY = 60;
        let raf;
        let lastPeriodTime = performance.now();
        let lastSign = 1;

        const step = () => {
            const s = stateRef.current;
            if (running) {
                const g = gravity * 60; // skala agar terasa di ruang piksel
                const angularAccel = (-g / length) * Math.sin(s.angle);
                s.angleVel += angularAccel * (1 / 60);
                s.angleVel *= 0.999; // redaman kecil
                s.angle += s.angleVel * (1 / 60);

                // Deteksi separuh periode (saat melewati titik terendah)
                const sign = Math.sign(s.angleVel) || 1;
                if (sign !== lastSign) {
                    const now = performance.now();
                    setPeriodDisplay(((now - lastPeriodTime) / 1000) * 2);
                    lastPeriodTime = now;
                    lastSign = sign;
                }
            }

            const bobX = originX + length * Math.sin(s.angle);
            const bobY = originY + length * Math.cos(s.angle);

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Tiang penyangga
            ctx.strokeStyle = '#7C8AA5';
            ctx.lineWidth = 2;
            ctx.beginPath();
            ctx.moveTo(originX - 40, originY);
            ctx.lineTo(originX + 40, originY);
            ctx.stroke();

            // Tali
            ctx.strokeStyle = '#F7F5EF';
            ctx.beginPath();
            ctx.moveTo(originX, originY);
            ctx.lineTo(bobX, bobY);
            ctx.stroke();

            // Bandul
            ctx.fillStyle = '#F4A300';
            ctx.beginPath();
            ctx.arc(bobX, bobY, 16, 0, Math.PI * 2);
            ctx.fill();

            // Titik jangkar
            ctx.fillStyle = '#7C8AA5';
            ctx.beginPath();
            ctx.arc(originX, originY, 4, 0, Math.PI * 2);
            ctx.fill();

            raf = requestAnimationFrame(step);
        };

        raf = requestAnimationFrame(step);
        return () => cancelAnimationFrame(raf);
    }, [length, gravity, running]);

    const resetSim = () => {
        stateRef.current = { angle: Math.PI / 4, angleVel: 0 };
        setPeriodDisplay(0);
    };

    return (
        <AppLayout title="Bandul Sederhana" subtitle="Fisika · Getaran & Gelombang">
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={480} height={420} className="max-w-full" />
                </div>

                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1">
                            <span>Panjang tali</span>
                            <span className="dial-readout">{length} px</span>
                        </div>
                        <input
                            type="range" min={80} max={280} value={length}
                            onChange={(e) => setLength(Number(e.target.value))}
                            className="w-full accent-amber"
                        />
                    </div>

                    <div>
                        <div className="flex justify-between text-slate mb-1">
                            <span>Gravitasi</span>
                            <span className="dial-readout">{gravity.toFixed(1)} m/s²</span>
                        </div>
                        <input
                            type="range" min={1} max={25} step={0.1} value={gravity}
                            onChange={(e) => setGravity(Number(e.target.value))}
                            className="w-full accent-amber"
                        />
                        <p className="text-xs text-slate/70 mt-1">Coba 1.6 (Bulan) vs 24.8 (Jupiter)</p>
                    </div>

                    <div className="pt-2 border-t border-slate/15">
                        <div className="flex justify-between">
                            <span className="text-slate">Estimasi periode</span>
                            <span className="dial-readout">{periodDisplay.toFixed(2)} s</span>
                        </div>
                    </div>

                    <div className="flex gap-2 pt-2">
                        <button
                            onClick={() => setRunning((r) => !r)}
                            className="flex-1 rounded-lg bg-amber text-ink font-medium py-2 hover:brightness-95"
                        >
                            {running ? 'Jeda' : 'Lanjutkan'}
                        </button>
                        <button
                            onClick={resetSim}
                            className="flex-1 rounded-lg bg-alert/20 text-alert font-medium py-2 hover:bg-alert/30"
                        >
                            Reset
                        </button>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
