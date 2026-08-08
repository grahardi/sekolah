<div class="judul-dok">
    <h2>Buku Induk Siswa</h2>
    <p>Sekolah Menengah Pertama &mdash; Kurikulum Merdeka</p>
</div>

<div class="no-induk">
    No. Induk&nbsp;:&nbsp;<strong>{{ str_pad($siswa->nis ?? $siswa->id, 6, '0', STR_PAD_LEFT) }}</strong>
    &nbsp;&nbsp;&nbsp; NISN&nbsp;:&nbsp;<strong>{{ $siswa->nisn }}</strong>
    &nbsp;&nbsp;&nbsp; Tahun Masuk&nbsp;:&nbsp;<strong>{{ $siswa->tahun_masuk }}</strong>
</div>

<div class="section-blok">
<div class="sek">A. KETERANGAN TENTANG DIRI SISWA</div>
<table class="data">
    @php
    $baris = function ($no, $label, $val) {
        $val = $val ?: '-';
        echo "<tr><td class='no'>{$no}</td><td class='lbl'>{$label}</td><td class='ttd'>:</td><td class='val'>" . e($val) . "</td></tr>";
    };
    @endphp
    @php $baris(1,  'Nama Lengkap',             $siswa->nama_lengkap) @endphp
    @php $baris(2,  'Jenis Kelamin',             $siswa->jenis_kelamin_lengkap) @endphp
    @php $baris(3,  'Tempat, Tanggal Lahir',     $siswa->tempat_lahir . ', ' . ($siswa->tanggal_lahir?->locale('id')->translatedFormat('d F Y') ?? '-')) @endphp
    @php $baris(4,  'Agama',                     $siswa->agama) @endphp
    @php $baris(5,  'Kewarganegaraan',           'Indonesia') @endphp
    @php $baris(6,  'Anak ke-',                  $siswa->anak_ke) @endphp
    @php $baris(7,  'NIK',                       $siswa->nik) @endphp
    @php $baris(8,  'No. Kartu Keluarga',        $siswa->no_kk) @endphp
    @php $baris(9,  'Nomor Telepon Siswa',       $siswa->no_telepon) @endphp
    @php $baris(10, 'Email',                     $siswa->email) @endphp
</table>
</div>

<p class="spacer-baris">&nbsp;</p>
<div class="section-blok">
<div class="sek">B. KETERANGAN TEMPAT TINGGAL</div>
<table class="data">
    @php $baris(11, 'Alamat (Jalan/Gg.)',   $siswa->alamat) @endphp
    @php $baris(12, 'RT / RW',              'RT ' . ($siswa->rt ?: '-') . ' / RW ' . ($siswa->rw ?: '-')) @endphp
    @php $baris(13, 'Dusun / Desa',         $siswa->dusun) @endphp
    @php $baris(14, 'Kelurahan',            $siswa->kelurahan) @endphp
    @php $baris(15, 'Kecamatan',            $siswa->kecamatan) @endphp
    @php $baris(16, 'Kode Pos',             $siswa->kode_pos) @endphp
    @php $baris(17, 'Koordinat (Lat, Long)',($siswa->lintang && $siswa->bujur) ? number_format($siswa->lintang,7) . ', ' . number_format($siswa->bujur,7) : null) @endphp
</table>
</div>

<p class="spacer-baris">&nbsp;</p>
<div class="section-blok">
<div class="sek">C. KETERANGAN KESEHATAN</div>
<table class="data">
    @php $baris(18, 'Golongan Darah',       $siswa->golongan_darah) @endphp
    @php $baris(19, 'Tinggi / Berat Badan', ($siswa->tinggi_badan ?: '-') . ' cm / ' . ($siswa->berat_badan ?: '-') . ' kg') @endphp
    @php $baris(20, 'Riwayat Penyakit',     $siswa->riwayat_penyakit) @endphp
</table>
</div>

<p class="spacer-baris">&nbsp;</p>
<div class="section-blok">
<div class="sek">D. KETERANGAN PENDIDIKAN</div>
<table class="data">
    @php $baris(21, 'Asal Sekolah SD/MI',  $siswa->asal_sekolah) @endphp
    @php $baris(22, 'No. STTB / Ijazah SD',$siswa->no_sttb_sd) @endphp
    @php $baris(23, 'No. UN SD',           $siswa->no_un_sd) @endphp
    @php $baris(24, 'Diterima di Kelas',   $siswa->kelas) @endphp
    @php $baris(25, 'Tanggal Diterima',    $siswa->tanggal_diterima?->locale('id')->translatedFormat('d F Y')) @endphp
    @php $baris(26, 'Tahun Masuk',         $siswa->tahun_masuk) @endphp
    @php $baris(27, 'Status',              ucfirst($siswa->status)) @endphp
</table>
</div>

<p class="spacer-baris">&nbsp;</p>
<div class="section-blok">
<div class="sek">E. KETERANGAN TENTANG AYAH KANDUNG</div>
<table class="data">
    @php $baris(28, 'Nama Ayah',           $siswa->nama_ayah) @endphp
    @php $baris(29, 'Tahun Lahir',         $siswa->tahun_lahir_ayah) @endphp
    @php $baris(30, 'Agama',               $siswa->agama) @endphp
    @php $baris(31, 'Jenjang Pendidikan',  $siswa->pendidikan_ayah) @endphp
    @php $baris(32, 'Pekerjaan',           $siswa->pekerjaan_ayah) @endphp
    @php $baris(33, 'Penghasilan/Bulan',   $siswa->penghasilan_ayah) @endphp
    @php $baris(34, 'No. Telepon',         $siswa->no_telepon_ortu) @endphp
</table>
</div>

<p class="spacer-baris">&nbsp;</p>
<div class="section-blok">
<div class="sek">F. KETERANGAN TENTANG IBU KANDUNG</div>
<table class="data">
    @php $baris(35, 'Nama Ibu',            $siswa->nama_ibu) @endphp
    @php $baris(36, 'Tahun Lahir',         $siswa->tahun_lahir_ibu) @endphp
    @php $baris(37, 'Agama',               $siswa->agama) @endphp
    @php $baris(38, 'Jenjang Pendidikan',  $siswa->pendidikan_ibu) @endphp
    @php $baris(39, 'Pekerjaan',           $siswa->pekerjaan_ibu) @endphp
    @php $baris(40, 'Penghasilan/Bulan',   $siswa->penghasilan_ibu) @endphp
</table>
</div>

<p class="spacer-baris">&nbsp;</p>
<div class="section-blok">
<div class="sek">G. KETERANGAN TENTANG WALI</div>
<table class="data">
    @php $baris(41, 'Nama Wali',   $siswa->nama_wali) @endphp
    @php $baris(42, 'Pekerjaan',   $siswa->pekerjaan_wali) @endphp
    @php $baris(43, 'No. Telepon', $siswa->nama_wali ? $siswa->no_telepon_ortu : null) @endphp
    @php $baris(44, 'Alamat',      $siswa->nama_wali ? $siswa->alamat_ortu : null) @endphp
</table>
</div>

<div class="foto-section">
    <div class="foto-frame">
        @if($siswa->foto && file_exists(public_path('storage/' . $siswa->foto)))
            <img src="{{ public_path('storage/' . $siswa->foto) }}">
        @else
            <div style="width:100%;height:100%;background:#f0f0f0;display:flex;align-items:center;justify-content:center;">
                <p style="font-size:7pt;color:#aaa;text-align:center;padding:6px;">Foto<br>Siswa<br>3×4</p>
            </div>
        @endif
    </div>
    <p style="font-size:7.5pt;margin-top:4px;color:#555;">{{ $siswa->nama_lengkap }}</p>
</div>


<p style="font-size:7pt;color:#aaa;text-align:center;margin-top:14px;border-top:1px solid #ddd;padding-top:5px;">
    Dicetak otomatis &mdash; {{ now()->format('d F Y, H:i') }}
</p>
