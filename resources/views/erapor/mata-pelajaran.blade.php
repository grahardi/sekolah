@extends('layouts.erapor')
@section('title', 'Mata Pelajaran')
@section('page-title', 'Mata Pelajaran')

@section('content')
<form action="{{ route('erapor.mata-pelajaran.store') }}" method="POST" class="card" style="padding:16px;margin-bottom:16px;display:grid;grid-template-columns:2fr 1fr auto;gap:10px;align-items:end;">
    @csrf
    <div><label class="form-label">Nama Mata Pelajaran</label><input name="nama" class="form-input" placeholder="mis. Matematika" required></div>
    <div><label class="form-label">Kelompok (opsional)</label><input name="kelompok" class="form-input" placeholder="Umum / Muatan Lokal"></div>
    <div><button class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-plus"></i> Tambah</button></div>
</form>

<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Nama</th><th style="padding:10px;">Kelompok</th><th style="padding:10px 18px;text-align:right;">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse($mapels as $m)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;font-weight:700;">{{ $m->nama }}</td>
                <td style="padding:10px;">{{ $m->kelompok ?? '-' }}</td>
                <td style="padding:10px 18px;text-align:right;">
                    <form action="{{ route('erapor.mata-pelajaran.destroy', $m) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus?')">
                        @csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="3" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada mata pelajaran.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
