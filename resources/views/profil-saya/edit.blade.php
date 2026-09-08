<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Profil Saya</title>
<link rel="stylesheet" href="{{ asset('vendor/tabler-icons/tabler-icons.min.css') }}">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; }
body { background:#F5F9FF; min-height:100vh; padding:24px; }
.wrap { max-width:560px; margin:0 auto; }
.card { background:#fff; border-radius:14px; border:1px solid #e9ecef; padding:24px; }
.form-label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:5px; }
.form-input { width:100%; border:1px solid #d1d5db; border-radius:8px; padding:9px 12px; font-size:13px; margin-bottom:14px; }
.btn { display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:600; padding:9px 18px; border-radius:8px; text-decoration:none; cursor:pointer; border:none; }
.btn-primary { background:#2563EB; color:#fff; }
.btn-secondary { background:#fff; color:#374151; border:1px solid #d1d5db; }
.alert { padding:11px 16px; border-radius:10px; font-size:13px; margin-bottom:18px; }
.alert-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
.alert-error { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; }
</style>
</head>
<body>
<div class="wrap">
    <a href="/dashboard" style="font-size:12px;color:#64748b;text-decoration:none;display:inline-block;margin-bottom:14px;">&larr; Kembali ke Portal</a>
    <h1 style="font-size:18px;font-weight:700;color:#0f172a;margin:0 0 4px;">Profil Saya</h1>
    <p style="font-size:13px;color:#64748b;margin:0 0 18px;">
        Ubah data pokok kamu di sini. Perubahan akan otomatis ikut terupdate di Kepegawaian dan E-Rapor.
    </p>

    @if(session('success'))
    <div class="alert alert-success"><i class="ti ti-circle-check"></i> {{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div class="alert alert-error">
        @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
    </div>
    @endif

    @if($sumber === 'user_saja')
    <div class="alert" style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;">
        <i class="ti ti-info-circle"></i> Akun kamu belum terhubung ke data Kepegawaian. Nama akan diubah untuk akun login saja.
    </div>
    @endif

    <div class="card">
        @if($data['foto_url'])
        <img src="{{ $data['foto_url'] }}" style="width:72px;height:90px;object-fit:cover;border-radius:8px;margin-bottom:16px;">
        @endif

        <form action="{{ route('profil-saya.update') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $data['nama_lengkap']) }}" class="form-input" required>

            <label class="form-label">NIP / NUPTK</label>
            <input type="text" name="nip_nuptk" value="{{ old('nip_nuptk', $data['nip_nuptk']) }}" class="form-input">

            @if($sumber === 'pegawai')
            <label class="form-label">NIK</label>
            <input type="text" name="nik" value="{{ old('nik', $data['nik']) }}" class="form-input">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <label class="form-label">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $data['tempat_lahir']) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $data['tanggal_lahir']) }}" class="form-input">
                </div>
            </div>

            <label class="form-label">No. HP</label>
            <input type="text" name="no_hp" value="{{ old('no_hp', $data['no_hp']) }}" class="form-input">

            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $data['email']) }}" class="form-input">

            <label class="form-label">Alamat</label>
            <textarea name="alamat" rows="2" class="form-input">{{ old('alamat', $data['alamat']) }}</textarea>

            <label class="form-label">Foto (opsional, ganti kalau perlu)</label>
            <input type="file" name="foto" accept="image/*" class="form-input" style="padding:6px;">
            @endif

            <div style="display:flex;gap:10px;margin-top:8px;">
                <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Simpan</button>
                <a href="/dashboard" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
