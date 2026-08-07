<?php

namespace App\Services;

use App\Models\ExoInstance;

class ExoSyncService
{
    public static function sinkronSiswa(ExoInstance $exoInstance, string $identifier = 'nisn'): array
    {
        if (! $exoInstance->sekolah_id) {
            return ['ok' => false, 'pesan' => 'Instance belum terhubung ke sekolah manapun.'];
        }
        if (! $exoInstance->db_host) {
            return ['ok' => false, 'pesan' => 'Kredensial database instance belum diisi.'];
        }

        $siswaList = \App\Models\Siswa::withoutGlobalScopes()
            ->where('sekolah_id', $exoInstance->sekolah_id)
            ->where('status', 'aktif')
            ->whereNotNull('kelas')
            ->get();

        if ($siswaList->isEmpty()) {
            return ['ok' => false, 'pesan' => 'Tidak ada siswa aktif di sekolah ini.'];
        }

        try {
            $conn = $exoInstance->dbConnection();

            // 1. Jurusan "Umum" - wajib diisi di pesertas, SMP gak punya jurusan
            //    beneran jadi pakai 1 jurusan generik ini utk semua siswa.
            $jurusanUmum = $conn->table('jurusans')->where('nama', 'Umum')->first();
            if (! $jurusanUmum) {
                $jurusanId = (string) \Illuminate\Support\Str::uuid();
                $conn->table('jurusans')->insert([
                    'id' => $jurusanId, 'kode' => 'UMUM', 'nama' => 'Umum',
                ]);
            } else {
                $jurusanId = $jurusanUmum->id;
            }

            // 2. Cache daftar agama yg sudah ada di exo, biar gak query berulang
            $agamaMap = $conn->table('agamas')->get()->keyBy(fn ($a) => strtoupper(trim($a->nama)));

            $dibuatGrup = 0; $dibuatSiswa = 0; $diupdateSiswa = 0; $dilewati = 0;

            foreach ($siswaList->groupBy('kelas') as $kelas => $siswaSatuAngkatan) {
                $namaGrupInduk = "Kelas {$kelas}";
                $grupInduk = $conn->table('groups')->where('name', $namaGrupInduk)->whereNull('parent_id')->first();
                if (! $grupInduk) {
                    $grupIndukId = (string) \Illuminate\Support\Str::uuid();
                    $conn->table('groups')->insert([
                        'id' => $grupIndukId, 'name' => $namaGrupInduk, 'parent_id' => null,
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                    $dibuatGrup++;
                } else {
                    $grupIndukId = $grupInduk->id;
                }

                foreach ($siswaSatuAngkatan->groupBy('rombel') as $rombel => $siswaSatuKelas) {
                    $namaSubGrup = $rombel ? "{$kelas} - {$rombel}" : "{$kelas}";
                    $subGrup = $conn->table('groups')->where('name', $namaSubGrup)->where('parent_id', $grupIndukId)->first();
                    if (! $subGrup) {
                        $subGrupId = (string) \Illuminate\Support\Str::uuid();
                        $conn->table('groups')->insert([
                            'id' => $subGrupId, 'name' => $namaSubGrup, 'parent_id' => $grupIndukId,
                            'created_at' => now(), 'updated_at' => now(),
                        ]);
                        $dibuatGrup++;
                    } else {
                        $subGrupId = $subGrup->id;
                    }

                    foreach ($siswaSatuKelas as $siswa) {
                        $noUjian = $identifier === 'nis' ? ($siswa->nis ?: $siswa->nisn) : ($siswa->nisn ?: $siswa->nis);
                        if (! $noUjian) { $dilewati++; continue; }

                        $namaAgama = strtoupper(trim($siswa->agama ?? 'ISLAM'));
                        $agamaRow = $agamaMap->get($namaAgama);
                        if (! $agamaRow) {
                            $agamaId = (string) \Illuminate\Support\Str::uuid();
                            $conn->table('agamas')->insert([
                                'id' => $agamaId, 'kode' => \Illuminate\Support\Str::slug($namaAgama, '_'),
                                'nama' => ucwords(strtolower($siswa->agama ?? 'Islam')),
                                'created_at' => now(), 'updated_at' => now(),
                            ]);
                            $agamaMap->put($namaAgama, (object) ['id' => $agamaId]);
                        } else {
                            $agamaId = $agamaRow->id;
                        }

                        $pesertaAda = $conn->table('pesertas')->where('no_ujian', $noUjian)->first();
                        // Password TEKS BIASA (bukan di-hash) - ini kode ujian
                        // sekali pakai/gampang diganti tiap sesi, bukan
                        // kredensial permanen kayak akun admin.
                        $passwordBaru = (string) random_int(100000, 999999);

                        if (! $pesertaAda) {
                            $pesertaId = (string) \Illuminate\Support\Str::uuid();
                            $conn->table('pesertas')->insert([
                                'id' => $pesertaId,
                                'sesi' => 1, // "Sesi 1" default
                                'no_ujian' => $noUjian,
                                'nama' => $siswa->nama_lengkap,
                                'password' => $passwordBaru,
                                'jurusan_id' => $jurusanId,
                                'agama_id' => $agamaId,
                                'status' => 1, // 1 = aktif
                                'created_at' => now(), 'updated_at' => now(),
                            ]);
                            $dibuatSiswa++;
                        } else {
                            $pesertaId = $pesertaAda->id;
                            $conn->table('pesertas')->where('id', $pesertaId)->update([
                                'nama' => $siswa->nama_lengkap,
                                'password' => $passwordBaru,
                                'jurusan_id' => $jurusanId,
                                'agama_id' => $agamaId,
                                'updated_at' => now(),
                            ]);
                            $diupdateSiswa++;
                        }

                        $conn->table('group_members')->where('student_id', $pesertaId)->delete();
                        $conn->table('group_members')->insert([
                            'id' => (string) \Illuminate\Support\Str::uuid(),
                            'group_id' => $subGrupId,
                            'student_id' => $pesertaId,
                            'created_at' => now(), 'updated_at' => now(),
                        ]);
                    }
                }
            }

            return [
                'ok' => true,
                'pesan' => "Sinkron selesai: {$dibuatGrup} grup/subgrup dibuat, {$dibuatSiswa} siswa baru, {$diupdateSiswa} siswa diperbarui" . ($dilewati ? ", {$dilewati} dilewati (NIS/NISN kosong)" : '') . '.',
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'pesan' => 'Gagal sinkron: ' . $e->getMessage()];
        }
    }
}
