<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Terima Kasih</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Space+Grotesk:wght@700&display=swap" rel="stylesheet">
<style>
    * { font-family:'Inter',sans-serif; box-sizing:border-box; }
    body { margin:0; background:#F5F9FF; color:#1E293B; display:flex; align-items:center; justify-content:center; min-height:100vh; padding:20px; }
    .box { text-align:center; background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:40px 28px; max-width:400px; }
    .icon { width:60px; height:60px; background:#dcfce7; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px; font-size:28px; }
    h1 { font-family:'Space Grotesk',sans-serif; font-size:20px; margin:0 0 8px; }
    p { font-size:14px; color:#64748b; margin:0; }
</style>
</head>
<body>
    <div class="box">
        <div class="icon">✅</div>
        <h1>Terima Kasih!</h1>
        <p>Jawaban kamu untuk "{{ $survey->judul }}" sudah berhasil dikirim.</p>
    </div>
</body>
</html>
