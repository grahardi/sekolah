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
                // -- Palet utama portal: biru muda, kuning, putih --
                cream: '#F5F9FF',      // background utama - putih kebiruan
                teal: '#2563EB',       // primer - biru untuk header, tombol, identitas
                'teal-light': '#DCEAFE', // biru sangat muda - background section/badge
                coral: '#FBBF24',      // aksen kuning - CTA & sorotan
                navy: '#1E293B',       // teks utama (gelap, netral, tetap kontras di atas biru muda/putih)

                // -- Palet modul Lab (tema "instrumen lab", gelap - khusus panel simulasi) --
                ink: '#16213E',
                lab: '#1B4332',
                amber: '#FBBF24',      // disamakan dgn kuning aksen portal
                paper: '#F7F5EF',
                slate: '#7C8AA5',
                alert: '#E63946',
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
