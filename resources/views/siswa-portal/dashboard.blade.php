<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Portal Siswa - {{ $siswa->nama_lengkap }}</title>
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link href="/vendor/fonts/fonts.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('vendor/tabler-icons/tabler-icons.min.css') }}">
<style>
    * { font-family:'Inter',sans-serif; box-sizing:border-box; }
    body { margin:0; background:#f5f9ff; color:#1E293B; }
    .wrap { max-width:1100px; margin:0 auto; padding:20px; }
    .banner { background:linear-gradient(135deg,#1E3A5F,#2563EB); border-radius:14px; padding:22px 26px; color:#fff; display:flex; align-items:center; gap:16px; flex-wrap:wrap; margin-bottom:20px; position:relative; }
    .banner img { width:64px; height:64px; border-radius:50%; object-fit:cover; border:3px solid rgba(255,255,255,.4); }
    .banner h1 { font-family:'Space Grotesk',sans-serif; font-size:20px; margin:0 0 4px; }
    .banner p { font-size:12px; opacity:.85; margin:0; }
    .grid { display:grid; grid-template-columns:2fr 1fr; gap:20px; }
    @media (max-width:800px) { .grid { grid-template-columns:1fr; } }
    .card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; overflow:hidden; margin-bottom:16px; }
    .card-header { padding:14px 18px; font-weight:700; color:#1e40af; font-size:14px; border-bottom:1px solid #f1f5f9; }
    .mapel-row { display:flex; justify-content:space-between; align-items:center; padding:12px 18px; background:#fffbeb; border-bottom:1px solid #f1f5f9; cursor:pointer; }
    .mapel-row .left { display:flex; align-items:center; gap:10px; }
    .kode-badge { background:#eff6ff; color:#1e40af; font-size:10px; font-weight:700; padding:3px 8px; border-radius:6px; }
    .nilai-badge { background:#fed7aa; color:#9a3412; font-weight:800; font-size:14px; padding:4px 10px; border-radius:8px; }
    .tp-body { padding:14px 18px; background:#fff; border-bottom:1px solid #f1f5f9; }
    .tp-label { font-size:10px; color:#94a3b8; text-transform:uppercase; letter-spacing:.03em; margin:0 0 8px; }
    .tp-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(90px,1fr)); gap:8px; }
    .tp-cell { border:1px solid #e2e8f0; border-radius:8px; padding:8px; text-align:center; }
    .tp-cell .n { font-size:10px; color:#94a3b8; }
    .tp-cell .v { font-weight:700; color:#0f172a; font-size:14px; }
    .side-item { display:flex; align-items:center; gap:8px; padding:10px 18px; font-size:13px; color:#374151; border-bottom:1px solid #f8fafc; }
    .logout { position:absolute; top:16px; right:16px; background:rgba(255,255,255,.15); color:#fff; border:none; border-radius:8px; padding:6px 12px; font-size:12px; cursor:pointer; }
</style>
</head>
<body>
<div class="wrap">
    <div class="banner">
        @if(!$viaQr)<button class="logout" onclick="location.href='{{ route('siswa-portal.logout') }}'"><i class="ti ti-logout"></i> Keluar</button>@endif
        <img src="{{ $siswa->foto_url }}" alt="">
        <div>
            <h1>Halo, {{ strtoupper($siswa->nama_lengkap) }}!</h1>
            <p>{{ $siswa->rombel_lengkap }} &middot; NISN: {{ $siswa->nisn }} &middot; T.A: {{ $tahunAktif->nama ?? '-' }} &middot; Semester: {{ $tahunAktif && $tahunAktif->semester === 'Genap' ? 'Genap' : 'Ganjil' }}</p>
        </div>
    </div>

    <div class="grid">
        <div>
            <div class="card">
                <div class="card-header"><i class="ti ti-file-text"></i> Nilai {{ $tahunAktif->semester ?? '' }} {{ $tahunAktif->nama ?? '' }}</div>
                @forelse($mapelData as $i => $m)
                <div class="mapel-row" onclick="toggleMapel({{ $i }})">
                    <div class="left">
                        <span class="kode-badge">{{ strtoupper(Str::limit($m['mapel']->nama, 3, '')) }}</span>
                        <strong style="font-size:13px;">{{ $m['mapel']->nama }}</strong>
                    </div>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <span class="nilai-badge">{{ $m['nilai_akhir'] ?? '-' }}</span>
                        <i class="ti ti-chevron-down" id="chev-{{ $i }}"></i>
                    </div>
                </div>
                <div class="tp-body" id="tp-{{ $i }}" style="display:none;">
                    <p class="tp-label">Nilai Tujuan Pembelajaran (TP)</p>
                    <div class="tp-grid">
                        @forelse($m['per_tp'] as $j => $pt)
                        <div class="tp-cell"><div class="n">TP{{ $j + 1 }}</div><div class="v">{{ $pt['nilai'] }}</div></div>
                        @empty
                        <p style="font-size:12px;color:#94a3b8;">Belum ada nilai TP.</p>
                        @endforelse
                        <div class="tp-cell" style="border-color:#1E3A5F;background:#eff6ff;"><div class="n" style="color:#1E3A5F;font-weight:600;">STS/UTS</div><div class="v" style="color:#1E3A5F;">{{ $m['sts'] ?? '-' }}</div></div>
                    </div>
                </div>
                @empty
                <p style="padding:20px;text-align:center;color:#94a3b8;font-size:13px;">Belum ada nilai untuk semester ini.</p>
                @endforelse
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-header"><i class="ti ti-calendar-check"></i> Kehadiran</div>
                <div class="side-item"><i class="ti ti-vaccine" style="color:#dc2626;"></i> Sakit: {{ $rapor->sakit ?? 0 }} hari</div>
                <div class="side-item"><i class="ti ti-mail" style="color:#d97706;"></i> Izin: {{ $rapor->izin ?? 0 }} hari</div>
                <div class="side-item"><i class="ti ti-x" style="color:#64748b;"></i> Tanpa Keterangan: {{ $rapor->tanpa_keterangan ?? 0 }} hari</div>
            </div>
            <div class="card">
                <div class="card-header"><i class="ti ti-clock"></i> Keterlambatan</div>
                <div class="side-item" style="color:#94a3b8;">Belum ada data</div>
            </div>
            <div class="card">
                <div class="card-header"><i class="ti ti-books"></i> Peminjaman Buku</div>
                <div class="side-item" style="color:#94a3b8;">Belum ada data</div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleMapel(i) {
        const body = document.getElementById('tp-' + i);
        const chev = document.getElementById('chev-' + i);
        const open = body.style.display !== 'none';
        body.style.display = open ? 'none' : 'block';
        chev.style.transform = open ? 'rotate(0deg)' : 'rotate(180deg)';
    }
</script>
</body>
</html>
