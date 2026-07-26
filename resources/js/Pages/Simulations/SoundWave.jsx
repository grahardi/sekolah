import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function SoundWave({ category = 'Fisika' }) {
    const canvasRef = useRef(null);
    const tRef = useRef(0);
    const [frequency, setFrequency] = useState(2);
    const [amplitude, setAmplitude] = useState(60);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        let raf;

        const draw = () => {
            tRef.current += 1 / 60;
            const t = tRef.current;
            const midY = canvas.height / 2;

            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.strokeStyle = '#7C8AA5';
            ctx.beginPath();
            ctx.moveTo(0, midY);
            ctx.lineTo(canvas.width, midY);
            ctx.stroke();

            ctx.strokeStyle = '#FBBF24';
            ctx.lineWidth = 2;
            ctx.beginPath();
            for (let x = 0; x <= canvas.width; x++) {
                const phase = (x / canvas.width) * Math.PI * 4;
                const y = midY + amplitude * Math.sin(phase * frequency - t * frequency * 2);
                x === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
            }
            ctx.stroke();

            // Speaker sederhana di kiri, bergetar mengikuti gelombang
            const vib = amplitude * 0.15 * Math.sin(t * frequency * 2);
            ctx.fillStyle = '#F7F5EF';
            ctx.beginPath();
            ctx.moveTo(10, midY - 30);
            ctx.lineTo(35 + vib, midY - 15);
            ctx.lineTo(35 + vib, midY + 15);
            ctx.lineTo(10, midY + 30);
            ctx.closePath();
            ctx.fill();

            raf = requestAnimationFrame(draw);
        };
        raf = requestAnimationFrame(draw);
        return () => cancelAnimationFrame(raf);
    }, [frequency, amplitude]);

    return (
        <LabLayout title="Gelombang Bunyi" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={480} height={280} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Frekuensi</span><span className="dial-readout">{frequency.toFixed(1)} Hz (skala)</span></div>
                        <input type="range" min={0.5} max={6} step={0.1} value={frequency} onChange={(e) => setFrequency(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Amplitudo</span><span className="dial-readout">{amplitude} px</span></div>
                        <input type="range" min={10} max={100} value={amplitude} onChange={(e) => setAmplitude(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <p className="text-xs text-slate/70 pt-2 border-t border-slate/15">
                        Frekuensi tinggi = nada lebih tinggi (gelombang rapat). Amplitudo besar = bunyi lebih keras.
                    </p>
                </div>
            </div>
        </LabLayout>
    );
}
