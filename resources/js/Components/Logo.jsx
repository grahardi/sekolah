// Logo SekolahCoID - dibuat sebagai SVG inline, tidak butuh file gambar terpisah.
// `variant="full"` menampilkan ikon + wordmark (untuk navbar/footer).
// `variant="mark"` cuma menampilkan ikon (untuk favicon/ruang sempit).
export default function Logo({ variant = 'full', className = '', light = false }) {
    const wordColor = light ? '#FFFFFF' : '#1E293B';
    const accentColor = light ? '#FBBF24' : '#2563EB';

    const Mark = (
        <svg viewBox="0 0 48 48" className="h-8 w-8 shrink-0" aria-hidden="true">
            <circle cx="24" cy="24" r="22" fill="#2563EB" />
            {/* Topi wisuda sebagai simbol edukasi */}
            <path
                d="M24 14 L40 20.5 L24 27 L8 20.5 Z"
                fill="#FBBF24"
            />
            <path
                d="M14 23.2 V30 c0 2.2 4.5 4 10 4 s10-1.8 10-4 v-6.8"
                stroke="#FFFFFF"
                strokeWidth="2"
                fill="none"
                strokeLinecap="round"
            />
            <line x1="40" y1="20.5" x2="40" y2="28" stroke="#FFFFFF" strokeWidth="2" strokeLinecap="round" />
            <circle cx="40" cy="30" r="1.6" fill="#FFFFFF" />
        </svg>
    );

    if (variant === 'mark') {
        return <div className={className}>{Mark}</div>;
    }

    return (
        <div className={`flex items-center gap-2.5 ${className}`}>
            {Mark}
            <span className="font-display font-700 text-lg leading-none" style={{ color: wordColor }}>
                sekolah<span style={{ color: accentColor }}>.co.id</span>
            </span>
        </div>
    );
}
