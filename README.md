# Portal sekolah.co.id — dengan modul Lab Interaktif

Scaffold **portal sekolah lengkap** (Inertia + React di atas Laravel 13):
landing page publik, dashboard setelah login, dan sidebar navigasi dengan
modul-modul sekolah. **Lab Interaktif** (3 contoh simulasi bergaya PhET:
Bandul, Gerak Peluru, Rangkaian Listrik) menyatu sebagai **submenu yang bisa
expand** di sidebar — bukan aplikasi terpisah. Modul lain (Server Ujian, Buku
Induk, Program BK, Manajemen Sekolah) sudah punya slot di menu, tinggal
diaktifkan saat modulnya dibangun.

## Wajib: portal ini butuh Laravel Breeze (untuk login/register)

`routes/web.php` sudah menyertakan `require __DIR__.'/auth.php';` dan halaman
`Welcome.jsx` mengarah ke `/login` & `/register`. Kalau Breeze belum terpasang,
route itu akan error. Pasang dulu:

```bash
composer require laravel/breeze --dev
php artisan breeze:install react
```
Breeze akan generate `routes/auth.php`, halaman login/register React, dan
migration tabel `users`.

## 0. Struktur menu portal

Menu sidebar didefinisikan di satu tempat: `resources/js/Layouts/PortalLayout.jsx`,
di dalam array `MENU`. Untuk mengaktifkan modul baru (misal Server Ujian),
tinggal ubah entri yang sudah ada dari `disabled: true` jadi punya `href` yang
benar, atau tambah `children` kalau modul itu juga punya beberapa submenu
seperti Lab.

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

## 6. Modul Ajar - file asli, bukan link karangan

Katalog `/modul-ajar` berisi 36 modul (Matematika, IPA, IPS, Informatika, PJOK
kelas 7) yang **sudah diverifikasi** punya file DOCX asli di Google Drive
(sumber: modulguruku.com). Supaya file-nya benar-benar ter-host di server
sendiri (bukan cuma link ke Drive), jalankan:

```bash
cd /www/wwwroot/sekolah.co.id/sekolahcoid
bash scripts/download-modul-ajar.sh
php artisan storage:link   # cuma perlu sekali kalau belum pernah
```

Script ini men-download 36 file ke `storage/app/public/modul-ajar/`.
`ModulAjarController` otomatis mendeteksi file lokal itu dan mengganti tombol
unduh untuk pakai file dari server sendiri - fallback ke link Google Drive
untuk modul yang belum ter-download, tanpa perlu ubah kode apa pun.

Kalau ada baris "PERINGATAN" saat script jalan, itu tandanya file tersebut
gagal ke-download langsung (biasanya karena Google Drive minta konfirmasi
tambahan untuk file besar) - cek link aslinya manual di
`scripts/download-modul-ajar.sh` (cari ID file-nya) dan download manual kalau
perlu, lalu taruh manual ke folder yang sama dengan nama file yang sesuai.

**Menambah modul baru:** tambahkan entri baru di `ModulAjarController::CATALOG`
dengan `file_key` unik, lalu taruh file docx-nya di
`storage/app/public/modul-ajar/{file_key}.docx` (upload manual, atau lewat
panel admin kalau nanti sudah dibangun).

## 7. Registrasi Sekolah via NPSN

Alur baru di `/registrasi-sekolah`: masukkan NPSN → sistem ambil data sekolah
otomatis dari data referensi resmi Kemendikdasmen (publik, tanpa API key,
`https://referensi.data.kemendikdasmen.go.id/pendidikan/npsn/{npsn}`) →
konfirmasi → lengkapi akun admin.

**Wajib dijalankan setelah pull:**
```bash
php artisan migrate
```
Ini membuat tabel `sekolahs` dan menambahkan kolom `sekolah_id` + `role` ke
tabel `users`.

**Wajib diedit manual** (karena `app/Models/User.php` dibuat oleh Breeze, bukan
oleh scaffold ini, jadi tidak disentuh otomatis lewat git supaya tidak
tertimpa): tambahkan relasi berikut ke `app/Models/User.php`:

```php
use App\Models\Sekolah;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// tambahkan 'sekolah_id' dan 'role' ke array $fillable yang sudah ada, lalu:
public function sekolah(): BelongsTo
{
    return $this->belongsTo(Sekolah::class);
}
```

**Catatan soal keandalan lookup NPSN:** `NpsnLookupController` mem-parsing
halaman HTML publik Kemendikdasmen dengan regex (situs itu tidak
menyediakan JSON API resmi). Ini cukup stabil selama struktur halaman mereka
tidak berubah drastis, tapi kalau suatu saat lookup berhenti bekerja
(misal field yang kembali `null` semua), kemungkinan besar karena format
halaman referensi berubah - cek ulang pola regex di controller tersebut
dengan `curl https://referensi.data.kemendikdasmen.go.id/pendidikan/npsn/{npsn}`
langsung dari server buat lihat HTML terbaru.

## 8. Integrasi modul Buku Induk (dari github.com/grahardi/buku-induk)

Modul Buku Induk Siswa (dari repo terpisah) sudah digabung ke sini dengan
penyesuaian:

- **Auth disatukan** — modul ini tidak lagi punya login sendiri, pakai Breeze
  yang sudah ada di portal (route `logout` di layout-nya otomatis nyambung).
- **Multi-sekolah otomatis** — model `Siswa` punya global scope yang
  memfilter berdasarkan `sekolah_id` milik user yang login, jadi tidak perlu
  ubah query di tiap controller satu-satu. Siswa baru otomatis ke-assign ke
  sekolah admin yang membuatnya.
- **Tampilan masih terpisah** — modul ini pakai layout Blade sendiri (sidebar
  biru gelap + Tabler Icons), belum diselaraskan ke tema cream/teal/coral
  portal utama. Fungsional dulu, polish visual menyusul kalau diperlukan.

**Wajib dijalankan setelah pull:**
```bash
composer require rap2hpoutre/fast-excel barryvdh/laravel-dompdf
php artisan migrate
php artisan storage:link   # kalau belum pernah
```

**Wajib ditambahkan manual ke `bootstrap/app.php`** (alias middleware `admin`
dipakai di `routes/buku-induk.php`):
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->web(append: [
        \App\Http\Middleware\HandleInertiaRequests::class,
    ]);
    $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureIsAdmin::class,
    ]);
})
```

Setelah itu, modul bisa diakses di `/buku-induk` (menu sidebar "Buku Induk"
sudah aktif). Akun admin yang daftar lewat `/registrasi-sekolah` otomatis
punya `role => 'admin'`, jadi bisa langsung tambah data siswa.
