<div class="card" style="padding:16px 20px;margin-bottom:20px;">
    <div style="background:#e0f2fe;border-radius:8px;padding:8px 14px;margin-bottom:16px;">
        <p style="font-size:13px;font-weight:800;color:#0369a1;margin:0;text-align:center;">{{ $judul }}</p>
    </div>
    <div class="ms-grid-3col">
        @foreach($items as $m)
        @if(!empty($m['segera']))
        <div class="sb-item-demo" style="border-radius:10px;padding:24px 16px;text-align:center;background:{{ $m['bg'] }};display:flex;flex-direction:column;align-items:center;gap:10px;">
            <span style="width:46px;height:46px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;">
                <i class="ti {{ $m['icon'] }}" style="font-size:20px;color:{{ $m['warna'] }};"></i>
            </span>
            <span style="font-size:13px;font-weight:700;color:{{ $m['warna'] }};">{{ $m['label'] }}</span>
            <span class="sb-demo-badge">Segera</span>
        </div>
        @else
        <a href="{{ $m['href'] }}" style="text-decoration:none;border-radius:10px;padding:24px 16px;text-align:center;background:{{ $m['bg'] }};display:flex;flex-direction:column;align-items:center;gap:10px;">
            <span style="width:46px;height:46px;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;">
                <i class="ti {{ $m['icon'] }}" style="font-size:20px;color:{{ $m['warna'] }};"></i>
            </span>
            <span style="font-size:13px;font-weight:700;color:{{ $m['warna'] }};">{{ $m['label'] }}</span>
        </a>
        @endif
        @endforeach
    </div>
</div>
