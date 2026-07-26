# Laboratorium Interaktif — modul sekolah.co.id

Scaffold Inertia + React di atas Laravel 13, plus 3 contoh simulasi interaktif
bergaya PhET (Bandul, Gerak Peluru, Rangkaian Listrik). Ini dirancang sebagai
**satu modul** di antara modul lain (Server Ujian, Buku Induk, Program BK,
Manajemen Sekolah) yang nanti ada di aplikasi Laravel yang sama — makanya route
di-prefix `/lab` dan sidebar di `AppLayout.jsx` sudah menyisakan slot untuk
modul-modul itu.

## 1. Buat project Laravel 13 (kalau belum ada)

```bash
composer create-project laravel/laravel sekolah-co-id
cd sekolah-co-id
composer require inertiajs/inertia-laravel tightenco/ziggy
php artisan inertia:middleware
```//
Tambahkan `\App\Http\Middleware\HandleInertiaRequests::class` ke grup `web` di
`bootstrap/app.php` (Laravel 13 pakai konfigurasi middleware baru di file ini,
bukan `Kernel.php` lagi).

## 2. Salin file dari scaffold ini

Timpa/tambahkan ke project Laravel-mu:

```
composer.json          -> gabungkan dependency-nya ke composer.json project (jangan overwrite langsung)
package.json            -> project root
vite.config.js           -> project root
tailwind.config.js       -> project root
postcss.config.js        -> project root
resources/css/app.css    -> resources/css/app.css
resources/views/app.blade.php -> resources/views/app.blade.php
resources/js/            -> resources/js/
routes/web.php           -> gabungkan isinya ke routes/web.php project
app/Http/Controllers/SimulationController.php -> app/Http/Controllers/
```

## 3. Install dependency & jalankan

```bash
composer require inertiajs/inertia-laravel tightenco/ziggy
npm install
npm install -D tailwindcss postcss autoprefixer laravel-vite-plugin @vitejs/plugin-react
npm install @inertiajs/react react react-dom matter-js

php artisan serve
npm run dev
```

Buka `http://localhost:8000/lab` (perlu login karena route dibungkus middleware
`auth` — sesuaikan dengan sistem otentikasi guru/siswa yang sudah ada).

## 3b. Alternatif: pakai simulasi PhET langsung (tanpa bikin dari nol)

Untuk topik yang PhET sudah punya dan sudah tersedia bahasa Indonesia, bisa
langsung embed lewat iframe — jauh lebih cepat daripada bikin ulang:

```jsx
<iframe
  src="https://phet.colorado.edu/sims/html/pendulum-lab/latest/pendulum-lab_in.html"
  className="w-full aspect-video rounded-2xl border border-slate/20"
  allowFullScreen
/>
```

Tinggal buat entri baru di `SimulationController::CATALOG` dengan
`component => 'Simulations/PhetEmbed'` dan komponen React kecil yang menerima
`url` sebagai prop. PhET dirilis dengan lisensi CC BY 4.0 untuk pemakaian
pendidikan.

## 4. Menambah simulasi baru

1. Tambah entri di `SimulationController::CATALOG` (slug, judul, mapel, deskripsi,
   nama komponen).
2. Buat file baru di `resources/js/Pages/Simulations/NamaSimulasi.jsx`, contoh
   struktur bisa dicontek dari `Pendulum.jsx` (canvas 2D manual) atau kalau butuh
   physics engine yang lebih rumit (tabrakan, banyak benda), pakai `matter-js`
   yang sudah didaftarkan di `package.json`.
3. Simulasi otomatis muncul di katalog `/lab`.

## 5. Kenapa struktur ini dipilih

- **Inertia** dipakai supaya routing & auth tetap di Laravel biasa (controller,
  middleware, policy) — tidak perlu bikin REST API + SPA terpisah, cocok untuk
  aplikasi yang mayoritas kontennya server-rendered (jadwal, nilai, dsb) tapi
  butuh beberapa halaman sangat interaktif.
- **Canvas API manual** dipakai di 3 contoh ini (bukan Matter.js) supaya kodenya
  mudah dibaca guru/developer yang belum kenal physics engine. `matter-js` tetap
  disiapkan di `package.json` untuk simulasi yang butuh tabrakan/banyak benda
  (misal simulasi gas kinetik, tumbukan bola).
- **Desain "panel instrumen lab"** (font mono untuk angka, warna amber sebagai
  aksen kontrol) dipilih supaya modul ini terasa berbeda dari modul administratif
  lain (buku induk, manajemen sekolah) yang biasanya bergaya tabel/form standar.
