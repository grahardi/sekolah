import { useEffect, useState } from 'react';

/**
 * Slider showcase - dibuat generic supaya gampang diisi konten asli nanti.
 * Tinggal ganti array `slides` di Welcome.jsx (judul, deskripsi, gambar/warna
 * aksen, dan link tujuan). Auto-geser tiap 5 detik, bisa juga digeser manual.
 */
export default function ShowcaseSlider({ slides }) {
    const [active, setActive] = useState(0);

    useEffect(() => {
        const timer = setInterval(() => {
            setActive((i) => (i + 1) % slides.length);
        }, 5000);
        return () => clearInterval(timer);
    }, [slides.length]);

    const goTo = (i) => setActive(i);
    const prev = () => setActive((i) => (i - 1 + slides.length) % slides.length);
    const next = () => setActive((i) => (i + 1) % slides.length);

    const slide = slides[active];

    return (
        <div className="relative rounded-2xl overflow-hidden bg-navy" style={{ minHeight: '340px' }}>
            {slides.map((s, i) => (
                <a
                    key={i}
                    href={s.href || '#'}
                    className={`absolute inset-0 transition-opacity duration-700 ${i === active ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'}`}
                    style={{ background: s.background || 'linear-gradient(135deg, #1E293B, #2563EB)' }}
                >
                    <div className="flex flex-col justify-end h-full p-8 lg:p-12">
                        <span className="text-xs font-mono uppercase tracking-wide text-coral mb-2">{s.tag}</span>
                        <h3 className="font-display font-700 text-2xl lg:text-3xl text-white max-w-lg">{s.title}</h3>
                        <p className="text-white/70 mt-2 max-w-lg text-sm lg:text-base">{s.desc}</p>
                    </div>
                </a>
            ))}

            {/* Panah navigasi */}
            <button
                onClick={(e) => { e.preventDefault(); prev(); }}
                className="absolute left-4 top-1/2 -translate-y-1/2 z-20 h-9 w-9 rounded-full bg-white/15 hover:bg-white/25 text-white flex items-center justify-center backdrop-blur"
                aria-label="Sebelumnya"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="w-5 h-5"><path d="M15 18l-6-6 6-6" /></svg>
            </button>
            <button
                onClick={(e) => { e.preventDefault(); next(); }}
                className="absolute right-4 top-1/2 -translate-y-1/2 z-20 h-9 w-9 rounded-full bg-white/15 hover:bg-white/25 text-white flex items-center justify-center backdrop-blur"
                aria-label="Berikutnya"
            >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" className="w-5 h-5"><path d="M9 18l6-6-6-6" /></svg>
            </button>

            {/* Titik indikator */}
            <div className="absolute bottom-4 right-6 z-20 flex gap-2">
                {slides.map((_, i) => (
                    <button
                        key={i}
                        onClick={(e) => { e.preventDefault(); goTo(i); }}
                        className={`h-2 rounded-full transition-all ${i === active ? 'w-6 bg-coral' : 'w-2 bg-white/40 hover:bg-white/60'}`}
                        aria-label={`Slide ${i + 1}`}
                    />
                ))}
            </div>
        </div>
    );
}
