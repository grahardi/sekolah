@extends('layouts.erapor')
@section('title', 'Kegiatan P5')
@section('page-title', 'Kegiatan P5 (Kokurikuler)')

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Daftar projek P5 yang dijalankan sekolah. Nanti bisa dipilih sebagai template deskripsi saat mengisi rapor per siswa.
</p>

<form action="{{ route('erapor.kegiatan-p5.store') }}" method="POST" class="card" style="padding:16px;margin-bottom:16px;">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:10px;">
        <div><label class="form-label">Nama Kegiatan</label><input name="nama_kegiatan" class="form-input" placeholder="mis. 7 Kebiasaan Anak Indonesia Hebat" required></div>
        <div><label class="form-label">Tema P5</label><input name="tema" class="form-input" placeholder="mis. Gaya Hidup Berkelanjutan"></div>
        <div><label class="form-label">Tahun Ajaran</label>
            <select name="tahun_ajaran_id" class="form-input">
                <option value="">-- Semua tahun --</option>
                @foreach($tahunAjarans as $t)<option value="{{ $t->id }}" {{ $t->is_aktif ? 'selected' : '' }}>{{ $t->label }}</option>@endforeach
            </select>
        </div>
    </div>
    <label class="form-label">Template Deskripsi (opsional)</label>
    <textarea name="deskripsi_template" class="form-input" rows="2" placeholder="mis. Dalam projek ini, ananda menunjukkan perkembangan yang sangat baik terutama pada...">{{ old('deskripsi_template') }}</textarea>
    <button type="submit" class="btn btn-primary" style="margin-top:10px;"><i class="ti ti-plus"></i> Tambah Kegiatan</button>
</form>

<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Nama Kegiatan</th><th style="padding:10px;">Tema</th><th style="padding:10px;">Tahun Ajaran</th><th></th>
        </tr></thead>
        <tbody>
            @forelse($kegiatans as $k)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;font-weight:700;">{{ $k->nama_kegiatan }}</td>
                <td style="padding:10px;">{{ $k->tema ?? '-' }}</td>
                <td style="padding:10px;color:#94a3b8;">{{ $k->tahunAjaran->label ?? 'Semua' }}</td>
                <td style="padding:10px;text-align:right;">
                    <form action="{{ route('erapor.kegiatan-p5.destroy', $k) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button></form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada kegiatan P5.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
