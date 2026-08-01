@extends('layouts.erapor')
@section('title', 'Tahun Ajaran')
@section('page-title', 'Tahun Ajaran')

@section('content')
<p style="font-size:12px;color:#94a3b8;margin:-10px 0 16px;">
    Demi keamanan data (nilai, TP, rapor semuanya terhubung ke tahun ajaran), tahun ajaran tidak bisa
    dihapus - cuma bisa diedit atau dinonaktifkan.
</p>
<form action="{{ route('erapor.tahun-ajaran.store') }}" method="POST" class="card" style="padding:16px;margin-bottom:16px;display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:10px;align-items:end;">
    @csrf
    <div><label class="form-label">Nama (mis. 2025/2026)</label><input name="nama" class="form-input" placeholder="2025/2026" required></div>
    <div><label class="form-label">Semester</label>
        <select name="semester" class="form-input" required>
            <option value="Ganjil">Ganjil</option>
            <option value="Genap">Genap</option>
        </select>
    </div>
    <div style="display:flex;align-items:center;gap:6px;padding-bottom:10px;">
        <input type="checkbox" name="is_aktif" value="1" id="is_aktif">
        <label for="is_aktif" style="font-size:13px;">Jadikan aktif</label>
    </div>
    <div><button class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-plus"></i> Tambah</button></div>
</form>

<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Tahun Ajaran</th><th style="padding:10px;">Semester</th><th style="padding:10px;">Status</th><th style="padding:10px 18px;text-align:right;">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse($tahunAjarans as $t)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;font-weight:700;">{{ $t->nama }}</td>
                <td style="padding:10px;">{{ $t->semester }}</td>
                <td style="padding:10px;">
                    @if($t->is_aktif)<span class="badge badge-aktif">Aktif</span>@else<span class="badge" style="background:#f1f5f9;color:#94a3b8;">Nonaktif</span>@endif
                </td>
                <td style="padding:10px 18px;text-align:right;">
                    @if($t->is_aktif)
                    <form action="{{ route('erapor.tahun-ajaran.nonaktifkan', $t) }}" method="POST" style="display:inline;">
                        @csrf<button class="btn btn-secondary btn-sm">Non-aktifkan</button>
                    </form>
                    @else
                    <form action="{{ route('erapor.tahun-ajaran.aktifkan', $t) }}" method="POST" style="display:inline;">
                        @csrf<button class="btn btn-secondary btn-sm">Aktifkan</button>
                    </form>
                    @endif
                    <a href="{{ route('erapor.tahun-ajaran.edit', $t) }}" class="btn btn-primary btn-sm"><i class="ti ti-pencil"></i></a>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada tahun ajaran.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
