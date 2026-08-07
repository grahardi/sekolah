<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<title>Membuka Server Ujian...</title>
<style>
    * { font-family: -apple-system, sans-serif; box-sizing: border-box; }
    body { margin: 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; background: #f5f9ff; padding: 20px; }
    .box { max-width: 460px; width: 100%; background: #fff; border-radius: 16px; padding: 32px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,.06); }
    .spinner { width: 32px; height: 32px; border: 3px solid #e2e8f0; border-top-color: #2563EB; border-radius: 50%; margin: 0 auto 16px; animation: spin 0.8s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
    h1 { font-size: 16px; color: #0f172a; margin: 0 0 8px; }
    p { font-size: 13px; color: #64748b; margin: 0 0 4px; }
    .fallback { display: none; text-align: left; background: #f8fafc; border-radius: 10px; padding: 16px; margin-top: 20px; font-size: 12.5px; }
    .fallback code { display: block; background: #0f172a; color: #4ade80; padding: 10px; border-radius: 6px; font-size: 11px; word-break: break-all; margin: 8px 0; }
    .btn { display: inline-block; margin-top: 14px; padding: 10px 20px; background: #2563EB; color: #fff; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; }
    .btn-copy { background: #f1f5f9; color: #374151; padding: 6px 12px; border-radius: 6px; font-size: 11px; border: none; cursor: pointer; margin-top: 6px; }
</style>
</head>
<body>
<div class="box">
    <div class="spinner" id="spinner"></div>
    <h1 id="judul">Menyiapkan akses ke Server Ujian...</h1>
    <p id="sub">Mohon tunggu sebentar.</p>

    <div class="fallback" id="fallback">
        <p><strong>Login otomatis tidak bisa dilakukan penuh</strong> (browser membatasi akses antar domain berbeda). Ikuti langkah ini:</p>
        <p>1. Server Ujian akan terbuka di tab baru</p>
        <p>2. Tekan <strong>F12</strong> untuk buka DevTools, klik tab <strong>Console</strong></p>
        <p>3. Paste baris ini, tekan Enter:</p>
        @if($token)
        <code id="kode-console">localStorage.setItem('{{ $localStorageKey }}', '{{ $token }}'); location.reload();</code>
        <button class="btn-copy" onclick="navigator.clipboard.writeText(document.getElementById('kode-console').textContent)">Salin Perintah</button>
        @else
        <p style="color:#dc2626;">Gagal membuat token otomatis - silakan login manual pakai kredensial yang ada di halaman Server Ujian.</p>
        @endif
        <a href="{{ $baseUrl }}/adm#/" target="_blank" class="btn">Buka Server Ujian</a>
        <p style="margin-top:14px;"><a href="/server-ujian" style="color:#64748b;">&larr; Kembali</a></p>
    </div>
</div>

<script>
@if($token)
    // Coba buka tab baru ke exo, lalu tampilkan fallback (krn localStorage
    // cross-origin gak bisa ditulis langsung dari sini oleh JS kita).
    setTimeout(function () {
        window.open('{{ $baseUrl }}/adm#/', '_blank');
        document.getElementById('spinner').style.display = 'none';
        document.getElementById('judul').textContent = 'Server Ujian dibuka di tab baru';
        document.getElementById('sub').textContent = 'Kalau belum otomatis login, ikuti langkah cepat di bawah ini:';
        document.getElementById('fallback').style.display = 'block';
    }, 800);
@else
    document.getElementById('spinner').style.display = 'none';
    document.getElementById('judul').textContent = 'Gagal menghubungi Server Ujian';
    document.getElementById('sub').textContent = 'Coba lagi, atau login manual.';
    document.getElementById('fallback').style.display = 'block';
@endif
</script>
</body>
</html>
