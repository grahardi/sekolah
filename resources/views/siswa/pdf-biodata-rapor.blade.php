<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'DejaVu Sans', sans-serif; font-size:10pt; color:#000; background:#fff; }
.page { padding:10mm 20mm 10mm 20mm; }
.judul-dok { text-align:center; margin-bottom:14px; width:100%; }
.judul-dok h2 { font-size:12pt; font-weight:bold; text-transform:uppercase; letter-spacing:2px; border:2.5px solid #000; display:inline-block; padding:5px 18px; }
.sek { background:#000; color:#fff; font-weight:bold; font-size:9pt; padding:3px 8px; margin:10px 0 0; letter-spacing:.5px; }
.section-blok { page-break-inside: avoid; margin-top: 8mm; }
table.data { width:100%; border-collapse:collapse; }
table.data td { padding:2.5px 6px; font-size:9.5pt; vertical-align:top; line-height:1.4; }
table.data td.no  { width:22px; text-align:right; padding-right:4px; }
table.data td.lbl { width:32%; text-align:left; }
table.data td.ttd { width:6px; text-align:center; }
table.data td.val { border-bottom:1px solid #777; }
.footer-table { width:100%; margin-top:20mm; border-collapse:collapse; }
.footer-table td { vertical-align:bottom; padding:0; }
.foto-frame { width:80px; height:100px; border:1.5px solid #333; overflow:hidden; }
.foto-frame img { width:100%; height:100%; object-fit:cover; }
.ttd-wrap { text-align:center; }
.ttd-nama { font-weight:bold; text-decoration:underline; margin-top:60px; }
</style>
</head>
<body>
<div class="page">

<div class="judul-dok">
    <h2>Identitas Rapor Siswa</h2>
</div>

@php
$baris = function ($no, $label, $val) {
    $val = $val ?: '-';
    echo "<tr><td class='no'>{$no}</td><td class='lbl'>{$label}</td><td class='ttd'>:</td><td class='val'>" . e($val) . "</td></tr>";
};
@endphp

<div class="section-blok">
<div class="sek">DATA DIRI</div>
<table class="data">
    @php $baris(1, 'Nama Lengkap', $siswa->nama_lengkap) @endphp
    @php $baris(2, 'Nomor Induk / NISN', "{$siswa->nis} / {$siswa->nisn}") @endphp
    @php $baris(3, 'Jenis Kelamin', $siswa->jenis_kelamin_lengkap) @endphp
    @php $baris(4, 'Tempat, Tanggal Lahir', $siswa->tempat_lahir . ', ' . ($siswa->tanggal_lahir?->locale('id')->translatedFormat('d F Y') ?? '-')) @endphp
    @php $baris(5, 'Agama', $siswa->agama) @endphp
    @php $baris(6, 'Anak ke -', $siswa->anak_ke) @endphp
    @php $baris(7, 'Status dalam Keluarga', 'Anak') @endphp
    @php $baris(8, 'Alamat Siswa', trim($siswa->alamat . ', ' . $siswa->kecamatan, ', ')) @endphp
</table>
</div>

<div class="section-blok">
<div class="sek">DATA KEPENDIDIKAN</div>
<table class="data">
    @php $baris(9, 'Diterima di Sekolah Ini', $siswa->tanggal_diterima?->locale('id')->translatedFormat('d F Y')) @endphp
    @php $baris(10, 'Diterima di Kelas', $siswa->diterima_di_kelas ?: $siswa->kelas) @endphp
    @php $baris(11, 'Sekolah Asal', $siswa->asal_sekolah) @endphp
    @php $baris(12, 'Tahun Lulus SD', $siswa->tahun_masuk) @endphp
</table>
</div>

<div class="section-blok">
<div class="sek">DATA KELUARGA (ORANG TUA - WALI)</div>
<table class="data">
    @php $baris(13, 'Nama Ayah', $siswa->nama_ayah) @endphp
    @php $baris(14, 'Pekerjaan Ayah', $siswa->pekerjaan_ayah) @endphp
    @php $baris(15, 'Nama Ibu', $siswa->nama_ibu) @endphp
    @php $baris(16, 'Pekerjaan Ibu', $siswa->pekerjaan_ibu) @endphp
    @php $baris(17, 'Alamat Orang Tua', $siswa->alamat_ortu ?: $siswa->alamat) @endphp
    @php $baris(18, 'Nama Wali', $siswa->nama_wali) @endphp
    @php $baris(19, 'Pekerjaan Wali', $siswa->pekerjaan_wali) @endphp
</table>
</div>

<table class="footer-table">
    <tr>
        <td style="text-align:center;width:50%;">
            <div class="foto-frame" style="margin:0 auto;">
                @if($siswa->foto && file_exists(public_path('storage/' . $siswa->foto)))
                <img src="{{ public_path('storage/' . $siswa->foto) }}">
                @endif
            </div>
        </td>
        <td style="text-align:center;width:50%;">
            <div class="ttd-wrap" style="margin:0 auto;">
                <p>{{ $kotaTtd }}, {{ $tanggalCetak->locale('id')->translatedFormat('d F Y') }}</p>
                <p>Kepala Sekolah</p>
                <p class="ttd-nama">{{ $sekolah->kepala_sekolah_nama ?: '-' }}</p>
                <p>NIP {{ $sekolah->kepala_sekolah_nip ?: '-' }}</p>
            </div>
        </td>
    </tr>
</table>

</div>
</body>
</html>
