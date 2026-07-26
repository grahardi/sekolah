import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function NaturalSelection({ category = 'Biologi' }) {
    const canvasRef = useRef(null);
    const [selectionPressure, setSelectionPressure] = useState(50);
    const [generation, setGeneration] = useState(0);
    const [lightAlleleFreq, setLightAlleleFreq] = useState(50);
    const [running, setRunning] = useState(false);

    useEffect(() => {
        if (!running) return;
        const interval = setInterval(() => {
            setGeneration((g) => g + 1);
            setLightAlleleFreq((freq) => {
                // Tekanan seleksi mendukung warna gelap jika environment gelap (pressure tinggi)
                const shift = (selectionPressure - 50) / 100 * -3;
                return Math.min(100, Math.max(0, freq + shift + (Math.random() - 0.5) * 2));
            });
        }, 500);
        return () => clearInterval(interval);
    }, [running, selectionPressure]);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Background merepresentasikan lingkungan (gelap jika pressure tinggi)
        const bg = 220 - selectionPressure * 1.5;
        ctx.fillStyle = `rgb(${bg},${bg},${bg + 20})`;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Populasi kupu-kupu (titik) - proporsi terang vs gelap sesuai allele freq
        const total = 60;
        const lightCount = Math.round((lightAlleleFreq / 100) * total);
        for (let i = 0; i < total; i++) {
            const x = 20 + (i % 12) * 35;
            const y = 20 + Math.floor(i / 12) * 45;
            ctx.fillStyle = i < lightCount ? '#F7F5EF' : '#1B1B1B';
            ctx.beginPath();
            ctx.ellipse(x, y, 10, 6, 0, 0, Math.PI * 2);
            ctx.fill();
        }
    }, [selectionPressure, lightAlleleFreq]);

    return (
        <LabLayout title="Evolusi & Seleksi Alam" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={440} height={280} className="max-w-full rounded" />
                </div>
                <div className="control-panel space-y-5">
                    <p className="text-xs text-slate/70">
                        Contoh: populasi kupu-kupu terang vs gelap. Lingkungan yang lebih gelap (jelaga pabrik)
                        membuat kupu-kupu gelap lebih tersamar dari predator.
                    </p>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Tingkat kegelapan lingkungan</span><span className="dial-readout">{selectionPressure}%</span></div>
                        <input type="range" min={0} max={100} value={selectionPressure} onChange={(e) => setSelectionPressure(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <button onClick={() => setRunning((r) => !r)} className="w-full rounded-lg bg-amber text-ink font-medium py-2">
                        {running ? 'Jeda Simulasi Generasi' : 'Jalankan Simulasi Generasi'}
                    </button>
                    <div className="pt-2 border-t border-slate/15 space-y-1">
                        <div className="flex justify-between"><span className="text-slate">Generasi ke-</span><span className="dial-readout">{generation}</span></div>
                        <div className="flex justify-between"><span className="text-slate">Frekuensi alel terang</span><span className="dial-readout">{lightAlleleFreq.toFixed(0)}%</span></div>
                    </div>
                </div>
            </div>
        </LabLayout>
    );
}
