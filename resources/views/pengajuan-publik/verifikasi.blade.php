<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Verifikasi - Pengajuan Perubahan Data</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; background:#f8fafc; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
.card { background:#fff; border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,.06); padding:32px; max-width:420px; width:100%; }
.form-label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:5px; }
.form-input { width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; }
.btn-primary { width:100%; background:#1E3A5F; color:#fff; border:none; padding:12px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; }
</style>
</head>
<body>
<div class="card">
    <div style="text-align:center;margin-bottom:22px;">
        <p style="font-size:12px;color:#94a3b8;margin:0 0 4px;">{{ $sekolah->nama }}</p>
        <p style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">Pengajuan Perubahan Data</p>
        <p style="font-size:13px;color:#64748b;margin:6px 0 0;">Masukkan data untuk verifikasi identitas</p>
    </div>

    @if($errors->any())
    <div style="background:#fef2f2;color:#991b1b;padding:12px 14px;border-radius:8px;margin-bottom:16px;font-size:13px;">
        @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
    </div>
    @endif

    <form action="{{ route('pengajuan-publik.verifikasi.proses', $npsn) }}" method="POST">
        @csrf
        <div style="margin-bottom:14px;">
            <label class="form-label">No. Induk (NIS atau NISN)</label>
            <input type="text" name="no_induk" class="form-input" required value="{{ old('no_induk') }}">
        </div>
        <div style="margin-bottom:14px;">
            <label class="form-label">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="form-input" required value="{{ old('tanggal_lahir') }}">
        </div>
        <div style="margin-bottom:20px;">
            <label class="form-label">Token Kelas</label>
            <input type="text" name="token" class="form-input" required placeholder="Diberikan oleh wali kelas" value="{{ old('token') }}" style="text-transform:uppercase;">
        </div>
        <button type="submit" class="btn-primary">Lanjutkan</button>
    </form>

    <p style="font-size:11px;color:#94a3b8;text-align:center;margin-top:16px;">Token bisa diminta ke wali kelas kalau belum punya.</p>
</div>
</body>
</html>
