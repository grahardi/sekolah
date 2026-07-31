<?php

namespace App\Exports;

use App\Models\Pegawai;
use Rap2hpoutre\FastExcel\FastExcel;

class PegawaiExport
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function download(string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $pegawais = Pegawai::filter($this->filters)->orderBy('nama_lengkap')->get();
        return (new FastExcel($this->transform($pegawais)))->download($filename);
    }

    public function downloadTemplate(string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return (new FastExcel(collect([$this->templateRow()])))->download($filename);
    }

    private function transform($pegawais): \Illuminate\Support\Collection
    {
        return $pegawais->map(fn ($p) => $this->toRow($p));
    }

    private function toRow($p): array
    {
        return [
            'nip_nuptk' => $p->nip_nuptk,
            'nama_lengkap' => $p->nama_lengkap,
            'jenis_kelamin' => $p->jenis_kelamin,
            'tempat_lahir' => $p->tempat_lahir,
            'tanggal_lahir' => $p->tanggal_lahir?->format('d/m/Y'),
            'jenis_kepegawaian' => $p->jenis_kepegawaian,
            'jabatan' => $p->jabatan,
            'unit_kerja' => $p->unit_kerja,
            'golongan' => $p->golongan,
            'pangkat' => $p->pangkat,
            'tmt_cpns' => $p->tmt_cpns?->format('d/m/Y'),
            'tmt_pns' => $p->tmt_pns?->format('d/m/Y'),
            'no_sk_pangkat' => $p->no_sk_pangkat,
            'tmt_pangkat_terakhir' => $p->tmt_pangkat_terakhir?->format('d/m/Y'),
            'tmt_gaji_berkala_terakhir' => $p->tmt_gaji_berkala_terakhir?->format('d/m/Y'),
            'pendidikan_terakhir' => $p->pendidikan_terakhir,
            'no_hp' => $p->no_hp,
            'email' => $p->email,
            'alamat' => $p->alamat,
            'status_aktif' => $p->status_aktif,
            'tanggal_masuk' => $p->tanggal_masuk?->format('d/m/Y'),
        ];
    }

    private function templateRow(): array
    {
        return [
            'nip_nuptk' => '198001012005011001',
            'nama_lengkap' => 'Contoh Nama Pegawai',
            'jenis_kelamin' => 'L',
            'tempat_lahir' => 'Malang',
            'tanggal_lahir' => '01/01/1980',
            'jenis_kepegawaian' => 'PNS',
            'jabatan' => 'Guru Mapel Matematika',
            'unit_kerja' => 'Guru Mapel',
            'golongan' => 'III/a',
            'pangkat' => 'Penata Muda',
            'tmt_cpns' => '01/01/2005',
            'tmt_pns' => '01/01/2006',
            'no_sk_pangkat' => '',
            'tmt_pangkat_terakhir' => '01/04/2022',
            'tmt_gaji_berkala_terakhir' => '01/01/2024',
            'pendidikan_terakhir' => 'S1 Pendidikan Matematika',
            'no_hp' => '08123456789',
            'email' => 'pegawai@email.com',
            'alamat' => 'Jl. Contoh No. 1',
            'status_aktif' => 'Aktif',
            'tanggal_masuk' => '01/01/2005',
        ];
    }
}
