@extends('layouts.erapor')
@section('title', 'Generate User Guru')
@section('page-title', 'Generate User Guru')

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Buat akun login untuk semua guru sekaligus. Format email otomatis:
    <code>namadepan{{ auth()->user()->sekolah_id }}.urutguru@guru.sekolah.co.id</code> - untuk sementara
    akun guru cuma bisa akses modul E-Rapor (Kepegawaian & Buku Induk belum bisa).
</p>

@if(session('akun_massal_baru'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:16px;margin-bottom:18px;">
    <p style="font-size:13px;font-weight:700;color:#166534;margin:0 0 10px;"><i class="ti ti-key"></i> {{ count(session('akun_massal_baru')) }} akun baru dibuat - catat sekarang, password tidak akan tampil lagi:</p>
    <table style="width:100%;border-collapse:collapse;font-size:12px;">
        <thead><tr style="text-align:left;"><th style="padding:4px;">Nama</th><th style="padding:4px;">Email</th><th style="padding:4px;">Password</th></tr></thead>
        <tbody>
            @foreach(session('akun_massal_baru') as $a)
            <tr style="font-family:monospace;"><td style="padding:4px;">{{ $a['nama'] }}</td><td style="padding:4px;">{{ $a['email'] }}</td><td style="padding:4px;">{{ $a['password'] }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<a href="{{ route('erapor.guru.export-user') }}" class="btn btn-secondary" style="margin-bottom:18px;"><i class="ti ti-download"></i> Export User</a>

<div class="card" style="padding:16px;margin-bottom:20px;">
    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 4px;">Atau Import User (kalau guru sudah punya email sendiri)</p>
    <p style="font-size:12px;color:#64748b;margin:0 0 12px;">Download template, isi kolom Email &amp; Password sesuai akun yang sudah ada, lalu upload lagi di sini.</p>
    <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <a href="{{ route('erapor.guru.template-user') }}" class="btn btn-secondary btn-sm"><i class="ti ti-download"></i> Download Template</a>
        <form action="{{ route('erapor.guru.import-user') }}" method="POST" enctype="multipart/form-data" style="display:flex;gap:8px;align-items:center;">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls,.csv" required style="font-size:12px;">
            <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-upload"></i> Import</button>
        </form>
    </div>
</div>

<form action="{{ route('erapor.guru.generate-user-massal') }}" method="POST" id="form-generate" onsubmit="return confirm('Generate akun untuk guru yang dicentang?')">
    @csrf
    <div class="card" style="padding:0;overflow:hidden;">
        <div style="padding:12px 18px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;">
            <label style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px;cursor:pointer;">
                <input type="checkbox" id="cb-semua" onclick="document.querySelectorAll('.cb-guru').forEach(cb => cb.checked = this.checked)"> Centang semua yang belum ada akun
            </label>
            <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-users-plus"></i> Generate Akun untuk yang Dicentang</button>
        </div>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
                <th style="padding:10px 18px;width:36px;"></th><th style="padding:10px;">Nama</th><th style="padding:10px;">Status Akun</th><th style="padding:10px;">Email (kalau sudah ada)</th>
            </tr></thead>
            <tbody>
                @forelse($gurus as $g)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:10px 18px;">
                        @if(! $g->user_id)
                        <input type="checkbox" name="guru_ids[]" value="{{ $g->id }}" class="cb-guru">
                        @endif
                    </td>
                    <td style="padding:10px;font-weight:700;">{{ $g->nama }}</td>
                    <td style="padding:10px;">
                        @if($g->user_id)<span class="badge badge-aktif">Sudah Ada</span>@else<span class="badge" style="background:#f1f5f9;color:#94a3b8;">Belum Ada</span>@endif
                    </td>
                    <td style="padding:10px;font-family:monospace;color:#64748b;">{{ $g->user?->email ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada data guru.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</form>
@endsection
