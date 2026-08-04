@extends('layouts.manajemen-sekolah')
@section('title', 'Lapor Pelanggaran')

@section('content')
<h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 16px;">Lapor Pelanggaran Siswa</h2>

<form action="{{ route('manajemen-sekolah.tatib.store') }}" method="POST" class="card" style="max-width:520px;padding:24px;">
    @csrf
    <div style="margin-bottom:14px;">
        <label class="form-label">Siswa</label>
        <select name="siswa_id" class="form-input" required>
            <option value="">-- Pilih siswa --</option>
            @foreach($siswaList as $s)
            <option value="{{ $s->id }}">{{ $s->nama_lengkap }} ({{ $s->rombel ? "{$s->kelas}-{$s->rombel}" : $s->kelas }})</option>
            @endforeach
        </select>
    </div>
    <div style="margin-bottom:14px;">
        <label class="form-label">Tanggal</label>
        <input type="date" name="tanggal" value="{{ now()->toDateString() }}" class="form-input" required>
    </div>
    <div style="margin-bottom:14px;">
        <label class="form-label">Kategori</label>
        <select name="kategori" id="kategori" class="form-input" required onchange="isiPoinOtomatis()">
            <option value="">-- Pilih kategori --</option>
            @foreach($daftarKategori as $kategori => $poin)
            <option value="{{ $kategori }}" data-poin="{{ $poin }}">{{ $kategori }} ({{ $poin }} poin)</option>
            @endforeach
        </select>
    </div>
    <div style="margin-bottom:14px;">
        <label class="form-label">Poin</label>
        <input type="number" name="poin" id="poin" class="form-input" min="0" required>
    </div>
    <div style="margin-bottom:18px;">
        <label class="form-label">Deskripsi (opsional)</label>
        <textarea name="deskripsi" class="form-input" rows="3" placeholder="Kronologi kejadian..."></textarea>
    </div>
    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;"><i class="ti ti-device-floppy"></i> Simpan Laporan</button>
</form>

<script>
    function isiPoinOtomatis() {
        const select = document.getElementById('kategori');
        const opt = select.options[select.selectedIndex];
        document.getElementById('poin').value = opt.dataset.poin || 0;
    }
</script>
@endsection
