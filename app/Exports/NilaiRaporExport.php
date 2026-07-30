<?php

namespace App\Exports;

use App\Models\{Siswa, NilaiRapor};
use Rap2hpoutre\FastExcel\FastExcel;

class NilaiRaporExport
{
    public function downloadTemplate(
        string $kelas,
        string $tahunAjaran,
        int    $semester
    ): \Symfony\Component\HttpFoundation\StreamedResponse {

        $siswas = Siswa::where('kelas', $kelas)
                       ->where('status', 'aktif')
                       ->orderBy('nama_lengkap')
                       ->get();

        $mapels = NilaiRapor::daftarMapel();

        $existing = [];
        if ($siswas->isNotEmpty()) {
            NilaiRapor::whereIn('siswa_id', $siswas->pluck('id'))
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('semester', $semester)
                ->get()
                ->each(fn($n) => $existing[$n->siswa_id][$n->mata_pelajaran] = $n->nilai);
        }

        $rows = $siswas->map(function ($s) use ($mapels, $existing) {
            $row = [
                'nisn'         => $s->nisn,
                'nis'          => $s->nis,
                'nama_lengkap' => $s->nama_lengkap,
                'kelas'        => $s->kelas,
                'rombel'       => $s->rombel,
            ];
            foreach ($mapels as $m) {
                $row[$m] = $existing[$s->id][$m] ?? '';
            }
            return $row;
        });

        if ($rows->isEmpty()) {
            $row = ['nisn'=>'','nis'=>'','nama_lengkap'=>'Contoh Nama','kelas'=>$kelas,'rombel'=>''];
            foreach ($mapels as $m) $row[$m] = '';
            $rows = collect([$row]);
        }

        $filename = 'nilai-rapor-' . str_replace('/','-',$tahunAjaran)
                  . '-sem' . $semester . '-kelas-' . str_replace(' ','',$kelas) . '.xlsx';

        return (new FastExcel($rows))->download($filename);
    }
}
