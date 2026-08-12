@php
    $navItems = [
        'pokok'   => ['label' => 'Data Pokok',    'icon' => 'ti-id-badge-2',     'route' => route('siswa.show',    $siswa)],
        'nilai'   => ['label' => 'Nilai Rapor',   'icon' => 'ti-clipboard-list', 'route' => route('siswa.nilai.index', $siswa)],
        'arsip'   => ['label' => 'Arsip Berkas',  'icon' => 'ti-folder',         'route' => route('siswa.arsip.show',  $siswa)],
        'ocr'     => ['label' => 'Hasil OCR',     'icon' => 'ti-scan',           'route' => route('siswa.scan-kk.show', $siswa)],
        'prestasi'=> ['label' => 'Prestasi',      'icon' => 'ti-trophy',         'route' => route('siswa.prestasi.index', $siswa)],
    ];
@endphp

<div style="display:flex;align-items:center;gap:4px;margin-bottom:20px;background:#fff;border:1px solid #e9ecef;border-radius:12px;padding:5px;">
    <div style="display:flex;align-items:center;gap:10px;padding:4px 12px;margin-right:4px;border-right:1px solid #e9ecef;">
        <div style="width:32px;height:32px;border-radius:8px;overflow:hidden;background:#dbeafe;flex-shrink:0;">
            <img src="{{ $siswa->foto_url }}" style="width:100%;height:100%;object-fit:cover;"
                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($siswa->nama_lengkap) }}&background=dbeafe&color=1d4ed8&size=32'">
        </div>
        <div>
            <p style="font-size:12px;font-weight:700;color:#111827;margin:0;">{{ $siswa->nama_lengkap }}</p>
            <p style="font-size:10px;color:#94a3b8;margin:0;">{{ $siswa->nisn }} · Kelas {{ $siswa->kelas }}</p>
        </div>
    </div>

    @foreach($navItems as $key => $item)
    <a href="{{ $item['route'] }}"
       style="display:flex;align-items:center;gap:6px;padding:7px 14px;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;
              {{ $active === $key ? 'background:#1d4ed8;color:#fff;' : 'color:#64748b;' }}">
        <i class="ti {{ $item['icon'] }}" style="font-size:15px;"></i>
        {{ $item['label'] }}
    </a>
    @endforeach
</div>
