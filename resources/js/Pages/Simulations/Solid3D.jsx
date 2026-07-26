import { useEffect, useRef, useState } from 'react';
import LabLayout from '../../Layouts/LabLayout';

// Proyeksi pseudo-3D sederhana (isometrik) untuk balok, tanpa dependency tambahan.
export default function Solid3D({ category = 'Matematika' }) {
    const canvasRef = useRef(null);
    const [rotation, setRotation] = useState(30);
    const [length, setLength] = useState(80);
    const [width, setWidth] = useState(60);
    const [height, setHeight] = useState(70);

    const volume = length * width * height;
    const surfaceArea = 2 * (length * width + length * height + width * height);

    useEffect(() => {
        const canvas = canvasRef.current;
        const ctx = canvas.getContext('2d');
        const cx = canvas.width / 2;
        const cy = canvas.height / 2 + 40;
        const rad = (rotation * Math.PI) / 180;

        // Proyeksi isometrik dgn rotasi horizontal
        const project = (x, y, z) => {
            const rx = x * Math.cos(rad) - z * Math.sin(rad);
            const rz = x * Math.sin(rad) + z * Math.cos(rad);
            return [cx + rx - rz * 0.5, cy - y - rz * 0.3];
        };

        const l = length / 2, w = width / 2, h = height / 2;
        const vertices = [
            [-l, -h, -w], [l, -h, -w], [l, h, -w], [-l, h, -w],
            [-l, -h, w], [l, -h, w], [l, h, w], [-l, h, w],
        ].map(([x, y, z]) => project(x, y, z));

        const edges = [
            [0, 1], [1, 2], [2, 3], [3, 0],
            [4, 5], [5, 6], [6, 7], [7, 4],
            [0, 4], [1, 5], [2, 6], [3, 7],
        ];

        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Sisi depan diarsir tipis biar terasa solid
        ctx.fillStyle = 'rgba(251,191,36,0.15)';
        ctx.beginPath();
        [0, 1, 2, 3].forEach((i, idx) => {
            const [x, y] = vertices[i];
            idx === 0 ? ctx.moveTo(x, y) : ctx.lineTo(x, y);
        });
        ctx.closePath();
        ctx.fill();

        ctx.strokeStyle = '#FBBF24';
        ctx.lineWidth = 2;
        edges.forEach(([a, b]) => {
            ctx.beginPath();
            ctx.moveTo(vertices[a][0], vertices[a][1]);
            ctx.lineTo(vertices[b][0], vertices[b][1]);
            ctx.stroke();
        });
    }, [rotation, length, width, height]);

    return (
        <LabLayout title="Bangun Ruang 3D" breadcrumb={['Home', 'Simulasi', category]}>
            <div className="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
                <div className="rounded-2xl bg-[#0F1729] border border-slate/20 flex items-center justify-center p-4">
                    <canvas ref={canvasRef} width={420} height={380} className="max-w-full" />
                </div>
                <div className="control-panel space-y-5">
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Rotasi</span><span className="dial-readout">{rotation}°</span></div>
                        <input type="range" min={0} max={360} value={rotation} onChange={(e) => setRotation(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Panjang</span><span className="dial-readout">{length}</span></div>
                        <input type="range" min={30} max={120} value={length} onChange={(e) => setLength(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Lebar</span><span className="dial-readout">{width}</span></div>
                        <input type="range" min={30} max={120} value={width} onChange={(e) => setWidth(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div>
                        <div className="flex justify-between text-slate mb-1"><span>Tinggi</span><span className="dial-readout">{height}</span></div>
                        <input type="range" min={30} max={120} value={height} onChange={(e) => setHeight(Number(e.target.value))} className="w-full accent-amber" />
                    </div>
                    <div className="pt-2 border-t border-slate/15 space-y-1">
                        <div className="flex justify-between"><span className="text-slate">Volume (p×l×t)</span><span className="dial-readout">{(volume / 1000).toFixed(1)}k</span></div>
                        <div className="flex justify-between"><span className="text-slate">Luas permukaan</span><span className="dial-readout">{(surfaceArea / 1000).toFixed(1)}k</span></div>
                    </div>
                </div>
            </div>
        </LabLayout>
    );
}
