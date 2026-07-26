import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.jsx',
    ],
    theme: {
        extend: {
            colors: {
                // -- Palet portal (tema edukasi, terang, hangat) --
                cream: '#FBF7EF',      // background utama portal
                teal: '#0F5132',       // primer - sidebar, header, identitas sekolah
                'teal-light': '#E8F3EC',
                coral: '#F4A300',      // aksen CTA - konsisten dgn "amber" di modul Lab
                navy: '#1F2937',       // teks utama di atas latar terang

                // -- Palet modul Lab (tema "instrumen lab", gelap) --
                ink: '#16213E',        // background panel simulasi
                lab: '#1B4332',        // aksen sekunder - hijau papan lab
                amber: '#F4A300',      // aksen utama - tombol & sorotan (=coral)
                paper: '#F7F5EF',      // kartu / area kertas di atas ink
                slate: '#7C8AA5',      // teks sekunder di atas ink
                alert: '#E63946',      // reset / peringatan
            },
            fontFamily: {
                display: ['"Space Grotesk"', 'sans-serif'],
                body: ['"Inter"', 'sans-serif'],
                mono: ['"IBM Plex Mono"', 'monospace'],
                sans: ['"Inter"', 'sans-serif'],
            },
            backgroundImage: {
                'grid-paper': "linear-gradient(rgba(124,138,165,0.08) 1px, transparent 1px), linear-gradient(90deg, rgba(124,138,165,0.08) 1px, transparent 1px)",
            },
            backgroundSize: {
                'grid-24': '24px 24px',
            },
        },
    },
    plugins: [forms],
};
