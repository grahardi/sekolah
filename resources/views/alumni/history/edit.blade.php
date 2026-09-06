@extends('layouts.alumni')
@section('title', 'Edit Riwayat - ' . $siswa->nama_lengkap)
@section('page-title', 'Edit Riwayat Alumni')

@section('header-actions')
    <a href="{{ route('alumni.history.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali ke List</a>
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

<p style="font-size:14px;font-weight:700;color:#0f172a;margin:-6px 0 16px;">{{ $siswa->nama_lengkap }} &middot; NISN {{ $siswa->nisn }} &middot; Lulus {{ $siswa->tahun_lulus ?: '-' }}</p>

<div class="card" style="max-width:480px;padding:22px;">
    <form action="{{ route('alumni.history.edit', $siswa) }}" method="POST">
        @csrf @method('PUT')

        <label class="form-label">Kategori</label>
        <select name="alumni_kategori" id="kategori-select" class="form-input" required style="margin-bottom:12px;"
            onchange="
                document.getElementById('wrap-sekolah').style.display = this.value === 'lanjut_sekolah' ? 'block' : 'none';
                document.getElementById('wrap-pondok').style.display = this.value === 'pondok_pesantren' ? 'block' : 'none';
                document.getElementById('wrap-keterangan').style.display = ['bekerja','tidak_melanjutkan','lainnya'].includes(this.value) ? 'block' : 'none';
            ">
            <option value="lanjut_sekolah" {{ $siswa->alumni_kategori === 'lanjut_sekolah' ? 'selected' : '' }}>Lanjut Sekolah</option>
            <option value="pondok_pesantren" {{ $siswa->alumni_kategori === 'pondok_pesantren' ? 'selected' : '' }}>Pondok Pesantren</option>
            <option value="bekerja" {{ $siswa->alumni_kategori === 'bekerja' ? 'selected' : '' }}>Bekerja</option>
            <option value="tidak_melanjutkan" {{ $siswa->alumni_kategori === 'tidak_melanjutkan' ? 'selected' : '' }}>Tidak Melanjutkan</option>
            <option value="lainnya" {{ $siswa->alumni_kategori === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
        </select>

        <div id="wrap-sekolah" style="display:{{ $siswa->alumni_kategori === 'lanjut_sekolah' ? 'block' : 'none' }};">
            <label class="form-label">Sekolah Tujuan</label>
            <select name="alumni_sekolah_tujuan_id" class="form-input" style="margin-bottom:8px;">
                <option value="">-- Tidak ada di daftar --</option>
                @foreach($sekolahTujuanList as $st)
                <option value="{{ $st->id }}" {{ (int) $siswa->alumni_sekolah_tujuan_id === $st->id ? 'selected' : '' }}>{{ $st->nama_sekolah }}</option>
                @endforeach
            </select>
            <label class="form-label">Atau Nama Sekolah Manual</label>
            <input type="text" name="alumni_sekolah_tujuan_manual" value="{{ $siswa->alumni_kategori === 'lanjut_sekolah' ? $siswa->alumni_sekolah_tujuan_manual : '' }}" class="form-input" style="margin-bottom:8px;">
            <label class="form-label">Jurusan</label>
            <input type="text" name="alumni_jurusan" value="{{ $siswa->alumni_jurusan }}" class="form-input" style="margin-bottom:12px;">
        </div>

        <div id="wrap-pondok" style="display:{{ $siswa->alumni_kategori === 'pondok_pesantren' ? 'block' : 'none' }};">
            <label class="form-label">Nama Pondok Pesantren</label>
            <input type="text" name="alumni_pondok_manual" value="{{ $siswa->alumni_kategori === 'pondok_pesantren' ? $siswa->alumni_sekolah_tujuan_manual : '' }}" class="form-input" style="margin-bottom:12px;">
        </div>

        <div id="wrap-keterangan" style="display:{{ in_array($siswa->alumni_kategori, ['bekerja','tidak_melanjutkan','lainnya']) ? 'block' : 'none' }};">
            <label class="form-label">Keterangan</label>
            <input type="text" name="alumni_keterangan" value="{{ $siswa->alumni_keterangan }}" class="form-input" style="margin-bottom:12px;">
        </div>

        <div style="display:flex;gap:10px;margin-top:8px;">
            <a href="{{ route('alumni.history.index') }}" class="btn btn-secondary" style="flex:1;justify-content:center;">Batal</a>
            <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Simpan Perubahan</button>
        </div>
    </form>
</div>

@endsection
