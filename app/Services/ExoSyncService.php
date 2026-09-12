<?php

namespace App\Services;

use App\Models\ExoInstance;

class ExoSyncService
{
    // ID tetap yg sudah ada di database Extraordinary (hasil provisioning
    // dari SQL master kita) - dipakai langsung, gak perlu insert baru lagi.
    private const JURUSAN_UMUM_ID = '3e41ce1d-af1b-4d2c-80e1-46f6dd261403';

    private const AGAMA_ID_MAP = [
        'ISLAM' => '3aed771a-9458-4cce-9811-8b0b50bfe462',
        'KRISTEN' => '6e4c117b-b057-44a3-98ab-d54d197030de',
        'PROTESTAN' => '6e4c117b-b057-44a3-98ab-d54d197030de',
        'KRISTEN PROTESTAN' => '6e4c117b-b057-44a3-98ab-d54d197030de',
        'KATOLIK' => 'dae66fe2-5785-4b44-892b-6a40c1c2e1f1',
        'BUDHA' => 'b835ff17-369c-4250-a565-000a06953adf',
        'BUDDHA' => 'b835ff17-369c-4250-a565-000a06953adf',
        'HINDU' => '8194f3f2-501b-420f-a496-85fded97beb0',
        'KONGHUCU' => '7c03497a-6df3-46db-9ff9-99a9c7b49b14',
        'KONG HU CU' => '7c03497a-6df3-46db-9ff9-99a9c7b49b14',
    ];

    public static function sinkronSiswa(ExoInstance $exoInstance, string $identifier = 'nisn', string $mode = 'update', bool $sertakanFoto = false): array
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
            ->when($sertakanFoto, fn ($q) => $q->with('arsipBerkas'))
            ->get();

        if ($siswaList->isEmpty()) {
            return ['ok' => false, 'pesan' => 'Tidak ada siswa aktif di sekolah ini.'];
        }

        try {
            $conn = $exoInstance->dbConnection();

            if ($mode === 'reset') {
                // Hapus dulu SEMUA data lama (bukan pakai TRUNCATE CASCADE - itu
                // bisa nyeret tabel lain yg ikut ber-FK ke pesertas/groups, mis.
                // hasil_ujians. DELETE biasa lebih aman & jelas errornya kalau
                // ternyata masih ada data yg nyangkut/gak bisa dihapus.
                $conn->table('group_members')->delete();
                $conn->table('pesertas')->delete();
                $conn->table('groups')->delete();
            }

            // Jurusan "Umum" - pakai ID tetap yg sudah ada dari hasil provisioning
            $jurusanId = self::JURUSAN_UMUM_ID;

            // Siapkan folder foto kalau opsi foto diaktifkan - DUA folder tujuan
            // dgn SUMBER PATH BEDA:
            // 1. 'exo-output-photo' - dari STORAGE_PATH di .env instance, yg
            //    TERKONFIRMASI diarahkan ke public/storage Laravel kita sendiri
            //    (dipakai wormhole/web monitoring, sengaja begitu biar web-servable)
            // 2. 'exo-photo-student' - dari $instance->path LANGSUNG (folder fisik
            //    instance itu sendiri, mis. /home/aginza/sekolah/instance1),
            //    BUKAN dari STORAGE_PATH - dipakai internal Extraordinary
            //    (mis. verifikasi wajah), beda lokasi fisik dgn yg di atas.
            $folderFoto = null;
            $folderFotoStudent = null;
            if ($sertakanFoto) {
                $storagePath = rtrim($exoInstance->bacaEnv('STORAGE_PATH') ?: '', '/');
                if ($storagePath) {
                    $folderFoto = $storagePath . '/exo-output-photo';
                    if (! is_dir($folderFoto)) @mkdir($folderFoto, 0775, true);
                }

                $instancePath = rtrim($exoInstance->path ?: '', '/');
                if ($instancePath) {
                    $folderFotoStudent = $instancePath . '/storage/exo-photo-student';
                    if (! is_dir($folderFotoStudent)) @mkdir($folderFotoStudent, 0775, true);
                }
            }

            $dibuatGrup = 0; $dibuatSiswa = 0; $diupdateSiswa = 0; $dilewati = 0; $fotoTersinkron = 0; $fotoSudahAda = 0;

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

                        // Cocokkan agama siswa ke ID tetap yg sudah diketahui.
                        // Kalau gak cocok (agama tidak umum/typo), fallback ke Islam
                        // drpd gagal total - gak sempurna tapi lebih aman drpd stop.
                        $namaAgama = strtoupper(trim($siswa->agama ?? 'ISLAM'));
                        $agamaId = self::AGAMA_ID_MAP[$namaAgama] ?? self::AGAMA_ID_MAP['ISLAM'];

                        $pesertaAda = $conn->table('pesertas')->where('no_ujian', $noUjian)->first();
                        // Password TEKS BIASA (bukan di-hash) - ini kode ujian
                        // sekali pakai/gampang diganti tiap sesi, bukan
                        // kredensial permanen kayak akun admin.
                        $passwordBaru = (string) random_int(100000, 999999);

                        // Foto - cuma proses kalau opsi diaktifkan DAN siswa
                        // beneran punya foto asli (bukan avatar inisial default).
                        // Fallback ke foto di Berkas kalau foto profil kosong,
                        // sama kayak logic foto_url.
                        //
                        // SENGAJA pakai copy() (bukan symlink) dan SENGAJA
                        // tidak menimpa file yg SUDAH ADA di sisi Extraordinary
                        // - kalau proctor upload foto manual lewat Extraordinary
                        // utk siswa itu, sinkron berikutnya gak akan menghapus/
                        // menimpanya. Symlink juga dihindari krn kalau nanti ada
                        // yg upload manual, tulisannya bisa "menembus" ke file
                        // ASLI kita di Buku Induk (bukan cuma sisi Extraordinary).
                        $avaValue = $pesertaAda->ava ?? null;
                        $fotoPath = $siswa->foto ?: ($siswa->arsipBerkas->foto ?? null);
                        if ($sertakanFoto && $folderFoto && $fotoPath) {
                            $tujuanPath = "{$folderFoto}/{$noUjian}.jpg";
                            $tujuanPathStudent = $folderFotoStudent ? "{$folderFotoStudent}/{$noUjian}.jpg" : null;

                            if (! file_exists($tujuanPath)) {
                                $sumberPath = storage_path('app/public/' . $fotoPath);
                                if (file_exists($sumberPath) && @copy($sumberPath, $tujuanPath)) {
                                    $avaValue = "exo-output-photo/{$noUjian}.jpg";
                                    $fotoTersinkron++;
                                }
                            } elseif (! $avaValue) {
                                // File udah ada di folder tapi kolom ava blm keisi (mis. dari luar sistem kita) - kaitkan aja
                                $avaValue = "exo-output-photo/{$noUjian}.jpg";
                                $fotoSudahAda++;
                            } else {
                                $fotoSudahAda++;
                            }

                            // Copy jg ke folder kedua (exo-photo-student) - SAMA
                            // aturan "jangan timpa file yg sudah ada" spy gak
                            // ngerusak foto yg mgkin udah diupload manual di sisi
                            // Extraordinary sblmnya.
                            if ($tujuanPathStudent && ! file_exists($tujuanPathStudent)) {
                                $sumberPath = storage_path('app/public/' . $fotoPath);
                                if (file_exists($sumberPath)) {
                                    @copy($sumberPath, $tujuanPathStudent);
                                }
                            }
                        }

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
                                'ava' => $avaValue,
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
                                'ava' => $avaValue,
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

            $awalan = $mode === 'reset' ? 'Reset & sinkron selesai' : 'Sinkron selesai';
            $pesanFoto = $sertakanFoto ? ", {$fotoTersinkron} foto baru dicopy" . ($fotoSudahAda ? " ({$fotoSudahAda} sudah ada sebelumnya)" : '') : '';
            return [
                'ok' => true,
                'pesan' => "{$awalan}: {$dibuatGrup} grup/subgrup dibuat, {$dibuatSiswa} siswa baru, {$diupdateSiswa} siswa diperbarui{$pesanFoto}" . ($dilewati ? ", {$dilewati} dilewati (NIS/NISN kosong)" : '') . '.',
            ];
        } catch (\Throwable $e) {
            return ['ok' => false, 'pesan' => 'Gagal sinkron: ' . $e->getMessage()];
        }
    }
}
