<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'DejaVu Sans', sans-serif; font-size:10px; color:#1e293b; }
.header { text-align:center; margin-bottom:20px; padding-bottom:12px; border-bottom:2px solid #1e40af; }
.header h1 { font-size:18px; color:#1e40af; font-weight:bold; }
.header p { color:#64748b; font-size:10px; margin-top:3px; }
.meta { text-align:right; font-size:9px; color:#94a3b8; margin-bottom:10px; }
table { width:100%; border-collapse:collapse; }
th { background:#1e40af; color:#fff; padding:7px 6px; text-align:left; font-size:9px; text-transform:uppercase; letter-spacing:.05em; }
td { padding:6px 6px; border-bottom:1px solid #f1f5f9; font-size:9px; vertical-align:middle; }
tr:nth-child(even) td { background:#f8fafc; }
.badge { display:inline-block; padding:1px 6px; border-radius:9999px; font-size:8px; font-weight:600; }
.aktif { background:#dcfce7; color:#166534; } .lulus { background:#dbeafe; color:#1e40af; }
.keluar { background:#fee2e2; color:#991b1b; } .pindah { background:#fef9c3; color:#854d0e; }
.footer { margin-top:15px; text-align:right; font-size:9px; color:#94a3b8; }
</style>
</head>
<body>
<div class="header"><h1>📚 BUKU INDUK SISWA</h1><p>Dicetak pada: {{ now()->format('d F Y, H:i') }}</p></div>
<div class="meta">Total: {{ $siswas->count() }} siswa</div>
<table>
    <thead>
        <tr><th>No</th><th>NISN/NIS</th><th>Nama Lengkap</th><th>L/P</th><th>Tgl Lahir</th><th>Kelas</th><th>Agama</th><th>Status</th></tr>
    </thead>
    <tbody>
        @foreach($siswas as $i => $s)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td><strong>{{ $s->nisn }}</strong><br><span style="color:#94a3b8">{{ $s->nis }}</span></td>
            <td><strong>{{ $s->nama_lengkap }}</strong></td>
            <td>{{ $s->jenis_kelamin }}</td>
            <td>{{ $s->tempat_lahir }}, {{ $s->tanggal_lahir->format('d/m/Y') }}</td>
            <td><strong>{{ $s->rombel ?? $s->kelas }}</strong></td>
            <td>{{ $s->agama }}</td>
            <td><span class="badge {{ $s->status }}">{{ ucfirst($s->status) }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="footer">Dokumen dibuat otomatis oleh Sistem Buku Induk Siswa</div>
</body>
</html>
