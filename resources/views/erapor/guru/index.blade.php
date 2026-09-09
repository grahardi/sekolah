@extends('layouts.erapor')
@section('title', 'Guru')
@section('page-title', 'Guru')

@section('header-actions')
    <a href="{{ route('erapor.guru.duplikat') }}" class="btn btn-secondary">
        <i class="ti ti-git-merge"></i> Cek Guru Duplikat
    </a>
    <a href="{{ route('erapor.tugas-mengajar.import-form') }}" class="btn btn-secondary">
        <i class="ti ti-file-import"></i> Import Tugas Mengajar
    </a>
    <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">
        <i class="ti ti-arrow-right"></i> Tambah Guru Lewat Kepegawaian
    </a>
@endsection

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

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:20px;">
    <div style="background:#eff6ff;border-radius:14px;padding:18px;">
        <p style="font-size:28px;font-weight:800;color:#1e40af;margin:0;">{{ $totalGuru }}</p>
        <p style="font-size:12px;color:#64748b;margin:2px 0 0;">Total Guru</p>
    </div>
    <div style="background:#f0fdf4;border-radius:14px;padding:18px;">
        <p style="font-size:28px;font-weight:800;color:#16a34a;margin:0;">{{ $totalKelasDiampu }}</p>
        <p style="font-size:12px;color:#64748b;margin:2px 0 0;">Kelas Diampu (semua guru)</p>
    </div>
</div>

<form method="GET" style="margin-bottom:18px;max-width:400px;display:flex;gap:8px;">
    <div style="position:relative;flex:1;">
        <i class="ti ti-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:16px;"></i>
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama guru..." class="form-input" style="padding-left:36px;">
    </div>
    <button type="submit" class="btn btn-primary" style="padding:10px 16px;"><i class="ti ti-search"></i></button>
</form>

<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;padding:12px 16px;margin-bottom:20px;font-size:12.5px;color:#1e40af;">
    <i class="ti ti-info-circle"></i> Semua guru sekarang harus ditambahkan lewat menu <strong>Kepegawaian</strong> dulu, biar data pokoknya (nama/NIP/dll) tersambung terus dan gak perlu diinput dua kali. Guru bantu/tamu lama yang sudah pernah ditambahkan langsung di sini tetap tersimpan seperti biasa.
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px;margin-bottom:20px;">
    @forelse($gurus as $g)
    <div class="card" style="padding:20px;text-align:center;">
        <img src="{{ $g->foto_url }}" style="width:64px;height:64px;border-radius:50%;object-fit:cover;margin:0 auto 10px;">
        <p style="font-weight:700;color:#0f172a;margin:0 0 4px;font-size:14px;">{{ $g->nama }}</p>
        <p style="font-size:11px;color:#94a3b8;margin:0 0 8px;font-family:monospace;">{{ $g->nip_nuptk ?? '-' }}</p>
        @if($g->isDariKepegawaian())
        <span class="badge" style="background:#eff6ff;color:#1E3A5F;">Kepegawaian</span>
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

{{ $gurus->links() }}
@endsection
