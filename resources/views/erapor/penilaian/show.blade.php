@extends('layouts.erapor')
@section('title', 'Input Nilai - ' . $penilaian->nama_penilaian)
@section('page-title', $penilaian->nama_penilaian)

@section('header-actions')
    <a href="{{ route('erapor.penilaian.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div style="background:linear-gradient(135deg,#1E3A5F,#2563EB);border-radius:14px;padding:18px 22px;margin-bottom:16px;color:#fff;">
    <div style="display:flex;gap:14px;flex-wrap:wrap;align-items:center;">
        <span class="badge" style="background:rgba(255,255,255,.15);color:#fff;">{{ $penilaian->subjenis_label }}</span>
        <span style="font-size:13px;opacity:.9;">{{ $penilaian->mataPelajaran->nama }} &middot; {{ $penilaian->kelas_lengkap }} &middot; Bobot {{ $penilaian->bobot_penilaian }}</span>
        @if($penilaian->tujuanPembelajarans->count() > 0)
        <span style="font-size:11px;opacity:.75;">TP: {{ $penilaian->tujuanPembelajarans->pluck('kode_tp')->filter()->implode(', ') ?: $penilaian->tujuanPembelajarans->count() . ' TP' }}</span>
        @endif
    </div>
</div>

<div class="card" style="padding:16px;margin-bottom:16px;">
    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 10px;">Import Nilai (opsional)</p>
    <p style="font-size:12px;color:#64748b;margin:0 0 10px;">Download template (sudah berisi nama siswa), isi kolom Nilai, lalu upload lagi - lebih cepat dari isi manual satu-satu di bawah.</p>
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <a href="{{ route('erapor.penilaian.template-nilai', $penilaian) }}" class="btn btn-secondary btn-sm"><i class="ti ti-download"></i> Download Template</a>
        <form action="{{ route('erapor.penilaian.import-nilai', $penilaian) }}" method="POST" enctype="multipart/form-data" style="display:flex;gap:8px;align-items:center;">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required style="font-size:12px;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-upload"></i> Import</button>
        </form>
    </div>
</div>

<div style="display:flex;gap:14px;flex-wrap:wrap;margin-bottom:12px;font-size:11px;">
    <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;border-radius:3px;background:#fee2e2;display:inline-block;"></span> Di bawah KKM ({{ $kkm }})</span>
    <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;border-radius:3px;background:#dcfce7;display:inline-block;"></span> Tuntas</span>
    <span style="display:flex;align-items:center;gap:5px;"><span style="width:12px;height:12px;border-radius:3px;background:#dbeafe;display:inline-block;"></span> Nilai Maksimal (100)</span>
</div>

<form action="{{ route('erapor.penilaian.save-nilai', $penilaian) }}" method="POST" class="card">
    @csrf
    <input type="hidden" id="kkm-value" value="{{ $kkm }}">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;width:50px;">No</th><th style="padding:10px;width:110px;">No Induk</th><th style="padding:10px;">Nama</th><th style="padding:10px 18px;width:140px;">Nilai (0-100)</th>
        </tr></thead>
        <tbody>
            @forelse($siswaList as $i => $siswa)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;color:#94a3b8;">{{ $i + 1 }}</td>
                <td style="padding:10px;color:#64748b;font-family:monospace;">{{ $siswa->nis }}</td>
                <td style="padding:10px;font-weight:700;">{{ $siswa->nama_lengkap }}</td>
                <td style="padding:10px 18px;">
                    <input type="number" name="nilai[{{ $siswa->id }}]" class="form-input nilai-input" min="0" max="100"
                           value="{{ $nilaiExisting[$siswa->id] ?? '' }}" style="text-align:center;font-weight:700;"
                           oninput="warnaiNilai(this)">
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:20px;text-align:center;color:#94a3b8;">Tidak ada siswa aktif di kelas ini.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($siswaList->count() > 0)
    <div style="padding:16px 18px;">
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;"><i class="ti ti-device-floppy"></i> Simpan Semua Nilai</button>
    </div>
    @endif
</form>

<script>
    function warnaiNilai(input) {
        const kkm = parseInt(document.getElementById('kkm-value').value);
        const val = input.value === '' ? null : parseInt(input.value);

        input.style.background = '';
        input.style.borderColor = '';

        if (val === null) return;

        if (val >= 100) {
            input.style.background = '#dbeafe'; input.style.borderColor = '#93c5fd'; input.style.color = '#1e40af';
        } else if (val < kkm) {
            input.style.background = '#fee2e2'; input.style.borderColor = '#fca5a5'; input.style.color = '#991b1b';
        } else {
            input.style.background = '#dcfce7'; input.style.borderColor = '#86efac'; input.style.color = '#166534';
        }
    }
    document.querySelectorAll('.nilai-input').forEach(warnaiNilai);
</script>
@endsection
