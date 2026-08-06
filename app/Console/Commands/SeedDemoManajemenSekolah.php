<?php

namespace App\Console\Commands;

use App\Models\AbsensiHarian;
use App\Models\ArsipBerkas;
use App\Models\Sekolah;
use App\Models\Siswa;
use Illuminate\Console\Command;

class SeedDemoManajemenSekolah extends Command
{
    protected $signature = 'demo:seed-manajemen-sekolah';
    protected $description = 'Tambahan data demo (TIDAK RESET): nama Kepala Sekolah, absensi harian 5-10 hari, dan kelengkapan berkas siswa';

    public function handle(): int
    {
        $sekolah = Sekolah::where('is_demo', true)->first();
        if (! $sekolah) {
            $this->error('Sekolah demo belum ada. Jalankan `php artisan demo:setup` dulu.');
            return self::FAILURE;
        }

        // 1. Nama Kepala Sekolah demo
        $sekolah->update([
            'kepala_sekolah_nama' => 'Hiruzen Sarutobi, S.Nin',
            'kepala_sekolah_nip' => (string) rand(196000000000, 196999999999),
        ]);
        $this->info("✓ Kepala Sekolah demo: Hiruzen Sarutobi, S.Nin (NIP {$sekolah->kepala_sekolah_nip})");

        // 2. Absensi Harian 5-10 hari terakhir (additive - skip kalau sudah ada di tanggal itu)
        $siswaList = Siswa::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)->where('status', 'aktif')->get();
        if ($siswaList->isEmpty()) {
            $this->warn('Belum ada siswa demo, absensi & berkas dilewati.');
            return self::SUCCESS;
        }

        $jumlahHari = rand(5, 10);
        $statusList = ['Hadir', 'Hadir', 'Hadir', 'Hadir', 'Hadir', 'Sakit', 'Izin', 'Alpha', 'Dispensasi'];
        $dibuat = 0;

        for ($h = 0; $h < $jumlahHari; $h++) {
            $tanggal = now()->subDays($h)->toDateString();
            foreach ($siswaList as $siswa) {
                $sudahAda = AbsensiHarian::where('siswa_id', $siswa->id)->where('tanggal', $tanggal)->exists();
                if ($sudahAda) continue;

                AbsensiHarian::create([
                    'sekolah_id' => $sekolah->id,
                    'siswa_id' => $siswa->id,
                    'tanggal' => $tanggal,
                    'status' => $statusList[array_rand($statusList)],
                    'keterangan' => null,
                ]);
                $dibuat++;
            }
        }
        $this->info("✓ Absensi Harian: {$dibuat} entri baru untuk {$jumlahHari} hari terakhir ({$siswaList->count()} siswa)");

        // 3. Kelengkapan Berkas - isi placeholder biar statistik kelengkapan tidak 0%
        // (foto dikecualikan - biarkan fallback ke avatar inisial spy gak jadi gambar rusak)
        $fieldBerkas = ['kartu_keluarga', 'akta_lahir', 'ijazah_sd', 'ijazah', 'transkrip_nilai', 'sertifikat_tka'];
        $berkasDibuat = 0;
        foreach ($siswaList as $siswa) {
            $arsip = ArsipBerkas::firstOrNew(['siswa_id' => $siswa->id]);
            foreach ($fieldBerkas as $f) {
                // Random sebagian kosong sebagian terisi - biar keliatan realistis (gak 100% semua)
                if (empty($arsip->{$f}) && rand(0, 100) < 75) {
                    $arsip->{$f} = 'demo-placeholder/' . $f . '-' . $siswa->id . '.jpg';
                }
            }
            $arsip->save();
            $berkasDibuat++;
        }
        $this->info("✓ Kelengkapan Berkas: diisi placeholder utk {$berkasDibuat} siswa (sebagian acak, biar realistis)");

        $this->newLine();
        $this->info('Selesai! Semua tambahan bersifat additive - data lain tidak berubah/terhapus.');
        return self::SUCCESS;
    }
}
