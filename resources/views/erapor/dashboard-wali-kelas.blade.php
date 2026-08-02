@extends('layouts.erapor')
@section('title', 'Dashboard Wali Kelas')
@section('page-title', 'Dashboard Wali Kelas')

@section('content')
<div style="background:linear-gradient(135deg,#1E3A5F,#2563EB);border-radius:14px;padding:20px 24px;margin-bottom:20px;color:#fff;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
    <img src="{{ $waliKelas->guru->foto_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($waliKelas->guru->nama) . '&background=fff&color=1E3A5F' }}" style="width:56px;height:56px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.4);">
    <div>
        <p style="font-size:12px;opacity:.8;margin:0;">Selamat datang,</p>
        <p style="font-size:18px;font-weight:800;margin:0 0 4px;">{{ $waliKelas->guru->nama }}</p>
        <p style="font-size:12px;opacity:.85;margin:0;">
            @if($tahunAktif){{ $tahunAktif->nama }} &middot; Semester {{ $tahunAktif->semester }}@endif
            &middot; Wali Kelas <strong>{{ $kelas }}{{ $rombel ? " - $rombel" : '' }}</strong>
        </p>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:20px;">
    <div style="background:#eff6ff;border-radius:14px;padding:18px;">
        <p style="font-size:28px;font-weight:800;color:#1e40af;margin:0;">{{ $totalSiswa }}</p>
        <p style="font-size:12px;color:#64748b;margin:2px 0 0;">Total Siswa</p>
    </div>
    <div style="background:#fef2f2;border-radius:14px;padding:18px;">
        <p style="font-size:28px;font-weight:800;color:#dc2626;margin:0;">{{ $absensiTinggi }}</p>
        <p style="font-size:12px;color:#64748b;margin:2px 0 0;">Absensi Tinggi (&gt;10 hari)</p>
    </div>
    <div style="background:#fffbeb;border-radius:14px;padding:18px;">
        <p style="font-size:28px;font-weight:800;color:#d97706;margin:0;">{{ $belumLengkap }}</p>
        <p style="font-size:12px;color:#64748b;margin:2px 0 0;">Data Belum Lengkap</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1.4fr;gap:16px;margin-bottom:20px;">
    <div class="card" style="padding:22px;text-align:center;">
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 14px;">Progres Finalisasi Rapor</p>
        <div style="position:relative;width:140px;height:140px;margin:0 auto;">
            <svg viewBox="0 0 36 36" style="width:100%;height:100%;transform:rotate(-90deg);">
                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#f1f5f9" stroke-width="3"></path>
                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#1E3A5F" stroke-width="3" stroke-dasharray="{{ $progresFinalisasi }}, 100"></path>
            </svg>
            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:800;color:#1E3A5F;">{{ $progresFinalisasi }}%</div>
        </div>
        <p style="font-size:12px;color:#64748b;margin:12px 0 0;">{{ $sudahFinal }} dari {{ $totalSiswa }} rapor difinalisasi</p>
    </div>

    <div class="card" style="padding:22px;">
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 14px;">Tugas Wali Kelas</p>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f1f5f9;">
                <span style="font-size:13px;color:#374151;">Input Absensi &amp; Catatan Rapor</span>
                <a href="{{ route('erapor.catatan-wali.index') }}" class="btn btn-secondary btn-sm">Buka</a>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f1f5f9;">
                <span style="font-size:13px;color:#374151;">Catatan UTS/PTS</span>
                <a href="{{ route('erapor.catatan-uts.index') }}" class="btn btn-secondary btn-sm">Buka</a>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f1f5f9;">
                <span style="font-size:13px;color:#374151;">Cek Kelengkapan Nilai</span>
                <a href="{{ route('erapor.progres-penilaian') }}" class="btn btn-secondary btn-sm">Lihat Progres</a>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;border-bottom:1px solid #f1f5f9;">
                <span style="font-size:13px;color:#374151;">Proses Nilai Akhir &amp; Deskripsi</span>
                <a href="{{ route('erapor.rapor.cetak-kelas') }}" class="btn btn-primary btn-sm">Proses Sekarang</a>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0;">
                <span style="font-size:13px;color:#374151;">Finalisasi &amp; Cetak Rapor</span>
                <a href="{{ route('erapor.rapor.cetak-kelas') }}" class="btn btn-sm" style="background:#16a34a;color:#fff;">Cetak Rapor</a>
            </div>
        </div>
    </div>
</div>

@if($guruMengajar->count() > 0)
<div class="card" style="padding:20px;">
    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 14px;">Tugas Mengajar Kamu</p>
    <div style="display:flex;flex-direction:column;gap:8px;">
        @foreach($guruMengajar as $kelasRombel => $penugasans)
        @php [$kl, $rb] = explode('|', $kelasRombel); @endphp
        @foreach($penugasans as $p)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#f8fafc;border-radius:8px;">
            <span style="font-size:13px;color:#374151;"><strong>{{ $p->mataPelajaran->nama }}</strong> &middot; Kelas {{ $rb ? "$kl - $rb" : $kl }}</span>
            <div style="display:flex;gap:6px;">
                <a href="{{ route('erapor.tp.index', ['mapel_id' => $p->mata_pelajaran_id]) }}" class="btn btn-secondary btn-sm">Kelola TP</a>
                <a href="{{ route('erapor.penilaian.index', ['mata_pelajaran_id' => $p->mata_pelajaran_id, 'kelas_rombel' => $kelasRombel]) }}" class="btn btn-primary btn-sm">Input Nilai</a>
            </div>
        </div>
        @endforeach
        @endforeach
    </div>
</div>
@endif
@endsection
