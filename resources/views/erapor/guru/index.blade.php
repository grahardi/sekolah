@extends('layouts.erapor')
@section('title', 'Guru')
@section('page-title', 'Guru')

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Guru dari Kepegawaian otomatis muncul di sini. Untuk guru yang tidak terdaftar
    di Kepegawaian (mis. guru bantu/tamu), tambahkan manual lewat form di bawah.
</p>

<form action="{{ route('erapor.guru.store-bantu') }}" method="POST" class="card" style="padding:16px;margin-bottom:16px;display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:10px;align-items:end;">
    @csrf
    <div><label class="form-label">Nama Guru Bantu</label><input name="nama" class="form-input" placeholder="Nama lengkap" required></div>
    <div><label class="form-label">NIP/NUPTK (opsional)</label><input name="nip_nuptk" class="form-input"></div>
    <div><label class="form-label">Keterangan</label><input name="keterangan" class="form-input" placeholder="Guru Bantu"></div>
    <div><button class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-plus"></i> Tambah</button></div>
</form>

<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Nama</th><th style="padding:10px;">NIP/NUPTK</th><th style="padding:10px;">Sumber</th><th style="padding:10px 18px;text-align:right;">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse($gurus as $g)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;font-weight:700;">{{ $g->nama }}</td>
                <td style="padding:10px;color:#94a3b8;">{{ $g->nip_nuptk ?? '-' }}</td>
                <td style="padding:10px;">
                    @if($g->isDariKepegawaian())
                    <span class="badge" style="background:#eff6ff;color:#2563EB;">Kepegawaian</span>
                    @else
                    <span class="badge" style="background:#fef3c7;color:#92400e;">{{ $g->keterangan ?? 'Guru Bantu' }}</span>
                    @endif
                </td>
                <td style="padding:10px 18px;text-align:right;">
                    <a href="{{ route('erapor.guru.tugas-mengajar', $g) }}" class="btn btn-secondary btn-sm"><i class="ti ti-clipboard-list"></i> Tugas Mengajar</a>
                    @if(!$g->isDariKepegawaian())
                    <form action="{{ route('erapor.guru.destroy-bantu', $g) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus guru bantu ini?')">
                        @csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada data guru. Tambah pegawai di Kepegawaian atau guru bantu di atas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
