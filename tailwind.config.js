/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.jsx',
    ],
    theme: {
        extend: {
            colors: {
                ink: '#16213E',        // background utama - "papan tulis" gelap
                lab: '#1B4332',        // aksen sekunder - hijau papan lab
                amber: '#F4A300',      // aksen utama - tombol & sorotan
                paper: '#F7F5EF',      // kartu / area kertas
                slate: '#7C8AA5',      // teks sekunder
                alert: '#E63946',      // reset / peringatan
            },
            fontFamily: {
                display: ['"Space Grotesk"', 'sans-serif'],
                body: ['"Inter"', 'sans-serif'],
                mono: ['"IBM Plex Mono"', 'monospace'],
            },
            backgroundImage: {
                'grid-paper': "linear-gradient(rgba(124,138,165,0.08) 1px, transparent 1px), linear-gradient(90deg, rgba(124,138,165,0.08) 1px, transparent 1px)",
            },
            backgroundSize: {
                'grid-24': '24px 24px',
            },
        },
    },
    plugins: [],
};
