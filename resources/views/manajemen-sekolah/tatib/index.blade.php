@extends('layouts.manajemen-sekolah')
@section('title', 'Tata Tertib')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0;">Tata Tertib Siswa</h2>
    <a href="{{ route('manajemen-sekolah.tatib.create') }}" class="btn btn-primary"><i class="ti ti-square-plus"></i> Lapor Pelanggaran</a>
</div>

<div class="card" style="padding:18px;margin-bottom:20px;">
    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 12px;">Top 10 Poin Pelanggaran Tertinggi</p>
    <div style="display:flex;flex-wrap:wrap;gap:8px;">
        @forelse($rekapPoin as $r)
        <div style="background:#fef2f2;border-radius:8px;padding:8px 14px;">
            <span style="font-size:12px;font-weight:700;color:#0f172a;">{{ $r->siswa->nama_lengkap ?? '-' }}</span>
            <span style="font-size:12px;color:#dc2626;font-weight:800;margin-left:6px;">{{ $r->total_poin }} poin</span>
        </div>
        @empty
        <p style="font-size:12px;color:#94a3b8;">Belum ada data pelanggaran.</p>
        @endforelse
    </div>
</div>

<form method="GET" style="margin-bottom:16px;max-width:400px;display:flex;gap:8px;">
    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama siswa..." class="form-input">
    <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i></button>
</form>

<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Tanggal</th><th style="padding:10px;">Siswa</th><th style="padding:10px;">Kategori</th><th style="padding:10px;">Poin</th><th style="padding:10px;">Status</th><th style="padding:10px 18px;text-align:right;">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse($pelanggaranList as $p)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;color:#64748b;">{{ $p->tanggal->locale('id')->translatedFormat('d M Y') }}</td>
                <td style="padding:10px;font-weight:700;">{{ $p->siswa->nama_lengkap ?? '-' }}</td>
                <td style="padding:10px;">{{ $p->kategori }}</td>
                <td style="padding:10px;color:#dc2626;font-weight:700;">{{ $p->poin }}</td>
                <td style="padding:10px;">
                    @if($p->status === 'Sudah Ditindak')<span class="badge badge-aktif">Sudah Ditindak</span>
                    @else<span class="badge" style="background:#fef3c7;color:#92400e;">Belum Ditindak</span>@endif
                </td>
                <td style="padding:10px 18px;text-align:right;">
                    <button type="button" onclick="document.getElementById('modal-{{ $p->id }}').style.display='flex'" class="btn btn-secondary btn-sm"><i class="ti ti-message-report"></i></button>
                    <form action="{{ route('manajemen-sekolah.tatib.destroy', $p) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus catatan ini?')">
                        @csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button>
                    </form>

                    <div id="modal-{{ $p->id }}" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:50;align-items:center;justify-content:center;padding:20px;">
                        <div class="card" style="max-width:400px;width:100%;padding:22px;text-align:left;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                                <p style="font-size:14px;font-weight:800;color:#0f172a;margin:0;">Tindak Lanjut - {{ $p->siswa->nama_lengkap }}</p>
                                <button type="button" onclick="document.getElementById('modal-{{ $p->id }}').style.display='none'" style="background:none;border:none;font-size:18px;color:#94a3b8;cursor:pointer;">&times;</button>
                            </div>
                            <p style="font-size:12px;color:#64748b;margin:0 0 10px;">{{ $p->deskripsi ?: '(tidak ada deskripsi)' }}</p>
                            <form action="{{ route('manajemen-sekolah.tatib.tindak-lanjut', $p) }}" method="POST">
                                @csrf @method('PUT')
                                <textarea name="tindak_lanjut" class="form-input" rows="3" placeholder="mis. Sudah dipanggil orang tua, siswa diberi peringatan tertulis...">{{ $p->tindak_lanjut }}</textarea>
                                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:10px;margin-top:10px;">Simpan</button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada catatan pelanggaran.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $pelanggaranList->links() }}
@endsection
