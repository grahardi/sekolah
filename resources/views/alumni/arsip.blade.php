@extends('layouts.alumni')
@section('title', 'Berkas Alumni — ' . $siswa->nama_lengkap)
@section('page-title', 'Berkas Alumni')

@section('header-actions')
    <a href="{{ route('alumni.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')

<p style="font-size:14px;font-weight:700;color:#0f172a;margin:-6px 0 16px;">{{ $siswa->nama_lengkap }} &middot; NISN {{ $siswa->nisn }} &middot; Lulus {{ $siswa->tahun_lulus ?: '-' }}</p>

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if($errors->any())
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
    @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
</div>
@endif

<div class="card" style="padding:18px;margin-bottom:20px;">
    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 12px;"><i class="ti ti-id-badge-2" style="color:#1d4ed8;"></i> Data Pokok</p>
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;font-size:13px;">
        <div><span style="color:#94a3b8;display:block;font-size:11px;">NIS</span>{{ $siswa->nis ?: '-' }}</div>
        <div><span style="color:#94a3b8;display:block;font-size:11px;">NISN</span>{{ $siswa->nisn ?: '-' }}</div>
        <div><span style="color:#94a3b8;display:block;font-size:11px;">Jenis Kelamin</span>{{ $siswa->jenis_kelamin_lengkap }}</div>
        <div><span style="color:#94a3b8;display:block;font-size:11px;">Tempat, Tanggal Lahir</span>{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir?->format('d-m-Y') ?: '-' }}</div>
        <div><span style="color:#94a3b8;display:block;font-size:11px;">Agama</span>{{ $siswa->agama ?: '-' }}</div>
        <div><span style="color:#94a3b8;display:block;font-size:11px;">Kelas Terakhir</span>{{ $siswa->kelas }}{{ $siswa->rombel ? " - $siswa->rombel" : '' }}</div>
        <div style="grid-column:span 3;"><span style="color:#94a3b8;display:block;font-size:11px;">Alamat</span>{{ $siswa->alamat ?: '-' }}{{ $siswa->kecamatan ? ", {$siswa->kecamatan}" : '' }}</div>
        <div><span style="color:#94a3b8;display:block;font-size:11px;">Nama Ayah</span>{{ $siswa->nama_ayah ?: '-' }}</div>
        <div><span style="color:#94a3b8;display:block;font-size:11px;">Nama Ibu</span>{{ $siswa->nama_ibu ?: '-' }}</div>
        <div><span style="color:#94a3b8;display:block;font-size:11px;">No. Telepon</span>{{ $siswa->no_telepon ?: '-' }}</div>
        <div><span style="color:#94a3b8;display:block;font-size:11px;">Tahun Masuk</span>{{ $siswa->tahun_masuk ?: '-' }}</div>
        <div><span style="color:#94a3b8;display:block;font-size:11px;">Tahun Lulus</span>{{ $siswa->tahun_lulus ?: '-' }}</div>
        <div><span style="color:#94a3b8;display:block;font-size:11px;">No. Ijazah</span>{{ $siswa->no_ijazah ?: '-' }}</div>
    </div>
</div>

<div class="card" style="padding:18px;margin-bottom:20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;"><i class="ti ti-timeline" style="color:#16a34a;"></i> Riwayat Alumni</p>
        <a href="{{ route('alumni.history.edit.form', $siswa) }}" style="font-size:11px;color:#16a34a;text-decoration:none;">Edit Riwayat &rarr;</a>
    </div>
    <div style="position:relative;padding-left:20px;">
        <div style="position:absolute;left:5px;top:6px;bottom:6px;width:2px;background:#e2e8f0;"></div>

        <div style="position:relative;margin-bottom:16px;">
            <div style="position:absolute;left:-20px;top:2px;width:12px;height:12px;border-radius:50%;background:#16a34a;border:2px solid #fff;box-shadow:0 0 0 2px #16a34a;"></div>
            <p style="font-size:13px;font-weight:600;color:#0f172a;margin:0;">{{ $siswa->nama_lengkap }} &mdash; Status Lulus{{ $siswa->tahun_lulus ? ", Tahun {$siswa->tahun_lulus}" : '' }}</p>
            <p style="font-size:11px;color:#94a3b8;margin:2px 0 0;">Kelas terakhir {{ $siswa->kelas }}{{ $siswa->rombel ? " - $siswa->rombel" : '' }}</p>
        </div>

        @forelse($siswa->alumniAjuanUlang as $riwayat)
        <div style="position:relative;margin-bottom:16px;">
            <div style="position:absolute;left:-20px;top:2px;width:12px;height:12px;border-radius:50%;background:{{ $riwayat->status === 'disetujui' ? '#16a34a' : ($riwayat->status === 'ditolak' ? '#dc2626' : '#d97706') }};border:2px solid #fff;box-shadow:0 0 0 2px {{ $riwayat->status === 'disetujui' ? '#16a34a' : ($riwayat->status === 'ditolak' ? '#dc2626' : '#d97706') }};"></div>
            <p style="font-size:13px;font-weight:600;color:#0f172a;margin:0;">{{ $riwayat->labelTujuan() }}{{ $riwayat->alumni_jurusan ? " - {$riwayat->alumni_jurusan}" : '' }}</p>
            <p style="font-size:11px;color:#94a3b8;margin:2px 0 0;">
                Diajukan {{ $riwayat->created_at->locale('id')->diffForHumans() }}
                @if($riwayat->status === 'menunggu')<span style="color:#d97706;font-weight:600;"> &middot; Menunggu Persetujuan</span>
                @elseif($riwayat->status === 'ditolak')<span style="color:#dc2626;font-weight:600;"> &middot; Ditolak</span>
                @else<span style="color:#16a34a;font-weight:600;"> &middot; Disetujui</span>@endif
            </p>
        </div>
        @empty
            @if($siswa->alumni_diisi_at)
            <div style="position:relative;">
                <div style="position:absolute;left:-20px;top:2px;width:12px;height:12px;border-radius:50%;background:#16a34a;border:2px solid #fff;box-shadow:0 0 0 2px #16a34a;"></div>
                <p style="font-size:13px;font-weight:600;color:#0f172a;margin:0;">{{ $siswa->alumni_label }}</p>
                <p style="font-size:11px;color:#94a3b8;margin:2px 0 0;">Diisi {{ $siswa->alumni_diisi_at->locale('id')->diffForHumans() }}</p>
            </div>
            @else
            <p style="font-size:12px;color:#94a3b8;margin:0;">Alumni belum pernah mengisi data lanjutan.</p>
            @endif
        @endforelse
    </div>
</div>

<form action="{{ route('alumni.arsip.update', $siswa) }}" method="POST" enctype="multipart/form-data" id="form-arsip">
    @csrf

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <div class="card">
            <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-folder" style="font-size:16px;vertical-align:-3px;margin-right:6px;color:#1d4ed8;"></i> Berkas Masuk</span></div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:8px;">
                @foreach(\App\Models\ArsipBerkas::berkasAktif() as $field => $meta)
                    @include('siswa._arsip-item', ['field' => $field, 'meta' => $meta, 'arsip' => $arsip, 'routeHapus' => 'alumni.arsip.hapus'])
                @endforeach
            </div>
        </div>
        <div class="card">
            <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-folder-check" style="font-size:16px;vertical-align:-3px;margin-right:6px;color:#16a34a;"></i> Berkas Kelulusan (Ijazah, TKA, Transkrip)</span></div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:8px;">
                @foreach(\App\Models\ArsipBerkas::berkasLulus() as $field => $meta)
                    @include('siswa._arsip-item', ['field' => $field, 'meta' => $meta, 'arsip' => $arsip, 'routeHapus' => 'alumni.arsip.hapus'])
                @endforeach
            </div>
        </div>
    </div>

    <div class="card" style="margin-top:20px;">
        <div class="card-body">
            <label class="form-label">Catatan</label>
            <textarea name="catatan" rows="2" class="form-input" placeholder="Catatan tambahan...">{{ $arsip->catatan }}</textarea>
        </div>
    </div>

    <div style="display:flex;justify-content:flex-end;margin-top:16px;">
        <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Simpan Berkas</button>
    </div>
</form>

<form action="{{ route('alumni.arsip.hapus', $siswa) }}" method="POST" id="form-hapus-berkas" style="display:none;">
    @csrf
    <input type="hidden" name="field" id="hapus-field-input">
</form>

@endsection

@push('scripts')
<script>
function showFileName(input, labelId) {
    const label = document.getElementById(labelId);
    if (label) label.textContent = input.files[0]?.name ?? '';
}
function hapusBerkas(e, el, field, label) {
    e.preventDefault();
    if (!confirm('Hapus berkas "' + label + '"?')) return;
    document.getElementById('hapus-field-input').value = field;
    document.getElementById('form-hapus-berkas').submit();
}
</script>
@endpush
