@extends('layouts.alumni')
@section('title', 'Import Berkas Alumni')
@section('page-title', 'Import Berkas Alumni Massal')

@section('header-actions')
    <a href="{{ route('alumni.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if($errors->any())
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;max-width:600px;">
    @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
</div>
@endif

<div class="card" style="padding:20px;max-width:600px;">
    <p style="font-size:13px;color:#64748b;margin:0 0 16px;">
        Upload banyak file sekaligus untuk 1 jenis berkas. Nama file harus persis <strong>NIS atau NISN</strong> alumni (tanpa spasi/teks lain), mis. <code>0123456789.pdf</code>.
        Sistem HANYA mencocokkan ke siswa berstatus <strong>Lulus</strong> — tidak akan pernah menimpa berkas siswa aktif.
    </p>

    <form action="{{ route('alumni.import-berkas.process') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label class="form-label">Jenis Berkas</label>
        <select name="jenis" class="form-input" required style="margin-bottom:14px;">
            <option value="">-- Pilih Jenis Berkas --</option>
            <option value="ijazah">Ijazah SMP</option>
            <option value="sertifikat_tka">Sertifikat TKA</option>
            <option value="transkrip_nilai">Transkrip Nilai</option>
        </select>

        <div style="border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:14px;">
            <label class="form-label">Opsi A — Upload File ZIP (disarankan kalau file banyak)</label>
            <input type="file" name="zip_file" accept=".zip" class="form-input">
            <p style="font-size:11px;color:#94a3b8;margin:6px 0 0;">ZIP berisi banyak file (nama tetap NIS/NISN.pdf), sistem akan extract otomatis. Maks 50MB.</p>
        </div>

        <p style="text-align:center;font-size:11px;color:#94a3b8;margin:0 0 14px;">— atau —</p>

        <div style="border:1px solid #e2e8f0;border-radius:8px;padding:14px;margin-bottom:16px;">
            <label class="form-label">Opsi B — Pilih File Satuan (bisa banyak sekaligus)</label>
            <input type="file" name="files[]" multiple accept=".jpg,.jpeg,.png,.pdf" class="form-input">
        </div>

        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-upload"></i> Upload Sekarang</button>
    </form>
</div>

@endsection
