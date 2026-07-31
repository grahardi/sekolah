import { useEffect, useState } from 'react';

/**
 * Slider showcase / hero. Slide pertama tipe "hero" (badge + judul besar +
 * tombol CTA, konten di tengah, warna solid). Slide lain tipe "split" -
 * teks di kiri, ilustrasi vektor fiktif (bukan foto orang asli) di kanan,
 * di atas warna solid brand - rapi & presisi, tidak ada gradient belang.
 */
export default function ShowcaseSlider({ slides }) {
    const [active, setActive] = useState(0);

    useEffect(() => {
        const timer = setInterval(() => {
            setActive((i) => (i + 1) % slides.length);
        }, 6000);
        return () => clearInterval(timer);
    }, [slides.length]);

    const goTo = (i) => setActive(i);
    const prev = () => setActive((i) => (i - 1 + slides.length) % slides.length);
    const next = () => setActive((i) => (i + 1) % slides.length);

    return (
        <div className="relative rounded-2xl overflow-hidden bg-navy" style={{ minHeight: '420px' }}>
            {slides.map((s, i) => {
                const isActive = i === active;
                const Illustration = s.illustration;

                if (s.type === 'hero') {
                    return (
                        <div
                            key={i}
                            className={`absolute inset-0 transition-opacity duration-700 flex items-center justify-center text-center ${isActive ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'}`}
                            style={{ background: s.background || '#1E293B' }}
                        >
                            <div className="max-w-xl px-6">
                                {s.tag && (
                                    <span className="inline-block text-xs font-mono uppercase tracking-wide text-coral bg-white/10 rounded-full px-3 py-1 mb-4">
                                        {s.tag}
                                    </span>
                                )}
                                <h1 className="font-display font-700 text-3xl lg:text-5xl text-white leading-tight">{s.title}</h1>
                                {s.desc && <p className="text-white/70 mt-4 text-sm lg:text-base">{s.desc}</p>}
                                {s.buttons && (
                                    <div className="flex items-center justify-center gap-3 mt-7">
                                        {s.buttons.map((b, bi) => (
                                            <a
                                                key={bi}
                                                href={b.href}
                                                className={`font-medium rounded-lg px-5 py-3 text-sm ${
                                                    b.variant === 'secondary'
                                                        ? 'border border-white/30 text-white hover:bg-white/10'
                                                        : 'bg-coral text-navy hover:brightness-95'
                                                }`}
                                            >
                                                {b.label}
                                            </a>
                                        ))}
                                    </div>
                                )}
                            </div>
                        </div>
                    );
                }

                return (
                    <a
                        key={i}
                        href={s.href || '#'}
                        className={`absolute inset-0 transition-opacity duration-700 grid grid-cols-1 md:grid-cols-2 items-center ${isActive ? 'opacity-100 z-10' : 'opacity-0 z-0 pointer-events-none'}`}
                        style={{ background: s.background || '#1E293B' }}
                    >
                        <div className="p-8 lg:p-12">
                            <span className="text-xs font-mono uppercase tracking-wide text-coral mb-2 inline-block">{s.tag}</span>
                            <h3 className="font-display font-700 text-2xl lg:text-3xl text-white max-w-md">{s.title}</h3>
                            <p className="text-white/70 mt-2 max-w-md text-sm lg:text-base">{s.desc}</p>
                        </div>
                        <div className="hidden md:flex items-center justify-center p-8">
                            {Illustration && <Illustration className="w-56 h-56 lg:w-72 lg:h-72" />}
                        </div>
                    </a>
                );
            })}

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
            <div className="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-2">
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
