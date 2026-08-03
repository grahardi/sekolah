<?php

namespace App\Console\Commands;

use App\Models\GuruPengajar;
use App\Models\MataPelajaran;
use App\Models\Penilaian;
use App\Models\PenilaianDetailNilai;
use App\Models\Rapor;
use App\Models\RaporDetailAkademik;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TpKelas;
use App\Models\TujuanPembelajaran;
use App\Models\WaliKelas;
use App\Services\MataPelajaranTemplate;
use App\Services\RaporCalculator;
use Illuminate\Console\Command;

class SeedDemoErapor extends Command
{
    protected $signature = 'demo:seed-erapor';
    protected $description = 'Generate data E-Rapor lengkap utk sekolah demo: guru, TP, penilaian, nilai UTS/akhir, catatan, absensi - 1 kelas final, 1 kelas draft';

    private const CATATAN_CONTOH = [
        'Secara keseluruhan menunjukkan perkembangan yang positif pada semester ini. Terus pertahankan semangat belajarnya.',
        'Menunjukkan sikap yang baik dan aktif dalam kegiatan belajar. Perlu ditingkatkan lagi kedisiplinan waktu mengumpulkan tugas.',
        'Sangat aktif bertanya dan berdiskusi di kelas. Pertahankan rasa ingin tahunya.',
        'Perlu bimbingan lebih dalam manajemen waktu belajar di rumah agar hasil belajar lebih maksimal.',
    ];

    public function handle(): int
    {
        $sekolah = Sekolah::where('is_demo', true)->first();
        if (! $sekolah) {
            $this->error('Sekolah demo belum ada. Jalankan `php artisan demo:setup` dulu.');
            return self::FAILURE;
        }

        $this->info('Menyiapkan Tahun Ajaran & Mata Pelajaran...');
        $tahunAjaran = TahunAjaran::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)->where('is_aktif', true)->first();
        if (! $tahunAjaran) {
            TahunAjaran::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)->update(['is_aktif' => false]);
            $tahunAjaran = TahunAjaran::create([
                'sekolah_id' => $sekolah->id, 'nama' => (date('Y')) . '/' . (date('Y') + 1), 'semester' => 'Ganjil', 'is_aktif' => true,
            ]);
        }
        $semester = $tahunAjaran->semester === 'Genap' ? 2 : 1;

        if (Penilaian::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)->where('tahun_ajaran_id', $tahunAjaran->id)->exists()) {
            if (! $this->confirm('Data penilaian demo utk tahun ajaran ini sudah ada. Lanjutkan akan menambah data BARU (bisa dobel). Lanjut?', false)) {
                $this->warn('Dibatalkan.');
                return self::SUCCESS;
            }
        }

        MataPelajaranTemplate::seedFor($sekolah);
        $mapelList = MataPelajaran::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)->get();

        $kombinasiKelas = Siswa::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)->where('status', 'aktif')
            ->selectRaw('kelas, rombel, count(*) as jumlah')->groupBy('kelas', 'rombel')->orderByDesc('jumlah')->limit(2)->get();

        if ($kombinasiKelas->count() < 1) {
            $this->error('Tidak ada data siswa demo. Jalankan `php artisan demo:setup` dulu.');
            return self::FAILURE;
        }

        $this->info('Membuat data Kepegawaian & Guru demo...');
        $namaGuru = ['Siti Nurhaliza, S.Pd.', 'Budi Santoso, S.Pd.', 'Rina Wulandari, S.Pd.', 'Agus Prasetyo, S.Pd.', 'Dewi Kartika, S.Pd.'];
        $guruList = collect();
        foreach ($namaGuru as $i => $nama) {
            $pegawai = \App\Models\Pegawai::firstOrCreate(
                ['sekolah_id' => $sekolah->id, 'nama_lengkap' => $nama],
                [
                    'nip_nuptk' => (string) (198000000000 + $i * 1111 + rand(100, 999)),
                    'jenis_kelamin' => rand(0, 1) ? 'L' : 'P',
                    'tempat_lahir' => 'Malang',
                    'tanggal_lahir' => now()->subYears(rand(28, 50))->format('Y-m-d'),
                    'jenis_kepegawaian' => ['PNS', 'PPPK', 'GTT'][array_rand(['PNS', 'PPPK', 'GTT'])],
                    'jabatan' => 'Guru Mata Pelajaran',
                    'status_aktif' => 'Aktif',
                    'tanggal_masuk' => now()->subYears(rand(2, 15))->format('Y-m-d'),
                ]
            );
            \App\Models\Guru::syncFromPegawai($sekolah->id);
            $guruList->push(\App\Models\Guru::where('sekolah_id', $sekolah->id)->where('pegawai_id', $pegawai->id)->first());
        }

        // 1 contoh guru bantu (tidak terdaftar di Kepegawaian) - biar fitur itu ikut ke-demo-in
        $guruList->push(\App\Models\Guru::firstOrCreate(
            ['sekolah_id' => $sekolah->id, 'nama' => 'Farhan Maulana (Guru Bantu)'],
            ['keterangan' => 'Guru Bantu']
        ));

        foreach ($kombinasiKelas as $idx => $kk) {
            $isFinal = $idx === 0;
            $this->line("--- Kelas {$kk->kelas}-{$kk->rombel} (" . ($isFinal ? 'akan Final' : 'akan Draft') . ") ---");

            $waliKelasRecord = WaliKelas::firstOrCreate(
                ['sekolah_id' => $sekolah->id, 'tahun_ajaran_id' => $tahunAjaran->id, 'kelas' => $kk->kelas, 'rombel' => $kk->rombel],
                ['guru_id' => $guruList->random()->id]
            );

            // Hubungkan akun demo (role induk) ke Guru yg jadi Wali Kelas kelas
            // PERTAMA (yg bakal Final) - biar demo bisa akses menu Wali Kelas juga.
            if ($isFinal) {
                $guruWaliDemo = \App\Models\Guru::find($waliKelasRecord->guru_id);
                $userDemo = \App\Models\User::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)->where('role', 'induk')->first();
                if ($guruWaliDemo && $userDemo) {
                    $guruWaliDemo->update(['user_id' => $userDemo->id]);
                    $this->info("Akun demo dihubungkan sbg Wali Kelas: {$guruWaliDemo->nama} (Kelas {$kk->kelas}-{$kk->rombel})");
                }
            }

            $siswaKelas = Siswa::withoutGlobalScopes()->where('sekolah_id', $sekolah->id)
                ->where('kelas', $kk->kelas)->where('rombel', $kk->rombel)->where('status', 'aktif')->get();

            foreach ($mapelList as $mapel) {
                $guru = $guruList->random();

                GuruPengajar::firstOrCreate([
                    'sekolah_id' => $sekolah->id, 'tahun_ajaran_id' => $tahunAjaran->id, 'guru_id' => $guru->id,
                    'mata_pelajaran_id' => $mapel->id, 'kelas' => $kk->kelas, 'rombel' => $kk->rombel,
                ]);

                $jumlahTp = rand(3, 4);
                $penilaianPerTp = [];
                for ($t = 1; $t <= $jumlahTp; $t++) {
                    $tp = TujuanPembelajaran::create([
                        'sekolah_id' => $sekolah->id, 'mata_pelajaran_id' => $mapel->id, 'guru_id' => $guru->id,
                        'tahun_ajaran_id' => $tahunAjaran->id, 'fase' => 'D', 'kode_tp' => "{$mapel->id}.{$t}",
                        'deskripsi_tp' => "Memahami materi {$mapel->nama} bagian {$t}", 'semester' => $semester,
                    ]);
                    TpKelas::create(['tujuan_pembelajaran_id' => $tp->id, 'kelas' => $kk->kelas, 'rombel' => $kk->rombel]);

                    // 1 Penilaian TERPISAH per TP - biar nilainya bisa beda-beda
                    // per TP (kalau digabung 1 penilaian utk semua TP, nilainya
                    // bakal sama semua & deskripsi min/max jadi gak ada variasi).
                    $penilaianSatuTp = Penilaian::create([
                        'sekolah_id' => $sekolah->id, 'tahun_ajaran_id' => $tahunAjaran->id, 'guru_id' => $guru->id,
                        'mata_pelajaran_id' => $mapel->id, 'kelas' => $kk->kelas, 'rombel' => $kk->rombel,
                        'nama_penilaian' => "Ulangan Harian {$t}", 'jenis_penilaian' => 'Sumatif', 'subjenis_penilaian' => 'Sumatif TP',
                        'bobot_penilaian' => 1, 'semester' => $semester, 'tanggal_penilaian' => now()->subDays(25 - $t * 3),
                    ]);
                    $penilaianSatuTp->tujuanPembelajarans()->sync([$tp->id]);
                    $penilaianPerTp[] = $penilaianSatuTp;
                }

                $penilaianUts = Penilaian::create([
                    'sekolah_id' => $sekolah->id, 'tahun_ajaran_id' => $tahunAjaran->id, 'guru_id' => $guru->id,
                    'mata_pelajaran_id' => $mapel->id, 'kelas' => $kk->kelas, 'rombel' => $kk->rombel,
                    'nama_penilaian' => 'STS ' . ($semester == 1 ? 'Ganjil' : 'Genap'), 'jenis_penilaian' => 'Sumatif', 'subjenis_penilaian' => 'Sumatif Tengah Semester',
                    'bobot_penilaian' => 1, 'semester' => $semester, 'tanggal_penilaian' => now()->subDays(10),
                ]);

                $penilaianUas = Penilaian::create([
                    'sekolah_id' => $sekolah->id, 'tahun_ajaran_id' => $tahunAjaran->id, 'guru_id' => $guru->id,
                    'mata_pelajaran_id' => $mapel->id, 'kelas' => $kk->kelas, 'rombel' => $kk->rombel,
                    'nama_penilaian' => 'SAS ' . ($semester == 1 ? 'Ganjil' : 'Genap'), 'jenis_penilaian' => 'Sumatif', 'subjenis_penilaian' => 'Sumatif Akhir Semester',
                    'bobot_penilaian' => 2, 'semester' => $semester, 'tanggal_penilaian' => now()->subDays(2),
                ]);

                foreach ($siswaKelas as $siswa) {
                    // Rentang nilai lebih lebar (60-100) per TP biar variasi
                    // min/max di deskripsi capaian kompetensi kelihatan jelas.
                    foreach ($penilaianPerTp as $p) {
                        PenilaianDetailNilai::create(['penilaian_id' => $p->id, 'siswa_id' => $siswa->id, 'nilai' => rand(60, 100)]);
                    }
                    PenilaianDetailNilai::create(['penilaian_id' => $penilaianUts->id, 'siswa_id' => $siswa->id, 'nilai' => rand(70, 95)]);
                    PenilaianDetailNilai::create(['penilaian_id' => $penilaianUas->id, 'siswa_id' => $siswa->id, 'nilai' => rand(72, 96)]);
                }
            }

            foreach ($siswaKelas as $siswa) {
                $rapor = Rapor::updateOrCreate(
                    ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $tahunAjaran->id, 'semester' => $semester],
                    [
                        'sekolah_id' => $sekolah->id, 'kelas' => $kk->kelas, 'rombel' => $kk->rombel,
                        'sakit' => rand(0, 3), 'izin' => rand(0, 2), 'tanpa_keterangan' => rand(0, 1),
                        'catatan_wali_kelas' => self::CATATAN_CONTOH[array_rand(self::CATATAN_CONTOH)],
                        'catatan_uts' => 'Hasil UTS menunjukkan pemahaman yang baik terhadap materi semester ini.',
                        'tanggal_rapor' => now(),
                    ]
                );

                foreach ($mapelList as $mapel) {
                    $hasil = RaporCalculator::hitung($siswa->id, $kk->kelas, $kk->rombel, $mapel->id, $tahunAjaran->id, $semester);
                    RaporDetailAkademik::updateOrCreate(
                        ['rapor_id' => $rapor->id, 'mata_pelajaran_id' => $mapel->id],
                        ['nilai_akhir' => $hasil['nilai_akhir'], 'capaian_kompetensi' => $hasil['deskripsi']]
                    );
                }

                if ($isFinal) {
                    $rapor->update(['status' => 'Final']);
                }
            }

            // ── Ekstrakurikuler ──────────────────────────────────────────
            $daftarEkskul = ['Pramuka', 'Futsal', 'Paduan Suara'];
            foreach ($daftarEkskul as $namaEkskul) {
                \App\Models\GuruEkstrakurikuler::firstOrCreate([
                    'sekolah_id' => $sekolah->id, 'tahun_ajaran_id' => $tahunAjaran->id,
                    'guru_id' => $guruList->random()->id, 'nama_ekstrakurikuler' => $namaEkskul,
                ]);
            }
            foreach ($siswaKelas as $siswa) {
                if (rand(0, 100) > 70) continue; // sebagian siswa tidak ikut ekskul, biar realistis
                $rapor = Rapor::where('siswa_id', $siswa->id)->where('tahun_ajaran_id', $tahunAjaran->id)->where('semester', $semester)->first();
                if (! $rapor || $rapor->detailEkskul()->exists()) continue;

                $ikutEkskul = collect($daftarEkskul)->random(rand(1, 2));
                foreach ((is_iterable($ikutEkskul) ? $ikutEkskul : [$ikutEkskul]) as $namaEkskul) {
                    \App\Models\RaporDetailEkskul::create([
                        'rapor_id' => $rapor->id, 'nama_ekskul' => $namaEkskul,
                        'kehadiran_hadir' => rand(6, 10), 'kehadiran_total' => 10,
                        'evaluasi' => ['Sangat Baik', 'Baik', 'Cukup'][array_rand(['Sangat Baik', 'Baik', 'Cukup'])],
                        'keterangan' => 'Mengikuti kegiatan dengan aktif dan disiplin.',
                    ]);
                }
            }

            // ── Kokurikuler (P5) ─────────────────────────────────────────
            $kegiatanP5 = \App\Models\KokurikulerKegiatan::create([
                'sekolah_id' => $sekolah->id, 'tahun_ajaran_id' => $tahunAjaran->id,
                'nama_kegiatan' => '7 Kebiasaan Anak Indonesia Hebat', 'tema' => 'Gaya Hidup Berkelanjutan',
                'bentuk_kegiatan' => 'Lintas Disiplin', 'koordinator_guru_id' => $guruList->random()->id, 'semester' => $semester,
            ]);
            foreach (array_slice(\App\Models\KokurikulerKegiatan::daftarDimensi(), 0, 3) as $dim) {
                \App\Models\KokurikulerTargetDimensi::create(['kegiatan_id' => $kegiatanP5->id, 'nama_dimensi' => $dim]);
            }
            \App\Models\KokurikulerKelasTerlibat::create(['kegiatan_id' => $kegiatanP5->id, 'kelas' => $kk->kelas, 'rombel' => $kk->rombel]);
            \App\Models\KokurikulerMapelTerlibat::create(['kegiatan_id' => $kegiatanP5->id, 'mata_pelajaran_id' => $mapelList->first()->id]);

            $dimensiList = $kegiatanP5->targetDimensis;
            foreach ($siswaKelas as $siswa) {
                foreach ($dimensiList as $dim) {
                    \App\Models\KokurikulerAsesmen::create([
                        'target_dimensi_id' => $dim->id, 'siswa_id' => $siswa->id,
                        'nilai_kualitatif' => ['Sangat Baik', 'Baik', 'Baik', 'Cukup'][array_rand(['Sangat Baik', 'Baik', 'Baik', 'Cukup'])],
                        'catatan_guru' => 'Menunjukkan perkembangan yang baik selama projek berlangsung.',
                    ]);
                }
                $deskripsiP5 = \App\Http\Controllers\KokurikulerController::generateDeskripsi($siswa->id, $kegiatanP5->id);
                Rapor::where('siswa_id', $siswa->id)->where('tahun_ajaran_id', $tahunAjaran->id)->where('semester', $semester)
                    ->update(['deskripsi_kokurikuler' => $deskripsiP5]);
            }

            $this->info("Kelas {$kk->kelas}-{$kk->rombel} selesai (" . $siswaKelas->count() . ' siswa, status: ' . ($isFinal ? 'Final' : 'Draft') . ')');
        }

        $this->newLine();
        $this->info('Selesai! Data demo E-Rapor siap dipakai showcase.');
        return self::SUCCESS;
    }
}
