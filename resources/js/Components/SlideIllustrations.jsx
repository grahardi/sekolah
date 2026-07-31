// Ilustrasi flat/vector fiktif (bukan foto orang asli) untuk tiap slide showcase.
// Gaya minimalis geometris, konsisten dengan palet brand (biru/kuning/navy).

export function BukuIndukIllustration(props) {
    return (
        <svg viewBox="0 0 400 400" {...props}>
            <circle cx="200" cy="200" r="160" fill="#FFFFFF" fillOpacity="0.06" />
            {/* Kartu data siswa */}
            <rect x="90" y="110" width="180" height="220" rx="14" fill="#FFFFFF" fillOpacity="0.95" />
            <circle cx="180" cy="165" r="28" fill="#2563EB" fillOpacity="0.85" />
            <rect x="115" y="210" width="130" height="10" rx="5" fill="#CBD5E1" />
            <rect x="115" y="232" width="100" height="10" rx="5" fill="#CBD5E1" />
            <rect x="115" y="254" width="115" height="10" rx="5" fill="#CBD5E1" />
            <rect x="115" y="286" width="90" height="14" rx="7" fill="#FBBF24" />
            {/* Panah sinkron dari cloud (Dapodik) ke kartu */}
            <circle cx="290" cy="120" r="34" fill="#FBBF24" fillOpacity="0.9" />
            <path d="M280 108 h20 M280 120 h24 M280 132 h16" stroke="#1E293B" strokeWidth="4" strokeLinecap="round" />
            <path d="M255 130 Q220 150 210 180" stroke="#FFFFFF" strokeWidth="4" fill="none" strokeDasharray="6 6" />
            <path d="M205 178 l10 -4 l2 11 z" fill="#FFFFFF" />
        </svg>
    );
}

export function LabIllustration(props) {
    return (
        <svg viewBox="0 0 400 400" {...props}>
            <circle cx="200" cy="200" r="160" fill="#FFFFFF" fillOpacity="0.06" />
            {/* Labu erlenmeyer */}
            <path d="M175 120 v60 l-55 90 a20 20 0 0018 30 h124 a20 20 0 0018-30 l-55-90 v-60 z" fill="#FFFFFF" fillOpacity="0.95" />
            <rect x="168" y="108" width="64" height="16" rx="6" fill="#CBD5E1" />
            <path d="M135 245 h130 v45 a20 20 0 01-20 20 h-90 a20 20 0 01-20-20 z" fill="#3ABF6B" fillOpacity="0.85" />
            {/* Gelembung */}
            <circle cx="185" cy="230" r="7" fill="#FFFFFF" fillOpacity="0.8" />
            <circle cx="210" cy="210" r="5" fill="#FFFFFF" fillOpacity="0.7" />
            <circle cx="200" cy="185" r="4" fill="#FFFFFF" fillOpacity="0.6" />
            {/* Molekul mengorbit */}
            <circle cx="300" cy="150" r="8" fill="#FBBF24" />
            <circle cx="330" cy="130" r="6" fill="#FBBF24" fillOpacity="0.7" />
            <circle cx="315" cy="175" r="5" fill="#FBBF24" fillOpacity="0.5" />
            <path d="M300 150 L330 130 M300 150 L315 175" stroke="#FBBF24" strokeWidth="2" />
        </svg>
    );
}

export function ModulAjarIllustration(props) {
    return (
        <svg viewBox="0 0 400 400" {...props}>
            <circle cx="200" cy="200" r="160" fill="#FFFFFF" fillOpacity="0.06" />
            {/* Buku terbuka */}
            <path d="M200 140 C170 120 120 118 90 130 v130 c30 -12 80 -10 110 10 z" fill="#FFFFFF" fillOpacity="0.95" />
            <path d="M200 140 C230 120 280 118 310 130 v130 c-30 -12 -80 -10 -110 10 z" fill="#E8F3EC" />
            <path d="M110 155 q35 -10 70 4 M110 178 q35 -10 70 4 M110 201 q35 -10 70 4" stroke="#94A3B8" strokeWidth="3" fill="none" strokeLinecap="round" />
            <path d="M220 159 q35 -14 70 0 M220 182 q35 -14 70 0 M220 205 q35 -14 70 0" stroke="#94A3B8" strokeWidth="3" fill="none" strokeLinecap="round" />
            {/* Bola lampu ide */}
            <circle cx="290" cy="105" r="26" fill="#FBBF24" fillOpacity="0.9" />
            <rect x="280" y="128" width="20" height="10" rx="3" fill="#CBD5E1" />
            <path d="M278 100 h24 M282 92 h16" stroke="#1E293B" strokeWidth="3" strokeLinecap="round" />
        </svg>
    );
}
