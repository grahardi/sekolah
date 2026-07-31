@extends('layouts.bk')
@section('title', 'Pilih Peserta')
@section('page-title', 'Pilih Peserta Survey')

@section('header-actions')
    <a href="{{ route('bk.peserta.create') }}" class="btn btn-primary"><i class="ti ti-square-plus"></i> Tambah Peserta</a>
@endsection

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Assign survey ke kelas tertentu. Satu survey bisa punya beberapa data peserta
    (mis. di-assign ke kelas 7 dulu, lalu belakangan ditambah kelas 8 sebagai data terpisah).
</p>

<div class="card">
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <thead>
                <tr style="text-align:left; color:#64748b; font-size:11px; text-transform:uppercase; border-bottom:1px solid #f1f5f9;">
                    <th style="padding:10px 18px;">Survey</th>
                    <th style="padding:10px;">Kelas Target</th>
                    <th style="padding:10px;">Ditambahkan</th>
                    <th style="padding:10px 18px; text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pesertas as $p)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:12px 18px;">
                        <a href="{{ route('bk.survey.show', $p->survey) }}" style="font-weight:700;color:#0f172a;text-decoration:none;">{{ $p->survey->judul }}</a>
                    </td>
                    <td style="padding:12px 10px;">
                        <div style="display:flex;flex-wrap:wrap;gap:4px;">
                            @foreach($p->target_kelas_array as $k)
                            <span class="badge" style="background:#eff6ff;color:#2563EB;">{{ $k }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td style="padding:12px 10px; color:#94a3b8;">{{ $p->created_at->format('d-m-Y H:i') }}</td>
                    <td style="padding:12px 18px; text-align:right;">
                        <form action="{{ route('bk.peserta.destroy', $p) }}" method="POST" onsubmit="return confirm('Hapus data peserta ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="padding:30px; text-align:center; color:#94a3b8;">Belum ada peserta survey. Klik "Tambah Peserta" untuk mulai.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 18px;">{{ $pesertas->links() }}</div>
</div>
@endsection
