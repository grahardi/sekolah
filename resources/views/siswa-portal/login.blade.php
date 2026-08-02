<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Portal Siswa</title>
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link href="/vendor/fonts/fonts.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('vendor/tabler-icons/tabler-icons.min.css') }}">
<style>
    * { font-family:'Inter',sans-serif; box-sizing:border-box; }
    body { margin:0; background:linear-gradient(135deg,#1E3A5F,#2563EB); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
    .box { max-width:400px; width:100%; background:#fff; border-radius:16px; padding:32px 28px; }
    h1 { font-family:'Space Grotesk',sans-serif; font-size:20px; margin:0 0 4px; color:#0f172a; text-align:center; }
    p.sub { font-size:13px; color:#64748b; margin:0 0 24px; text-align:center; }
    label { font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px; }
    .form-input { width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:11px 12px; font-size:14px; margin-bottom:16px; }
    .btn { width:100%; background:#1E3A5F; color:#fff; font-weight:700; border:none; border-radius:10px; padding:13px; font-size:14px; cursor:pointer; }
    .error { color:#dc2626; font-size:12px; margin:-10px 0 14px; }
</style>
</head>
<body>
    <div class="box">
        <div style="text-align:center;margin-bottom:16px;">
            <i class="ti ti-school" style="font-size:36px;color:#1E3A5F;"></i>
        </div>
        <h1>Portal Siswa</h1>
        <p class="sub">Masuk pakai NISN dan tanggal lahir untuk lihat nilai & data kamu.</p>

        <form action="{{ route('siswa-portal.login.submit') }}" method="POST">
            @csrf
            <label>NISN</label>
            <input type="text" name="nisn" inputmode="numeric" class="form-input" placeholder="mis. 0132701496" required autofocus value="{{ old('nisn') }}">
            @error('nisn')<p class="error">{{ $message }}</p>@enderror

            <label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="form-input" required>

            <button type="submit" class="btn">Masuk</button>
        </form>
    </div>
</body>
</html>
