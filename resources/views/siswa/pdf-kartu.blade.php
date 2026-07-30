<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'DejaVu Sans', sans-serif; color:#1e293b; }
.card { border:2px solid #1e40af; border-radius:12px; overflow:hidden; max-width:400px; margin:0 auto; }
.card-header { background:linear-gradient(135deg,#1e40af,#3b82f6); color:#fff; padding:16px; display:flex; align-items:center; gap:12px; }
.card-header-text h1 { font-size:14px; font-weight:bold; }
.card-header-text p { font-size:10px; opacity:.85; margin-top:2px; }
.card-body { padding:16px; }
.student-info { display:flex; gap:14px; margin-bottom:14px; }
.foto-box { width:80px; height:95px; background:#e2e8f0; border-radius:8px; overflow:hidden; flex-shrink:0; }
.foto-box img { width:100%; height:100%; object-fit:cover; }
.name-block h2 { font-size:15px; font-weight:bold; }
.name-block .kelas-badge { display:inline-block; background:#dbeafe; color:#1e40af; font-size:10px; font-weight:bold; padding:2px 8px; border-radius:999px; margin-top:4px; }
.detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
.detail-item label { font-size:8px; text-transform:uppercase; letter-spacing:.05em; color:#94a3b8; font-weight:bold; display:block; margin-bottom:2px; }
.detail-item span { font-size:10px; color:#334155; font-weight:600; }
.divider { border-top:1px dashed #e2e8f0; margin:12px 0; }
.parent-section h3 { font-size:9px; text-transform:uppercase; letter-spacing:.05em; color:#94a3b8; font-weight:bold; margin-bottom:8px; }
.card-footer { background:#f8fafc; border-top:1px solid #e2e8f0; padding:8px 16px; text-align:center; font-size:8px; color:#94a3b8; }
</style>
</head>
<body>
<div class="card">
    <div class="card-header">
        <div style="font-size:28px">📚</div>
        <div class="card-header-text">
            <h1>KARTU IDENTITAS SISWA</h1>
            <p>Buku Induk Siswa &mdash; {{ date('Y') }}</p>
        </div>
    </div>
    <div class="card-body">
        <div class="student-info">
            <div class="foto-box"><img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama_lengkap }}"></div>
            <div class="name-block">
                <h2>{{ $siswa->nama_lengkap }}</h2>
                <div class="kelas-badge">Kelas {{ $siswa->kelas }}{{ $siswa->rombel ? ' — ' . $siswa->rombel : '' }}</div>
                <div style="margin-top:8px;font-size:9px;color:#64748b;">
                    <div><strong>NISN:</strong> {{ $siswa->nisn }}</div>
                    @if($siswa->nis)<div><strong>NIS:</strong> {{ $siswa->nis }}</div>@endif
                </div>
            </div>
        </div>
        <div class="detail-grid">
            <div class="detail-item"><label>Jenis Kelamin</label><span>{{ $siswa->jenis_kelamin_lengkap }}</span></div>
            <div class="detail-item"><label>Agama</label><span>{{ $siswa->agama }}</span></div>
            <div class="detail-item"><label>Tempat, Tgl Lahir</label><span>{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->format('d/m/Y') }}</span></div>
            <div class="detail-item"><label>Tahun Masuk</label><span>{{ $siswa->tahun_masuk }}</span></div>
            <div class="detail-item" style="grid-column:span 2"><label>Alamat</label><span>{{ $siswa->alamat }}</span></div>
        </div>
        @if($siswa->nama_ayah || $siswa->nama_ibu)
        <div class="divider"></div>
        <div class="parent-section">
            <h3>Data Orang Tua / Wali</h3>
            <div class="detail-grid">
                @if($siswa->nama_ayah)<div class="detail-item"><label>Nama Ayah</label><span>{{ $siswa->nama_ayah }}</span></div>@endif
                @if($siswa->nama_ibu)<div class="detail-item"><label>Nama Ibu</label><span>{{ $siswa->nama_ibu }}</span></div>@endif
                @if($siswa->no_telepon_ortu)<div class="detail-item"><label>No. Telepon</label><span>{{ $siswa->no_telepon_ortu }}</span></div>@endif
            </div>
        </div>
        @endif
    </div>
    <div class="card-footer">Dicetak {{ now()->format('d F Y') }} &bull; Sistem Buku Induk Siswa</div>
</div>
</body>
</html>
