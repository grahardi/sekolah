@extends('layouts.sarpras')
@section('title', 'Scan QR Barang')
@section('page-title', 'Scan QR Barang')

@section('content')

@if(session('error'))
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('error') }}</div>
@endif

<div class="card" style="padding:20px;max-width:480px;">
    <p style="font-size:13px;color:#64748b;margin:0 0 16px;">
        Arahkan kamera ke QR code pada label barang untuk langsung buka detail barangnya.
    </p>

    <div id="reader" style="width:100%;border-radius:12px;overflow:hidden;"></div>

    <p style="text-align:center;font-size:11px;color:#94a3b8;margin:14px 0;">— atau masukkan kode manual —</p>

    <form action="{{ route('sarpras.scan.cari') }}" method="POST" style="display:flex;gap:8px;">
        @csrf
        <input type="text" name="kode" class="form-input" placeholder="Kode Barang" required>
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
const scanner = new Html5Qrcode("reader");
Html5Qrcode.getCameras().then(cameras => {
    if (cameras && cameras.length) {
        scanner.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 220 },
            (kode) => {
                scanner.stop();
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('sarpras.scan.cari') }}";
                form.innerHTML = '@csrf' + '<input type="hidden" name="kode" value="' + kode.replace(/"/g, '&quot;') + '">';
                document.body.appendChild(form);
                form.submit();
            },
            () => {}
        );
    }
}).catch(() => {
    document.getElementById('reader').innerHTML = '<p style="font-size:12px;color:#94a3b8;text-align:center;padding:20px;">Kamera tidak tersedia/diizinkan. Pakai input manual di bawah.</p>';
});
</script>

@endsection
