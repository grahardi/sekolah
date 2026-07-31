// Logo resmi SekolahCoID (file asli di public/images/).
// `variant="full"` = ikon + wordmark lengkap (navbar publik, sidebar portal).
// `variant="mark"` = cuma ikon pohon+buku+topi, tanpa tulisan (favicon-style,
// ruang sempit).
export default function Logo({ variant = 'full', className = '', light = false }) {
    if (variant === 'mark') {
        return (
            <img
                src="/images/logo-icon.png"
                alt="sekolah.co.id"
                className={`h-8 w-8 object-contain ${className}`}
            />
        );
    }

    return (
        <img
            src="/images/logo.png"
            alt="sekolah.co.id"
            className={`h-9 w-auto object-contain ${className}`}
        />
    );
}
