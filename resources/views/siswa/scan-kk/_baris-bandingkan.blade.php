@php
$berbeda = $nilaiScan && trim(strtoupper($nilaiScan)) !== trim(strtoupper((string) $nilaiInduk));
@endphp
<tr style="border-top:1px solid #f1f5f9;">
    <td style="padding:9px 12px;font-size:12px;color:#64748b;">{{ $label }}</td>
    <td style="padding:9px 12px;font-size:13px;color:#0f172a;">{{ $nilaiInduk ?: '-' }}</td>
    <td style="padding:9px 12px;font-size:13px;font-weight:600;color:{{ $berbeda ? '#dc2626' : '#0f172a' }};">
        <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
            <span>{{ $nilaiScan ?: '-' }}</span>
            @if($berbeda)
            <form action="{{ route('siswa.scan-kk.terapkan', $siswa) }}" method="POST">
                @csrf
                <input type="hidden" name="field" value="{{ $fieldTujuan }}">
                <input type="hidden" name="nilai" value="{{ $nilaiScan }}">
                <button type="submit" class="btn btn-secondary btn-sm" style="white-space:nowrap;">Terapkan</button>
            </form>
            @endif
        </div>
    </td>
</tr>
