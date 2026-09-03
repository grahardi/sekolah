<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
@page { margin: 20mm; }
body { font-family:'DejaVu Sans', sans-serif; font-size:11pt; color:#000; }
.judul { text-align:center; font-weight:bold; text-transform:uppercase; font-size:13pt; letter-spacing:1px; margin-bottom:20px; }
table.biodata { width:100%; border-collapse:collapse; }
table.biodata td { padding:3px 0; vertical-align:top; font-size:11pt; }
td.no { width:22px; }
td.label { width:190px; }
td.titik { width:14px; }
td.sub-label { padding-left:22px; }
.ttd-wrap { width:260px; margin-left:auto; margin-top:40px; text-align:center; }
.ttd-nama { font-weight:bold; text-decoration:underline; margin-top:65px; }
</style>
</head>
<body>

<div class="judul">Identitas Rapor Siswa</div>

<table class="biodata">
    <tr><td class="no">1.</td><td class="label">Nama Lengkap</td><td class="titik">:</td><td>{{ $siswa->nama_lengkap }}</td></tr>
    <tr><td class="no">2.</td><td class="label">Nomor Induk / NISN</td><td class="titik">:</td><td>{{ $siswa->nis }} / {{ $siswa->nisn }}</td></tr>
    <tr><td class="no">3.</td><td class="label">Jenis Kelamin</td><td class="titik">:</td><td>{{ $siswa->jenis_kelamin_lengkap }}</td></tr>
    <tr><td class="no">4.</td><td class="label">Tempat, Tanggal Lahir</td><td class="titik">:</td><td>{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir?->locale('id')->translatedFormat('d F Y') }}</td></tr>
    <tr><td class="no">5.</td><td class="label">Agama</td><td class="titik">:</td><td>{{ $siswa->agama }}</td></tr>
    <tr><td class="no">6.</td><td class="label">Anak ke &ndash;</td><td class="titik">:</td><td>{{ $siswa->anak_ke ?: '-' }}</td></tr>
    <tr><td class="no">7.</td><td class="label">Status dalam Keluarga</td><td class="titik">:</td><td>Anak</td></tr>
    <tr><td class="no">8.</td><td class="label">Alamat Siswa</td><td class="titik">:</td><td>{{ $siswa->alamat }}</td></tr>
    <tr><td></td><td></td><td></td><td>{{ $siswa->kecamatan }}</td></tr>
    <tr><td class="no">9.</td><td class="label">Diterima di Sekolah Ini</td><td class="titik">:</td><td>{{ $siswa->tanggal_diterima?->locale('id')->translatedFormat('d F Y') ?: '-' }}</td></tr>
    <tr><td class="no">10.</td><td class="label">Sekolah Asal</td><td class="titik">:</td><td>{{ $siswa->asal_sekolah ?: '-' }}</td></tr>
    <tr><td class="no">11.</td><td class="label">Tahun Lulus SD</td><td class="titik">:</td><td>{{ $siswa->tahun_masuk ?: '-' }}</td></tr>
    <tr><td class="no">12.</td><td class="label">Nama Orang Tua</td><td></td><td></td></tr>
    <tr><td></td><td class="sub-label">a. Nama Ayah</td><td class="titik">:</td><td>{{ $siswa->nama_ayah ?: '-' }}</td></tr>
    <tr><td></td><td class="sub-label">b. Nama Ibu</td><td class="titik">:</td><td>{{ $siswa->nama_ibu ?: '-' }}</td></tr>
    <tr><td class="no">13.</td><td class="label">Alamat Orang Tua</td><td class="titik">:</td><td>{{ $siswa->alamat_ortu ?: $siswa->alamat }}</td></tr>
    <tr><td class="no">14.</td><td class="label">Pekerjaan Orang Tua</td><td></td><td></td></tr>
    <tr><td></td><td class="sub-label">a. Pekerjaan Ayah</td><td class="titik">:</td><td>{{ $siswa->pekerjaan_ayah ?: '-' }}</td></tr>
    <tr><td></td><td class="sub-label">b. Pekerjaan Ibu</td><td class="titik">:</td><td>{{ $siswa->pekerjaan_ibu ?: '-' }}</td></tr>
    <tr><td class="no">15.</td><td class="label">Nama Wali</td><td class="titik">:</td><td>{{ $siswa->nama_wali ?: '-' }}</td></tr>
    <tr><td class="no">16.</td><td class="label">Alamat Wali</td><td class="titik">:</td><td>-</td></tr>
    <tr><td class="no">17.</td><td class="label">Pekerjaan Wali</td><td class="titik">:</td><td>{{ $siswa->pekerjaan_wali ?: '-' }}</td></tr>
</table>

<div class="ttd-wrap">
    <p>{{ $kotaTtd }}, {{ now()->locale('id')->translatedFormat('d F Y') }}</p>
    <p>Kepala Sekolah</p>
    <p class="ttd-nama">{{ $sekolah->kepala_sekolah_nama ?: '-' }}</p>
    <p>NIP {{ $sekolah->kepala_sekolah_nip ?: '-' }}</p>
</div>

</body>
</html>
