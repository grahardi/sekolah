@php
$berbeda = $nilaiScan && trim(strtoupper($nilaiScan)) !== trim(strtoupper((string) $nilaiInduk));
@endphp
<div style="padding:8px 0;border-bottom:1px solid #f1f5f9;">
    <p style="font-size:11px;color:#94a3b8;margin:0;">{{ $label }} (data induk)</p>
    <p style="margin:0;color:#0f172a;">{{ $nilaiInduk ?: '-' }}</p>
</div>
<div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f1f5f9;margin-bottom:6px;">
    <div>
        <p style="font-size:11px;color:#7c3aed;margin:0;">{{ $label }} (hasil scan)</p>
        <p style="margin:0;font-weight:600;color:{{ $berbeda ? '#dc2626' : '#0f172a' }};">{{ $nilaiScan ?: '-' }}</p>
    </div>
    @if($berbeda)
    <form action="{{ route('siswa.scan-kk.terapkan', $siswa) }}" method="POST">
        @csrf
        <input type="hidden" name="field" value="{{ $fieldTujuan }}">
        <input type="hidden" name="nilai" value="{{ $nilaiScan }}">
        <button type="submit" class="btn btn-secondary btn-sm">Terapkan</button>
    </form>
    @endif
</div>
