import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

export default function OpticsLens({ category = 'Fisika' }) {
    const canvasRef = useRef(null);
    const [focalLength, setFocalLength] = useState(80);
    const [objectDistance, setObjectDistance] = useState(150);
    const [objectHeight, setObjectHeight] = useState(50);

    // Rumus lensa tipis: 1/f = 1/do + 1/di
    const di = 1 / (1 / focalLength - 1 / objectDistance);
    const magnification = -di / objectDistance;
    const imageHeight = magnification * objectHeight;
    const isVirtual = di < 0 || !isFinite(di);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const centerX = canvas.width / 2;
        const axisY = canvas.height / 2;
        const scale = 1;

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Sumbu utama
        ctx.strokeStyle = '#7C8AA5';
        ctx.beginPath();
        ctx.moveTo(0, axisY);
        ctx.lineTo(canvas.width, axisY);
        ctx.stroke();

        // Lensa (elips vertikal)
        ctx.strokeStyle = '#FBBF24';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.ellipse(centerX, axisY, 10, 100, 0, 0, Math.PI * 2);
        ctx.stroke();

        // Titik fokus
        ctx.fillStyle = '#7C8AA5';
        [centerX - focalLength, centerX + focalLength].forEach((fx) => {
            ctx.beginPath();
            ctx.arc(fx, axisY, 3, 0, Math.PI * 2);
            ctx.fill();
        });

        const objX = centerX - objectDistance;
        const objTopY = axisY - objectHeight;

        // Panah benda
        ctx.strokeStyle = '#F7F5EF';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(objX, axisY);
        ctx.lineTo(objX, objTopY);
        ctx.stroke();
        ctx.fillStyle = '#F7F5EF';
        ctx.beginPath();
        ctx.moveTo(objX, objTopY);
        ctx.lineTo(objX - 5, objTopY + 10);
        ctx.lineTo(objX + 5, objTopY + 10);
        ctx.fill();

        if (isFinite(di)) {
            const imgX = centerX + di;
            const imgTopY = axisY - imageHeight;
            const color = isVirtual ? 'rgba(230,57,70,0.7)' : 'rgba(143,211,254,0.9)';

            // Sinar sejajar sumbu -> lewat fokus
            ctx.strokeStyle = color;
            ctx.beginPath();
            ctx.moveTo(objX, objTopY);
            ctx.lineTo(centerX, objTopY);
            ctx.lineTo(imgX, imgTopY);
            ctx.stroke();

            // Sinar lewat pusat lensa -> lurus
            ctx.beginPath();
            ctx.moveTo(objX, objTopY);
            ctx.lineTo(imgX, imgTopY);
            ctx.stroke();

            // Panah bayangan
            ctx.strokeStyle = color;
            ctx.beginPath();
            ctx.moveTo(imgX, axisY);
            ctx.lineTo(imgX, imgTopY);
            ctx.stroke();
        }
    }, [focalLength, objectDistance, objectHeight, di, imageHeight, isVirtual]);

    return (
        <LabLayout title="Optik: Lensa & Cermin" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4 overflow-hidden">
                    <canvas ref={canvasRef} width={520} height={320} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Jarak fokus (f)</span><span className="dial-readout">{focalLength} px</span></div>
                        <input type="range" min={40} max={150} value={focalLength} onChange={(e) => setFocalLength(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Jarak benda (do)</span><span className="dial-readout">{objectDistance} px</span></div>
                        <input type="range" min={30} max={260} value={objectDistance} onChange={(e) => setObjectDistance(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Tinggi benda</span><span className="dial-readout">{objectHeight} px</span></div>
                        <input type="range" min={20} max={100} value={objectHeight} onChange={(e) => setObjectHeight(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15 space-y-1">
                        <div className="flex justify-between"><span className="text-slate">Sifat bayangan</span><span className="dial-readout">{isVirtual ? 'Maya' : 'Nyata'}</span></div>
                        <div className="flex justify-between"><span className="text-slate">Perbesaran</span><span className="dial-readout">{Math.abs(magnification).toFixed(2)}x</span></div>
                    </div>
                </div>
            </div>
        </LabLayout>
    );
}
