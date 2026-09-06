<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Data Alumni - {{ $siswa->nama_lengkap }}</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; background:#f0fdf4; min-height:100vh; padding:20px; }
.wrap { max-width:480px; margin:0 auto; }
.card { background:#fff; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,.05); padding:22px; margin-bottom:16px; }
.btn-outline { background:#fff; color:#16a34a; border:2px solid #16a34a; padding:11px 20px; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; width:100%; text-align:center; text-decoration:none; display:block; }
</style>
</head>
<body>
<div class="wrap">
    <div style="text-align:center;margin-bottom:20px;">
        <p style="font-size:12px;color:#94a3b8;margin:0 0 4px;">{{ $sekolah->nama }}</p>
        <p style="font-size:18px;font-weight:700;color:#0f172a;margin:0 0 12px;">Data Alumni</p>
        <div style="display:flex;align-items:center;justify-content:center;gap:12px;">
            <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama_lengkap }}" style="width:54px;height:54px;border-radius:12px;object-fit:cover;background:#dcfce7;"
                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($siswa->nama_lengkap) }}&background=dcfce7&color=166534&size=54'">
            <div style="text-align:left;">
                <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">{{ $siswa->nama_lengkap }}</p>
                <p style="font-size:12px;color:#64748b;margin:2px 0 0;">NISN {{ $siswa->nisn }} &middot; Lulus {{ $siswa->tahun_lulus ?: '-' }}</p>
            </div>
        </div>
        <form action="{{ route('alumni-publik.keluar', $npsn) }}" method="POST" style="margin-top:10px;">
            @csrf
            <button type="submit" style="border:none;background:none;color:#94a3b8;font-size:11px;text-decoration:underline;cursor:pointer;">Bukan {{ $siswa->nama_lengkap }}? Ganti akun</button>
        </form>
    </div>

    @if(session('success'))
    <div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;text-align:center;"><i class="ti ti-check"></i> {{ session('success') }}</div>
    @endif

    <div class="card" style="text-align:center;">
        <i class="ti ti-circle-check" style="font-size:40px;color:#16a34a;"></i>
        <p style="font-size:12px;color:#94a3b8;margin:10px 0 4px;text-transform:uppercase;font-weight:700;">Data Kamu Tersimpan</p>
        <p style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">{{ $siswa->alumni_label }}</p>
        <p style="font-size:12px;color:#94a3b8;margin:8px 0 0;">Diisi {{ $siswa->alumni_diisi_at->locale('id')->diffForHumans() }}</p>
    </div>

    @if($ajuanUlangAktif)
    <div class="card" style="background:#fffbeb;border:1px solid #fde68a;">
        <p style="font-size:12px;color:#92400e;margin:0;"><i class="ti ti-clock"></i> Ada pengajuan perubahan menuju <strong>{{ $ajuanUlangAktif->labelTujuan() }}</strong> yang sedang menunggu persetujuan admin sekolah.</p>
    </div>
    @else
    <a href="{{ route('alumni-publik.ajuan-ulang.form', $npsn) }}" class="btn-outline"><i class="ti ti-edit"></i> Ajukan Perubahan</a>
    @endif
</div>
</body>
</html>
