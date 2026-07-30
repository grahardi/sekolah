@extends('layouts.app')
@section('title', 'Import Data Siswa')
@section('page-title', 'Import Data Siswa')

@section('header-actions')
    <a href="{{ route('siswa.import.berkas.form') }}" class="btn btn-secondary"><i class="ti ti-folder-plus"></i> Import Berkas</a>
    <a href="{{ route('siswa.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div style="max-width:560px;margin:0 auto;display:flex;flex-direction:column;gap:16px;">

    <div class="card">
        <div style="text-align:center;padding:32px 28px 20px;">
            <div style="width:56px;height:56px;background:#eff6ff;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <i class="ti ti-school" style="font-size:28px;color:#2563EB;"></i>
            </div>
            <h2 style="font-size:17px;font-weight:800;color:#0f172a;margin:0 0 6px;">Import Langsung dari Dapodik</h2>
            <p style="font-size:13px;color:#64748b;margin:0;">Upload file "Daftar Peserta Didik" asli hasil unduhan Dapodik, tanpa perlu diubah dulu</p>
        </div>

        <div style="padding:0 28px 28px;">
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:14px;margin-bottom:20px;display:flex;gap:10px;">
                <i class="ti ti-info-circle" style="font-size:20px;color:#2563EB;flex-shrink:0;margin-top:1px;"></i>
                <div>
                    <p style="font-size:12px;color:#1e40af;margin:0;">
                        Ambil file dari Dapodik: menu <strong>Peserta Didik</strong> &rarr; tombol <strong>Unduh</strong>.
                        File-nya bernama <code>daftar_pd-NAMA_SEKOLAH-tanggal.xlsx</code> - upload persis seperti itu,
                        tidak perlu diedit kolomnya.
                    </p>
                </div>
            </div>

            <form action="{{ route('siswa.import.dapodik') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom:18px;">
                    <label class="form-label">File Dapodik (daftar_pd-....xlsx)</label>
                    <label style="display:flex;flex-direction:column;align-items:center;justify-content:center;border:2px dashed #93c5fd;border-radius:10px;padding:28px;text-align:center;cursor:pointer;background:#f8fbff;">
                        <i class="ti ti-cloud-upload" style="font-size:36px;color:#60a5fa;margin-bottom:8px;"></i>
                        <p style="font-size:13px;font-weight:600;color:#374151;margin:0 0 4px;" id="file-label-dapodik">Klik untuk pilih file Dapodik</p>
                        <p style="font-size:12px;color:#94a3b8;margin:0;">atau drag &amp; drop di sini</p>
                        <input type="file" name="file_dapodik" accept=".xlsx,.xls" required style="display:none;"
                               onchange="document.getElementById('file-label-dapodik').textContent = this.files[0]?.name ?? 'Klik untuk pilih file Dapodik'">
                    </label>
                    @error('file_dapodik')<p style="color:#ef4444;font-size:12px;margin-top:6px;">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;"><i class="ti ti-upload"></i> Import dari Dapodik</button>
            </form>

            <p style="font-size:11px;color:#94a3b8;margin:14px 0 0;">
                Data yang tidak ada di ekspor Dapodik (tahun masuk, status) otomatis diisi nilai
                wajar (tahun sekarang, status "aktif") dan bisa diedit manual per siswa nanti.
            </p>
        </div>
    </div>

    <div class="card">
        <div style="text-align:center;padding:32px 28px 20px;">
            <div style="width:56px;height:56px;background:#eff6ff;border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;">
                <i class="ti ti-file-import" style="font-size:28px;color:#1d4ed8;"></i>
            </div>
            <h2 style="font-size:17px;font-weight:800;color:#0f172a;margin:0 0 6px;">Import dari Template Excel</h2>
            <p style="font-size:13px;color:#64748b;margin:0;">Kalau datamu bukan dari Dapodik, gunakan template kolom kita sendiri</p>
        </div>

        <div style="padding:0 28px 28px;">
            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px;margin-bottom:20px;display:flex;gap:10px;">
                <i class="ti ti-alert-triangle" style="font-size:20px;color:#d97706;flex-shrink:0;margin-top:1px;"></i>
                <div>
                    <p style="font-size:13px;font-weight:700;color:#92400e;margin:0 0 4px;">Gunakan template resmi</p>
                    <p style="font-size:12px;color:#b45309;margin:0 0 8px;">Pastikan format kolom sesuai agar data terimport dengan benar.</p>
                    <a href="{{ route('siswa.import.template') }}" style="font-size:12px;font-weight:700;color:#92400e;display:inline-flex;align-items:center;gap:4px;text-decoration:underline;">
                        <i class="ti ti-download" style="font-size:14px;"></i> Download Template Excel
                    </a>
                </div>
            </div>

            <form action="{{ route('siswa.import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="margin-bottom:18px;">
                    <label class="form-label">File Excel (.xlsx / .xls / .csv)</label>
                    <label style="display:flex;flex-direction:column;align-items:center;justify-content:center;border:2px dashed #d1d5db;border-radius:10px;padding:28px;text-align:center;cursor:pointer;background:#f9fafb;">
                        <i class="ti ti-cloud-upload" style="font-size:36px;color:#94a3b8;margin-bottom:8px;"></i>
                        <p style="font-size:13px;font-weight:600;color:#374151;margin:0 0 4px;" id="file-label">Klik untuk pilih file</p>
                        <p style="font-size:12px;color:#94a3b8;margin:0;">atau drag &amp; drop di sini</p>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required style="display:none;"
                               onchange="document.getElementById('file-label').textContent = this.files[0]?.name ?? 'Klik untuk pilih file'">
                    </label>
                    @error('file')<p style="color:#ef4444;font-size:12px;margin-top:6px;">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;"><i class="ti ti-upload"></i> Import Sekarang</button>
            </form>

            <div style="margin-top:20px;padding-top:20px;border-top:1px solid #f1f5f9;">
                <p style="font-size:12px;font-weight:700;color:#374151;margin:0 0 10px;">Kolom yang dibutuhkan:</p>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px;">
                    @foreach(['nisn','nis','nama_lengkap','jenis_kelamin (L/P)','tempat_lahir','tanggal_lahir (dd/mm/yyyy)','agama','alamat','rt','rw','dusun','kelurahan','kecamatan','kode_pos','kelas','rombel','tahun_masuk','status'] as $col)
                    <div style="display:flex;align-items:center;gap:6px;font-size:12px;color:#374151;padding:3px 0;">
                        <i class="ti ti-point-filled" style="font-size:10px;color:#22c55e;"></i>{{ $col }}
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if(session('import_imported') !== null)
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:16px 18px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <i class="ti ti-circle-check" style="font-size:22px;color:#16a34a;flex-shrink:0;"></i>
            <p style="font-size:14px;font-weight:700;color:#166534;margin:0;">{{ session('import_imported') }} siswa berhasil diimport</p>
        </div>
        @if(session('import_warnings') && count(session('import_warnings')) > 0)
        <div style="margin-top:12px;padding-top:12px;border-top:1px solid #bbf7d0;">
            <p style="font-size:12px;font-weight:700;color:#92400e;margin:0 0 6px;">Peringatan:</p>
            <ul style="margin:0;padding-left:18px;">@foreach(session('import_warnings') as $w)<li style="font-size:12px;color:#92400e;margin-bottom:3px;">{{ $w }}</li>@endforeach</ul>
        </div>
        @endif
    </div>
    @endif

    @if(session('import_error_count') && session('import_error_count') > 0)
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:16px 18px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:10px;">
            <i class="ti ti-circle-x" style="font-size:22px;color:#dc2626;flex-shrink:0;"></i>
            <p style="font-size:14px;font-weight:700;color:#991b1b;margin:0;">{{ session('import_error_count') }} baris gagal</p>
        </div>
        <a href="{{ route('siswa.import.errors', session('import_error_token')) }}" class="btn btn-danger btn-sm">
            <i class="ti ti-list-details"></i> Tampilkan Baris Error
        </a>
    </div>
    @endif

</div>
@endsection
