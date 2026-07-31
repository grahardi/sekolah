@extends('layouts.bk')
@section('title', 'Program BK - Survey')
@section('page-title', 'Survey / Asesmen')

@section('header-actions')
    @if(auth()->user()->isAdmin())
    <a href="{{ route('bk.survey.create') }}" class="btn btn-primary"><i class="ti ti-square-plus"></i> Buat Survey Baru</a>
    @endif
@endsection

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Buat kuesioner (DCM, AUM, atau custom), pilih kelas target, lalu bagikan linknya ke siswa.
</p>

<div class="card">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="text-align:left; color:#64748b; font-size:11px; text-transform:uppercase; border-bottom:1px solid #f1f5f9;">
                    <th style="padding:10px 18px;">Judul</th>
                    <th style="padding:10px;">Jenis</th>
                    <th style="padding:10px;">Status</th>
                    <th style="padding:10px;">Respon Masuk</th>
                    <th style="padding:10px 18px; text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($surveys as $s)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:12px 18px;">
                        <a href="{{ route('bk.survey.show', $s) }}" style="font-weight:700;color:#0f172a;text-decoration:none;">{{ $s->judul }}</a>
                        <p style="font-size:11px;color:#94a3b8;margin:0;">{{ $s->target_kelas ?: 'Semua kelas' }}</p>
                    </td>
                    <td style="padding:12px 10px;">
                        <span class="badge" style="background:#eff6ff;color:#2563EB;">{{ $s->jenis }}</span>
                    </td>
                    <td style="padding:12px 10px;">
                        @if($s->status === 'aktif')<span class="badge badge-aktif">Aktif</span>
                        @elseif($s->status === 'draft')<span class="badge" style="background:#f1f5f9;color:#64748b;">Draft</span>
                        @else<span class="badge badge-keluar">Ditutup</span>@endif
                    </td>
                    <td style="padding:12px 10px;">{{ $s->jawabans_count }} siswa</td>
                    @if(auth()->user()->isAdmin())
                    <td style="padding:12px 18px; text-align:right;">
                        <a href="{{ route('bk.survey.edit', $s) }}" class="btn btn-secondary btn-sm"><i class="ti ti-pencil"></i></a>
                        <form action="{{ route('bk.survey.destroy', $s) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus survey ini beserta semua jawabannya?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button>
                        </form>
                    </td>
                    @else
                    <td style="padding:12px 18px;"></td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="5" style="padding:30px; text-align:center; color:#94a3b8;">Belum ada survey. Klik "Buat Survey Baru" untuk mulai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 18px;">{{ $surveys->links() }}</div>
</div>
@endsection
