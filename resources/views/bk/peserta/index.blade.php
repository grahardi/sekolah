@extends('layouts.bk')
@section('title', 'Project Survey')
@section('page-title', 'Project Survey')

@section('header-actions')
    <a href="{{ route('bk.peserta.create') }}" class="btn btn-primary"><i class="ti ti-square-plus"></i> Buat Project Baru</a>
@endsection

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Project = survey + kelas target, jadi satu paket dengan link dan statistik sendiri.
    Satu survey bisa punya beberapa project (mis. project untuk kelas 7, project terpisah utk kelas 8).
</p>

<div class="card">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="text-align:left; color:#64748b; font-size:11px; text-transform:uppercase; border-bottom:1px solid #f1f5f9;">
                    <th style="padding:10px 18px;">Survey</th>
                    <th style="padding:10px;">Kelas Target</th>
                    <th style="padding:10px;">Respon</th>
                    <th style="padding:10px;">Dibuat</th>
                    <th style="padding:10px 18px; text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesertas as $p)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:12px 18px;">
                        <a href="{{ route('bk.peserta.show', $p) }}" style="font-weight:700;color:#0f172a;text-decoration:none;">{{ $p->survey->judul }}</a>
                        <p style="font-size:11px;color:#94a3b8;margin:0;">{{ $p->survey->jenis }} &middot; {{ ucfirst($p->survey->status) }}</p>
                    </td>
                    <td style="padding:12px 10px;">
                        <div style="display:flex;flex-wrap:wrap;gap:4px;">
                            @foreach($p->target_kelas_array as $k)
                            <span class="badge" style="background:#eff6ff;color:#2563EB;">{{ $k }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td style="padding:12px 10px;">{{ $p->jawabans()->count() }} siswa</td>
                    <td style="padding:12px 10px; color:#94a3b8;">{{ $p->created_at->format('d-m-Y') }}</td>
                    <td style="padding:12px 18px; text-align:right;">
                        <a href="{{ route('bk.peserta.show', $p) }}" class="btn btn-secondary btn-sm"><i class="ti ti-chart-bar"></i> Detail</a>
                        <form action="{{ route('bk.peserta.destroy', $p) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus project ini beserta semua jawabannya?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="padding:30px; text-align:center; color:#94a3b8;">Belum ada project. Klik "Buat Project Baru" untuk mulai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 18px;">{{ $pesertas->links() }}</div>
</div>
@endsection
