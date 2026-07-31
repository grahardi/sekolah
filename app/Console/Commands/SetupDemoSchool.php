<?php

namespace App\Console\Commands;

use App\Models\ArsipBerkas;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SetupDemoSchool extends Command
{
    protected $signature = 'demo:setup {--reset : Hapus dulu data demo lama sebelum membuat ulang}';
    protected $description = 'Siapkan sekolah demo (data & berkas acak) untuk keperluan showcase Buku Induk, akses read-only';

    private const NAMA_DEPAN = ['Ahmad','Muhammad','Budi','Andi','Bayu','Dimas','Eko','Fajar','Gilang','Hendra','Indra','Joko','Krisna','Lukman','Mahesa','Nanda','Oscar','Putra','Rafi','Satrio','Aisyah','Bunga','Citra','Dewi','Eka','Fitri','Gita','Hana','Intan','Kartika','Larasati','Mega','Nadia','Olivia','Putri','Ratna','Sari','Tania','Utari','Wulan'];
    private const NAMA_BELAKANG = ['Pratama','Saputra','Wijaya','Kusuma','Santoso','Nugroho','Firmansyah','Setiawan','Permana','Hidayat','Ramadhan','Maulana','Anggraini','Lestari','Wardani','Puspita','Kurniawan','Suryani','Rahmawati','Handayani'];

    public function handle(): int
    {
        if ($this->option('reset')) {
            $old = Sekolah::where('is_demo', true)->first();
            if ($old) {
                $this->warn('Menghapus data demo lama...');
                foreach ($old->siswas as $siswa) {
                    if ($siswa->foto) Storage::disk('public')->delete($siswa->foto);
                    $arsip = $siswa->arsipBerkas;
                    if ($arsip) {
                        foreach (['kartu_keluarga','akta_lahir','ijazah_sd'] as $f) {
                            if ($arsip->{$f}) Storage::disk('public')->delete($arsip->{$f});
                        }
                        $arsip->delete();
                    }
                    $siswa->forceDelete();
                }
                $old->users()->delete();
                $old->delete();
            }
        }

        $this->info('Membuat sekolah demo...');
        $sekolah = Sekolah::create([
            'npsn' => '00000000',
            'nama' => 'SMP Negeri Contoh (Demo)',
            'is_demo' => true,
            'alamat' => 'Jalan Contoh Demo No. 1',
            'kecamatan' => 'Kecamatan Contoh',
            'kabupaten_kota' => 'Kabupaten Contoh',
            'provinsi' => 'Jawa Timur',
            'status_sekolah' => 'Negeri',
            'bentuk_pendidikan' => 'SMP',
            'jenjang_pendidikan' => 'DIKDAS',
        ]);

        $email = 'demo@sekolah.co.id';
        $password = 'demo12345';
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Akun Demo (Read Only)',
                'password' => Hash::make($password),
                'sekolah_id' => $sekolah->id,
                'role' => 'induk', // read-only
                'aktif' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->info('Membuat 20 data siswa acak...');
        $usedNisn = [];

        for ($i = 1; $i <= 20; $i++) {
            $namaLengkap = self::NAMA_DEPAN[array_rand(self::NAMA_DEPAN)] . ' ' . self::NAMA_BELAKANG[array_rand(self::NAMA_BELAKANG)];
            $jk = str_contains($namaLengkap, 'a') && rand(0,1) ? 'P' : (rand(0,1) ? 'L' : 'P');

            do {
                $nisn = (string) rand(1000000000, 9999999999);
            } while (in_array($nisn, $usedNisn));
            $usedNisn[] = $nisn;

            $kelas = [7, 8, 9][array_rand([7, 8, 9])];
            $rombel = ['A', 'B', 'C'][array_rand(['A', 'B', 'C'])];

            $siswa = Siswa::create([
                'sekolah_id' => $sekolah->id,
                'nisn' => $nisn,
                'nis' => (string) (10000 + $i),
                'nama_lengkap' => $namaLengkap,
                'jenis_kelamin' => $jk,
                'tempat_lahir' => 'Malang',
                'tanggal_lahir' => now()->subYears(12 + $kelas - 7)->subDays(rand(0, 365))->format('Y-m-d'),
                'agama' => 'Islam',
                'alamat' => 'Jalan Demo No. ' . rand(1, 100),
                'kecamatan' => 'Kecamatan Contoh',
                'kelas' => (string) $kelas,
                'rombel' => $rombel,
                'tahun_masuk' => (int) date('Y') - ($kelas - 7),
                'status' => 'aktif',
                'nama_ayah' => self::NAMA_DEPAN[array_rand(self::NAMA_DEPAN)] . ' ' . self::NAMA_BELAKANG[array_rand(self::NAMA_BELAKANG)],
                'pekerjaan_ayah' => ['Wiraswasta','PNS','Petani','Karyawan Swasta'][array_rand(['Wiraswasta','PNS','Petani','Karyawan Swasta'])],
                'nama_ibu' => self::NAMA_DEPAN[array_rand(self::NAMA_DEPAN)] . ' ' . self::NAMA_BELAKANG[array_rand(self::NAMA_BELAKANG)],
                'pekerjaan_ibu' => ['Ibu Rumah Tangga','Wiraswasta','PNS','Karyawan Swasta'][array_rand(['Ibu Rumah Tangga','Wiraswasta','PNS','Karyawan Swasta'])],
            ]);

            $this->buatBerkasAcak($siswa);
        }

        $this->newLine();
        $this->info('Selesai! Akun demo (read-only):');
        $this->line("  Email    : {$email}");
        $this->line("  Password : {$password}");
        $this->line('  Peran    : Petugas Induk (Read Only)');
        $this->newLine();
        $this->line('20 siswa demo dengan foto & berkas acak (KK, Akta, Ijazah SD - semua diberi watermark "CONTOH/DEMO") sudah dibuat.');

        return self::SUCCESS;
    }

    private function buatBerkasAcak(Siswa $siswa): void
    {
        $dir = "arsip/{$siswa->id}";

        // Foto placeholder - kotak warna solid + inisial nama, dibuat pakai GD
        $fotoPath = $this->buatFotoPlaceholder($siswa, $dir);
        $siswa->update(['foto' => $fotoPath]);

        $arsip = ArsipBerkas::firstOrCreate(['siswa_id' => $siswa->id]);

        foreach ([
            'kartu_keluarga' => 'Kartu Keluarga',
            'akta_lahir'     => 'Akta Kelahiran',
            'ijazah_sd'      => 'Ijazah SD/MI',
        ] as $field => $label) {
            $pdf = Pdf::loadView('siswa.pdf-dummy-demo', [
                'label' => $label,
                'siswa' => $siswa,
            ])->setPaper('a4', 'portrait');

            $filename = "{$dir}/" . uniqid($field . '_') . '.pdf';
            Storage::disk('public')->put($filename, $pdf->output());
            $arsip->{$field} = $filename;
        }
        $arsip->foto = $fotoPath;
        $arsip->save();
    }

    private function buatFotoPlaceholder(Siswa $siswa, string $dir): string
    {
        $colors = [[37,99,235],[220,38,38],[22,163,74],[217,119,6],[124,58,237]];
        [$r, $g, $b] = $colors[array_rand($colors)];

        $img = imagecreatetruecolor(300, 400);
        imagefill($img, 0, 0, imagecolorallocate($img, $r, $g, $b));

        $initials = collect(explode(' ', $siswa->nama_lengkap))->map(fn ($w) => strtoupper($w[0]))->take(2)->implode('');
        $white = imagecolorallocate($img, 255, 255, 255);
        imagestring($img, 5, 120, 180, $initials, $white);
        imagestring($img, 2, 90, 210, '(FOTO DEMO)', $white);

        $tmpPath = tempnam(sys_get_temp_dir(), 'foto') . '.jpg';
        imagejpeg($img, $tmpPath, 85);
        imagedestroy($img);

        $filename = "{$dir}/" . uniqid('foto_') . '.jpg';
        Storage::disk('public')->put($filename, file_get_contents($tmpPath));
        unlink($tmpPath);

        return $filename;
    }
}
