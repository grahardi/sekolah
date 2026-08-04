<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - Manajemen Sekolah</title>
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link href="/vendor/fonts/fonts.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('vendor/tabler-icons/tabler-icons.min.css') }}">
<style>
    * { font-family:'Inter',sans-serif; box-sizing:border-box; }
    body { margin:0; background:linear-gradient(160deg,#1E3A5F,#2563EB); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
    .box { max-width:400px; width:100%; background:#fff; border-radius:20px; overflow:hidden; }
    .box-header { background:linear-gradient(135deg,#1E3A5F,#2563EB); padding:32px 28px 26px; text-align:center; }
    .box-header img, .box-header .logo-fallback { width:64px; height:64px; border-radius:50%; margin:0 auto 12px; display:flex; align-items:center; justify-content:center; background:#fff; }
    .box-header .logo-fallback i { font-size:30px; color:#1E3A5F; }
    .box-header h1 { font-family:'Space Grotesk',sans-serif; font-size:16px; color:#fff; margin:0 0 2px; font-weight:700; }
    .box-header p { font-size:11.5px; color:rgba(255,255,255,.8); margin:0; }
    .box-body { padding:28px; }
    label { font-size:12px; font-weight:600; color:#374151; display:block; margin-bottom:6px; }
    .form-input { width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:11px 12px; font-size:14px; margin-bottom:16px; }
    .btn { width:100%; background:#1E3A5F; color:#fff; font-weight:700; border:none; border-radius:10px; padding:13px; font-size:14px; cursor:pointer; }
    .error { color:#dc2626; font-size:12px; margin:-10px 0 14px; }
    .ingat { display:flex; align-items:center; gap:8px; margin-bottom:18px; font-size:13px; color:#374151; }
    .footnote { text-align:center; font-size:11px; color:#94a3b8; margin-top:18px; }
</style>
</head>
<body>
    <div class="box">
        <div class="box-header">
            <div class="logo-fallback"><i class="ti ti-building"></i></div>
            <h1>sekolah.co.id</h1>
            <p>Manajemen Sekolah - Sistem Informasi Manajemen Terpadu</p>
        </div>
        <div class="box-body">
            <form action="{{ route('manajemen-sekolah.login.submit') }}" method="POST">
                @csrf
                <label>Email / Nomor ID</label>
                <input type="text" name="email" class="form-input" required autofocus value="{{ old('email') }}">
                @error('email')<p class="error">{{ $message }}</p>@enderror

                <label>Password</label>
                <input type="password" name="password" class="form-input" required>

                <label class="ingat"><input type="checkbox" name="ingat" value="1"> Ingat saya</label>

                <button type="submit" class="btn">Masuk</button>
            </form>
            <p class="footnote">&copy; {{ date('Y') }} sekolah.co.id - Kembali ke <a href="/dashboard" style="color:#2563EB;">Portal Utama</a></p>
        </div>
    </div>
</body>
</html>
