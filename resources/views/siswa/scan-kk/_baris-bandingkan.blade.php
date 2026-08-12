@php
$berbeda = $nilaiScan && trim(strtoupper($nilaiScan)) !== trim(strtoupper((string) $nilaiInduk));
@endphp
<tr style="border-top:1px solid #f1f5f9;">
    <td style="padding:9px 12px;">
        @if($berbeda)
        <input type="checkbox" name="fields[]" value="{{ $fieldTujuan }}" class="cb-terapkan">
        @endif
    </td>
    <td style="padding:9px 12px;font-size:12px;color:#64748b;">{{ $label }}</td>
    <td style="padding:9px 12px;font-size:13px;color:#0f172a;">{{ $nilaiInduk ?: '-' }}</td>
    <td style="padding:9px 12px;font-size:13px;font-weight:600;color:{{ $berbeda ? '#dc2626' : '#0f172a' }};">
        {{ $nilaiScan ?: '-' }}
        @if($nilaiScan)
        <input type="hidden" name="nilai[{{ $fieldTujuan }}]" value="{{ $nilaiScan }}">
        @endif
    </td>
</tr>
