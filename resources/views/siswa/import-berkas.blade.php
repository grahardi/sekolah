@extends('layouts.app')
@section('title', 'Import Berkas')
@section('page-title', 'Import Berkas Massal')

@section('header-actions')
    <a href="{{ route('siswa.import.form') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div style="max-width:560px;margin:0 auto;display:flex;flex-direction:column;gap:16px;">

    <div class="card">
        <div style="text-align:center;padding:32px 28px 20px;">
            <div style="width:56px;height:56px;background:#eff6ff;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <i class="ti ti-folder-plus" style="font-size:28px;color:#2563EB;"></i>
            </div>
            <h2 style="font-size:17px;font-weight:800;color:#0f172a;margin:0 0 6px;">Import Berkas Massal</h2>
            <p style="font-size:13px;color:#64748b;margin:0;">Upload banyak file sekaligus - dicocokkan otomatis ke siswa lewat nama file</p>
        </div>

        <div style="padding:0 28px 28px;">
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px;margin-bottom:20px;display:flex;gap:10px;">
                <i class="ti ti-info-circle" style="font-size:20px;color:#2563EB;flex-shrink:0;margin-top:1px;"></i>
                <div>
                    <p style="font-size:12px;color:#1e40af;margin:0;">
                        Nama tiap file harus <strong>NIS atau NISN siswa</strong>, tanpa embel-embel lain.
                        Contoh: file <code>15700.jpg</code> akan otomatis masuk ke siswa dengan
                        NIS/NISN <strong>15700</strong>. File dengan nama yang tidak cocok siswa manapun
                        akan dilaporkan di akhir, tidak menghentikan proses lainnya.
                    </p>
                </div>
            </div>

            @if($errors->any())
            <div class="alert alert-error" style="margin-bottom:16px;">
                <i class="ti ti-circle-x"></i>
                <div><ul style="margin:0;padding-left:16px;font-size:12px;">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul></div>
            </div>
            @endif

            <form action="{{ route('siswa.import.berkas.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom:16px;">
                    <label class="form-label">Jenis Berkas <span style="color:#ef4444">*</span></label>
                    <select name="jenis" class="form-input" required>
                        @foreach($jenisBerkas as $key => $label)
                        <option value="{{ $key }}" {{ old('jenis') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <p style="font-size:11px;color:#94a3b8;margin-top:4px;">
                        Semua file yang di-upload dalam satu proses ini dianggap jenis yang sama.
                        Kalau ada beberapa jenis berkas berbeda, lakukan import terpisah per jenis.
                    </p>
                </div>

                <div style="margin-bottom:18px;">
                    <label class="form-label">File-file (bisa pilih banyak sekaligus)</label>
                    <label style="display:flex;flex-direction:column;align-items:center;justify-content:center;border:2px dashed #d1d5db;border-radius:10px;padding:28px;text-align:center;cursor:pointer;background:#f9fafb;">
                        <i class="ti ti-cloud-upload" style="font-size:36px;color:#94a3b8;margin-bottom:8px;"></i>
                        <p style="font-size:13px;font-weight:600;color:#374151;margin:0 0 4px;" id="file-label-berkas">Klik untuk pilih file (bisa banyak)</p>
                        <p style="font-size:12px;color:#94a3b8;margin:0;">Format: jpg, png, atau pdf - maks 5MB per file</p>
                        <input type="file" name="files[]" accept=".jpg,.jpeg,.png,.pdf" multiple required style="display:none;"
                               onchange="document.getElementById('file-label-berkas').textContent = this.files.length + ' file dipilih'">
                    </label>
                    @error('files')<p style="color:#ef4444;font-size:12px;margin-top:6px;">{{ $message }}</p>@enderror
                    @error('files.*')<p style="color:#ef4444;font-size:12px;margin-top:6px;">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;"><i class="ti ti-upload"></i> Import Berkas</button>
            </form>
        </div>
    </div>

</div>
@endsection
