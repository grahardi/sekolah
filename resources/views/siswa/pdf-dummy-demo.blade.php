<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<style>
    body { font-family: sans-serif; padding: 60px; color: #1E293B; }
    .stempel {
        border: 4px double #dc2626; color: #dc2626; display: inline-block;
        padding: 14px 28px; font-size: 22px; font-weight: bold; transform: rotate(-8deg);
        margin-bottom: 40px; letter-spacing: 2px;
    }
    h1 { font-size: 20px; border-bottom: 2px solid #1E293B; padding-bottom: 10px; }
    table { width: 100%; margin-top: 20px; border-collapse: collapse; }
    td { padding: 8px 4px; font-size: 13px; border-bottom: 1px solid #e2e8f0; }
    td:first-child { color: #64748b; width: 200px; }
    .footer { margin-top: 60px; font-size: 11px; color: #94a3b8; text-align: center; }
</style>
</head>
<body>
    <div class="stempel">CONTOH / DEMO</div>
    <h1>{{ $label }}</h1>
    <p style="color:#64748b;font-size:13px;">
        Dokumen ini adalah placeholder untuk keperluan demonstrasi sistem, bukan dokumen resmi.
    </p>
    <table>
        <tr><td>Nama Siswa</td><td>{{ $siswa->nama_lengkap }}</td></tr>
        <tr><td>NISN</td><td>{{ $siswa->nisn }}</td></tr>
        <tr><td>NIS</td><td>{{ $siswa->nis }}</td></tr>
        <tr><td>Kelas</td><td>{{ $siswa->kelas }} - {{ $siswa->rombel }}</td></tr>
        <tr><td>Jenis Dokumen</td><td>{{ $label }}</td></tr>
    </table>
    <div class="footer">Dihasilkan otomatis oleh sistem demo sekolah.co.id</div>
</body>
</html>
