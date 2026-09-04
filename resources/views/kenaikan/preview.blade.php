@extends('layouts.app')
@section('title', 'Preview Kenaikan Kelas')
@section('page-title', 'Preview — Konfirmasi Proses')

@section('header-actions')
    <a href="{{ route('kenaikan.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
@php
    $aksiInfo = [
        'naik'    => ['label'=>'Naik Kelas',    'color'=>'#1d4ed8','bg'=>'#eff6ff','icon'=>'ti-arrow-up-circle'],
        'lulus'   => ['label'=>'Lulus',          'color'=>'#16a34a','bg'=>'#f0fdf4','icon'=>'ti-certificate'],
        'tinggal' => ['label'=>'Tinggal Kelas',  'color'=>'#d97706','bg'=>'#fffbeb','icon'=>'ti-refresh'],
        'mutasi'            => ['label'=>'Mutasi',            'color'=>'#7c3aed','bg'=>'#f5f3ff','icon'=>'ti-transfer'],
        'mengundurkan_diri' => ['label'=>'Mengundurkan Diri', 'color'=>'#dc2626','bg'=>'#fef2f2','icon'=>'ti-hand-stop'],
        'wafat'             => ['label'=>'Wafat',             'color'=>'#475569','bg'=>'#f8fafc','icon'=>'ti-flower'],
        'hilang'            => ['label'=>'Hilang',            'color'=>'#b45309','bg'=>'#fffbeb','icon'=>'ti-help-circle'],
        'lainnya'           => ['label'=>'Keluar (Lainnya)',  'color'=>'#64748b','bg'=>'#f8fafc','icon'=>'ti-dots'],
    ];
    $info = $aksiInfo[$aksi] ?? $aksiInfo['naik'];
@endphp

<div style="background:{{ $info['bg'] }};border:1px solid {{ $info['color'] }}33;border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:14px;">
    <i class="ti {{ $info['icon'] }}" style="font-size:28px;color:{{ $info['color'] }};flex-shrink:0;"></i>
    <div style="flex:1;">
        <p style="font-size:15px;font-weight:800;color:{{ $info['color'] }};margin:0 0 4px;">
            {{ $info['label'] }} — Kelas {{ $kelas }}
            @if($aksi === 'naik' && $kelas_tujuan) → Kelas {{ $kelas_tujuan }} @endif
        </p>
        <p style="font-size:12px;color:#64748b;margin:0;">
            Tahun Ajaran: <strong>{{ $tahun_ajaran }}</strong>
            @if($wali_kelas) &nbsp;·&nbsp; Wali Kelas: <strong>{{ $wali_kelas }}</strong> @endif
            &nbsp;·&nbsp; <strong>{{ $siswas->count() }}</strong> siswa aktif
        </p>
    </div>
</div>

<form action="{{ route('kenaikan.proses') }}" method="POST" id="form-proses">
    @csrf
    <input type="hidden" name="aksi"         value="{{ $aksi }}">
    <input type="hidden" name="kelas_asal"   value="{{ $kelas }}">
    <input type="hidden" name="tahun_ajaran" value="{{ $tahun_ajaran }}">
    <input type="hidden" name="kelas_tujuan" value="{{ $kelas_tujuan }}">
    <input type="hidden" name="wali_kelas"   value="{{ $wali_kelas }}">

    <div class="card">
        <div class="card-header">
            <div style="display:flex;align-items:center;gap:10px;">
                <input type="checkbox" id="check-all" style="width:16px;height:16px;cursor:pointer;" onchange="toggleAll(this.checked)" checked>
                <label for="check-all" style="font-size:13px;font-weight:700;color:#0f172a;cursor:pointer;margin:0;">
                    Pilih Semua &nbsp;<span style="color:#94a3b8;font-weight:400;">(<span id="count-selected">{{ $siswas->count() }}</span> / {{ $siswas->count() }} dipilih)</span>
                </label>
            </div>
            <div style="display:flex;gap:6px;">
                <button type="button" onclick="filterJK('L')" style="font-size:11px;padding:4px 10px;border-radius:6px;border:1px solid #d1d5db;background:#fff;cursor:pointer;font-weight:600;color:#1d4ed8;">Laki-laki</button>
                <button type="button" onclick="filterJK('P')" style="font-size:11px;padding:4px 10px;border-radius:6px;border:1px solid #d1d5db;background:#fff;cursor:pointer;font-weight:600;color:#be185d;">Perempuan</button>
                <button type="button" onclick="toggleAll(true)" style="font-size:11px;padding:4px 10px;border-radius:6px;border:1px solid #d1d5db;background:#fff;cursor:pointer;font-weight:600;color:#374151;">Semua</button>
            </div>
        </div>

        @if($siswas->isEmpty())
            <div style="padding:48px;text-align:center;color:#94a3b8;">
                <i class="ti ti-users" style="font-size:48px;display:block;margin-bottom:12px;color:#e2e8f0;"></i>
                <p style="font-size:14px;font-weight:600;color:#374151;">Tidak ada siswa aktif di kelas {{ $kelas }}</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="padding:10px 14px;width:44px;"></th>
                            <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">No</th>
                            <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Nama Siswa</th>
                            <th style="padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">NISN</th>
                            <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">L/P</th>
                            <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Rombel Sekarang</th>
                            <th style="padding:10px 14px;text-align:center;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">→ Rombel Baru</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswas as $i => $s)
                        <tr style="border-top:1px solid #f1f5f9;" data-jk="{{ $s->jenis_kelamin }}">
                            <td style="padding:10px 14px;text-align:center;">
                                <input type="checkbox" name="siswa_ids[]" value="{{ $s->id }}" class="cb-siswa" style="width:16px;height:16px;cursor:pointer;" checked onchange="updateCount()">
                            </td>
                            <td style="padding:10px 14px;font-size:12px;color:#94a3b8;">{{ $i + 1 }}</td>
                            <td style="padding:10px 14px;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="width:30px;height:30px;border-radius:8px;overflow:hidden;background:#dbeafe;flex-shrink:0;">
                                        <img src="{{ $s->foto_url }}" style="width:100%;height:100%;object-fit:cover;" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($s->nama_lengkap) }}&background=dbeafe&color=1d4ed8&size=30'">
                                    </div>
                                    <span style="font-size:13px;font-weight:600;color:#111827;">{{ $s->nama_lengkap }}</span>
                                </div>
                            </td>
                            <td style="padding:10px 14px;font-family:monospace;font-size:12px;color:#374151;">{{ $s->nisn }}</td>
                            <td style="padding:10px 14px;text-align:center;font-size:12px;font-weight:700;color:{{ $s->jenis_kelamin === 'L' ? '#1d4ed8' : '#be185d' }};">{{ $s->jenis_kelamin }}</td>
                            <td style="padding:10px 14px;text-align:center;"><span style="background:#f1f5f9;color:#374151;padding:3px 10px;border-radius:6px;font-size:12px;font-weight:700;">{{ $s->rombel_lengkap }}</span></td>
                            <td style="padding:10px 14px;text-align:center;"><span style="background:{{ $info['bg'] }};color:{{ $info['color'] }};padding:3px 12px;border-radius:6px;font-size:12px;font-weight:800;">{{ $rombelTujuan[$s->id] ?? '—' }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding:16px 20px;border-top:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
                <p style="font-size:13px;color:#64748b;margin:0;"><span id="count-selected-bottom">{{ $siswas->count() }}</span> siswa akan diproses</p>
                <div style="display:flex;gap:10px;">
                    <a href="{{ route('kenaikan.index') }}" class="btn btn-secondary"><i class="ti ti-x"></i> Batal</a>
                    <button type="submit" class="btn btn-primary" style="background:{{ $info['color'] }};" onclick="return konfirmasi()">
                        <i class="ti {{ $info['icon'] }}"></i> Proses {{ $info['label'] }} Sekarang
                    </button>
                </div>
            </div>
        @endif
    </div>
</form>

@push('scripts')
<script>
function toggleAll(checked) {
    document.querySelectorAll('.cb-siswa').forEach(cb => cb.checked = checked);
    updateCount();
}
function updateCount() {
    const n = document.querySelectorAll('.cb-siswa:checked').length;
    document.getElementById('count-selected').textContent = n;
    document.getElementById('count-selected-bottom').textContent = n;
    const total = document.querySelectorAll('.cb-siswa').length;
    document.getElementById('check-all').checked = n === total;
    document.getElementById('check-all').indeterminate = n > 0 && n < total;
}
function filterJK(jk) {
    document.querySelectorAll('[data-jk]').forEach(tr => { tr.querySelector('.cb-siswa').checked = tr.dataset.jk === jk; });
    updateCount();
}
function konfirmasi() {
    const n = document.querySelectorAll('.cb-siswa:checked').length;
    if (n === 0) { alert('Pilih minimal 1 siswa.'); return false; }
    return confirm('Proses ' + n + ' siswa? Tindakan ini tidak dapat dibatalkan.');
}
</script>
@endpush
@endsection
