<?php

namespace App\Exports;

use App\Models\Siswa;
use Rap2hpoutre\FastExcel\FastExcel;

class SiswaExport
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function download(string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $siswas = Siswa::filter($this->filters)->orderBy('nama_lengkap')->get();
        return (new FastExcel($this->transform($siswas)))->download($filename);
    }

    public function downloadTemplate(string $filename): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return (new FastExcel(collect([$this->templateRow()])))->download($filename);
    }

    private function transform($siswas): \Illuminate\Support\Collection
    {
        return $siswas->map(fn($s) => $this->toRow($s));
    }

    private function toRow($s): array
    {
        return [
            'nisn'             => $s->nisn,
            'nis'              => $s->nis,
            'nama_lengkap'     => $s->nama_lengkap,
            'jenis_kelamin'    => $s->jenis_kelamin,
            'tempat_lahir'     => $s->tempat_lahir,
            'tanggal_lahir'    => $s->tanggal_lahir?->format('d/m/Y'),
            'agama'            => $s->agama,
            'alamat'           => $s->alamat,
            'rt'               => $s->rt,
            'rw'               => $s->rw,
            'dusun'            => $s->dusun,
            'kelurahan'        => $s->kelurahan,
            'kecamatan'        => $s->kecamatan,
            'kode_pos'         => $s->kode_pos,
            'lintang'          => $s->lintang,
            'bujur'            => $s->bujur,
            'no_telepon'       => $s->no_telepon,
            'email'            => $s->email,
            'nik'              => $s->nik,
            'no_kk'            => $s->no_kk,
            'kelas'            => $s->kelas,
            'rombel'           => $s->rombel,
            'tahun_masuk'      => $s->tahun_masuk,
            'status'           => $s->status,
            'asal_sekolah'     => $s->asal_sekolah,
            'no_sttb_sd'       => $s->no_sttb_sd,
            'no_un_sd'         => $s->no_un_sd,
            'anak_ke'          => $s->anak_ke,
            'golongan_darah'   => $s->golongan_darah,
            'tinggi_badan'     => $s->tinggi_badan,
            'berat_badan'      => $s->berat_badan,
            'nama_ayah'        => $s->nama_ayah,
            'tahun_lahir_ayah' => $s->tahun_lahir_ayah,
            'pendidikan_ayah'  => $s->pendidikan_ayah,
            'pekerjaan_ayah'   => $s->pekerjaan_ayah,
            'penghasilan_ayah' => $s->penghasilan_ayah,
            'nama_ibu'         => $s->nama_ibu,
            'tahun_lahir_ibu'  => $s->tahun_lahir_ibu,
            'pendidikan_ibu'   => $s->pendidikan_ibu,
            'pekerjaan_ibu'    => $s->pekerjaan_ibu,
            'penghasilan_ibu'  => $s->penghasilan_ibu,
            'nama_wali'        => $s->nama_wali,
            'pekerjaan_wali'   => $s->pekerjaan_wali,
            'no_telepon_ortu'  => $s->no_telepon_ortu,
            'alamat_ortu'      => $s->alamat_ortu,
        ];
    }

    private function templateRow(): array
    {
        return [
            'nisn'             => '1234567890',
            'nis'              => '001',
            'nama_lengkap'     => 'Contoh Nama Siswa',
            'jenis_kelamin'    => 'L',
            'tempat_lahir'     => 'Surabaya',
            'tanggal_lahir'    => '15/08/2010',
            'agama'            => 'Islam',
            'alamat'           => 'Jl. Contoh No. 1',
            'rt'               => '001',
            'rw'               => '002',
            'dusun'            => 'Dsn. Contoh',
            'kelurahan'        => 'Kel. Contoh',
            'kecamatan'        => 'Kec. Contoh',
            'kode_pos'         => '60111',
            'lintang'          => '-7.2504',
            'bujur'            => '112.7688',
            'no_telepon'       => '08123456789',
            'email'            => 'siswa@email.com',
            'nik'              => '3578012345678901',
            'no_kk'            => '3578012345678900',
            'kelas'            => 'VII',
            'rombel'           => 'VII A',
            'tahun_masuk'      => '2024',
            'status'           => 'aktif',
            'asal_sekolah'     => 'SDN Contoh 1',
            'no_sttb_sd'       => 'DN-01-MI-2024-001',
            'no_un_sd'         => '',
            'anak_ke'          => '1',
            'golongan_darah'   => 'A',
            'tinggi_badan'     => '145',
            'berat_badan'      => '38',
            'nama_ayah'        => 'Bapak Contoh',
            'tahun_lahir_ayah' => '1980',
            'pendidikan_ayah'  => 'SMA/SMK/MA',
            'pekerjaan_ayah'   => 'Wiraswasta',
            'penghasilan_ayah' => 'Rp 2.000.000 - Rp 3.000.000',
            'nama_ibu'         => 'Ibu Contoh',
            'tahun_lahir_ibu'  => '1983',
            'pendidikan_ibu'   => 'SMA/SMK/MA',
            'pekerjaan_ibu'    => 'Ibu Rumah Tangga',
            'penghasilan_ibu'  => 'Tidak Berpenghasilan',
            'nama_wali'        => '',
            'pekerjaan_wali'   => '',
            'no_telepon_ortu'  => '08129876543',
            'alamat_ortu'      => 'Jl. Contoh No. 1',
        ];
    }
}
