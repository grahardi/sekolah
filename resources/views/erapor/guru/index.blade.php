@extends('layouts.erapor')
@section('title', 'Guru')
@section('page-title', 'Guru')

@section('content')
@if(session('akun_guru_baru'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:14px 16px;margin-bottom:16px;">
    <p style="font-size:13px;font-weight:700;color:#166534;margin:0 0 4px;"><i class="ti ti-key"></i> Akun login baru dibuat untuk guru ini:</p>
    <p style="font-size:13px;color:#166534;margin:0;font-family:monospace;">
        Email: {{ session('akun_guru_baru')['email'] }} &nbsp;|&nbsp; Password: {{ session('akun_guru_baru')['password'] }}
    </p>
    <p style="font-size:11px;color:#166534;margin:4px 0 0;">Catat kredensial ini - password tidak akan ditampilkan lagi setelah halaman ini ditutup.</p>
</div>
@endif

<form method="GET" style="margin-bottom:18px;max-width:400px;">
    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama guru..." class="form-input" onchange="this.form.submit()">
</form>

<form action="{{ route('erapor.guru.store-bantu') }}" method="POST" class="card" style="padding:16px;margin-bottom:20px;display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;align-items:end;">
    @csrf
    <div><label class="form-label">Tambah Guru Bantu</label><input name="nama" class="form-input" placeholder="Nama lengkap" required></div>
    <div><label class="form-label">NIP/NUPTK</label><input name="nip_nuptk" class="form-input"></div>
    <div><label class="form-label">Keterangan</label><input name="keterangan" class="form-input" placeholder="Guru Bantu"></div>
    <div><button class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-plus"></i> Tambah</button></div>
</form>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;">
    @forelse($gurus as $g)
    <div class="card" style="padding:20px;text-align:center;">
        <img src="{{ $g->foto_url }}" style="width:64px;height:64px;border-radius:50%;object-fit:cover;margin:0 auto 10px;">
        <p style="font-weight:700;color:#0f172a;margin:0 0 4px;font-size:14px;">{{ $g->nama }}</p>
        <p style="font-size:11px;color:#94a3b8;margin:0 0 8px;font-family:monospace;">{{ $g->nip_nuptk ?? '-' }}</p>
        @if($g->isDariKepegawaian())
        <span class="badge" style="background:#eff6ff;color:#2563EB;">Kepegawaian</span>
        @else
        <span class="badge" style="background:#fef3c7;color:#92400e;">{{ $g->keterangan ?? 'Guru Bantu' }}</span>
        @endif

        <div style="display:flex;flex-direction:column;gap:6px;margin-top:14px;">
            @if($g->isDariKepegawaian())
            <a href="{{ route('pegawai.show', $g->pegawai_id) }}" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;"><i class="ti ti-pencil"></i> Edit</a>
            @endif
            <a href="{{ route('erapor.guru.tugas-mengajar', $g) }}" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;"><i class="ti ti-clipboard-list"></i> Tugas Mengajar</a>
            <a href="{{ route('erapor.guru.login-sebagai', $g) }}" class="btn btn-primary btn-sm" style="width:100%;justify-content:center;" onclick="return confirm('Login sebagai {{ $g->nama }}? Kamu bisa kembali ke akun admin kapan saja.')">
                <i class="ti ti-login-2"></i> Login Sebagai
            </a>
        </div>

        @if(!$g->isDariKepegawaian())
        <form action="{{ route('erapor.guru.destroy-bantu', $g) }}" method="POST" onsubmit="return confirm('Hapus guru bantu ini?')" style="margin-top:6px;">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm" style="width:100%;justify-content:center;"><i class="ti ti-trash"></i> Hapus</button>
        </form>
        @endif
    </div>
    @empty
    <p style="grid-column:1/-1;text-align:center;color:#94a3b8;padding:30px;">Belum ada data guru.</p>
    @endforelse
</div>
@endsection
