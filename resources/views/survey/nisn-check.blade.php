<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $survey->judul }}</title>
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@700&display=swap" rel="stylesheet">
<style>
    * { font-family:'Inter',sans-serif; box-sizing:border-box; }
    body { margin:0; background:#F5F9FF; color:#1E293B; display:flex; align-items:center; justify-content:center; min-height:100vh; padding:20px; }
    .box { max-width:420px; width:100%; background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:32px 26px; text-align:center; }
    h1 { font-family:'Space Grotesk',sans-serif; font-size:19px; margin:12px 0 6px; }
    p { font-size:13px; color:#64748b; margin:0 0 20px; }
    .form-input { width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:12px; font-size:16px; text-align:center; letter-spacing:1px; }
    .btn { width:100%; background:#2563EB; color:#fff; font-weight:700; border:none; border-radius:10px; padding:13px; font-size:14px; cursor:pointer; margin-top:14px; }
    .error { color:#dc2626; font-size:12px; margin-top:8px; }
</style>
</head>
<body>
    <div class="box">
        <img src="/images/logo-icon.png" alt="" style="height:40px;">
        <h1>{{ $survey->judul }}</h1>
        <p>Masukkan NISN kamu untuk mulai mengisi survey ini.</p>

        <form action="{{ route('survey.public.verifikasi', $project->token) }}" method="POST">
            @csrf
            <input type="text" name="nisn" inputmode="numeric" placeholder="Contoh: 0012345678" class="form-input" required autofocus>
            <button type="submit" class="btn">Lanjutkan</button>
        </form>
        @error('nisn')<p class="error">{{ $message }}</p>@enderror
    </div>
</body>
</html>
