@php
    $fileUrl = $arsip->getUrl($field);
    $hasFile = !is_null($fileUrl);
    $isImg   = $hasFile && $arsip->isImage($field);
@endphp

<div style="border:1px solid #e9ecef;border-radius:10px;padding:14px;background:#fafafa;">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
        <i class="ti {{ $meta['icon'] }}" style="font-size:20px;color:{{ $hasFile ? '#16a34a' : '#94a3b8' }};"></i>
        <div style="flex:1;">
            <p style="font-size:13px;font-weight:700;color:#111827;margin:0;">{{ $meta['label'] }}</p>
            <p style="font-size:11px;color:#94a3b8;margin:0;">{{ $hasFile ? 'File tersimpan' : 'Belum ada berkas' }}</p>
        </div>
        @if($hasFile)
            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-primary btn-sm"><i class="ti ti-eye"></i> Lihat</a>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('siswa.arsip.hapus', $siswa) }}"
               onclick="hapusBerkas(event, this, '{{ $field }}', '{{ addslashes($meta['label']) }}')"
               style="display:inline-flex;align-items:center;justify-content:center;padding:5px 10px;background:#fee2e2;color:#991b1b;border-radius:7px;font-size:12px;cursor:pointer;text-decoration:none;">
                <i class="ti ti-trash"></i>
            </a>
            @endif
        @endif
    </div>

    @if($isImg && $hasFile)
        <div style="margin-bottom:10px;">
            <img src="{{ $fileUrl }}" alt="{{ $meta['label'] }}" style="max-height:120px;border-radius:8px;border:1px solid #e9ecef;object-fit:cover;">
        </div>
    @endif

    @if(auth()->user()->isAdmin())
    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:12px;color:#374151;font-weight:500;">
        <i class="ti ti-upload" style="font-size:15px;color:#1d4ed8;"></i>
        {{ $hasFile ? 'Ganti Berkas' : 'Upload Berkas' }}
        <span style="color:#94a3b8;font-weight:400;">(JPG, PNG, PDF · maks 5MB)</span>
        <input type="file" name="{{ $field }}" accept=".jpg,.jpeg,.png,.pdf" style="display:none;" onchange="showFileName(this, '{{ $field }}-label')">
    </label>
    <span id="{{ $field }}-label" style="font-size:11px;color:#1d4ed8;margin-top:4px;display:block;"></span>
    @endif
</div>
