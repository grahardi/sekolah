@extends('layouts.manajemen-sekolah')
@section('title', 'Isi Absensi')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px;">
    <h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0;">Isi Absensi</h2>
    <a href="{{ route('manajemen-sekolah.absensi.hari-ini') }}" class="btn btn-secondary"><i class="ti ti-list"></i> Absensi Siswa Hari Ini</a>
</div>

<form method="GET" class="card" style="padding:16px;margin-bottom:16px;display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;">
    <div>
        <label class="form-label">Tanggal</label>
        <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-input" onchange="this.form.submit()">
    </div>
    <div>
        <label class="form-label">Kelas (opsional)</label>
        <select name="kelas_rombel" class="form-input" onchange="this.form.submit()">
            <option value="">-- Semua kelas --</option>
            @foreach($kelasList as $k)
            @php [$kl,$rb]=explode('|',$k); @endphp
            <option value="{{ $k }}" {{ $kelasRombel === $k ? 'selected' : '' }}>{{ $rb ? "$kl - $rb" : $kl }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label">Cari Nama Siswa</label>
        <div style="display:flex;gap:6px;">
            <input type="text" name="cari" value="{{ $cari }}" placeholder="Ketik nama siswa..." class="form-input">
            <button type="submit" class="btn btn-primary" style="flex-shrink:0;"><i class="ti ti-search"></i> Cari</button>
        </div>
    </div>
</form>

@if(($kelasRombel || $cari) && $siswaList->count() > 0)
<div style="display:flex;flex-direction:column;gap:8px;">
    @foreach($siswaList as $siswa)
    @php $existing = $absensiTersimpan[$siswa->id] ?? null; @endphp
    <div class="card" style="padding:14px 18px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
        <div style="min-width:180px;">
            <p style="font-weight:700;color:#0f172a;margin:0;font-size:13px;">{{ $siswa->nama_lengkap }}</p>
            <p style="font-size:11px;color:#94a3b8;margin:2px 0 0;">
                {{ $siswa->rombel ? "{$siswa->kelas}-{$siswa->rombel}" : $siswa->kelas }} &middot;
                @if($existing)<span style="color:#0f172a;font-weight:600;">{{ $existing->status }}</span>@else Belum diabsen @endif
            </p>
        </div>
        <div style="display:flex;gap:6px;flex-wrap:wrap;">
            <button type="button" class="btn btn-sm" style="background:#fde68a;color:#92400e;" onclick="bukaModalAbsen({{ $siswa->id }}, '{{ $siswa->nama_lengkap }}', 'Sakit')"><i class="ti ti-vaccine"></i> Sakit</button>
            <button type="button" class="btn btn-sm" style="background:#bbf7d0;color:#15803d;" onclick="bukaModalAbsen({{ $siswa->id }}, '{{ $siswa->nama_lengkap }}', 'Izin')"><i class="ti ti-mail"></i> Izin</button>
            <button type="button" class="btn btn-sm" style="background:#bfdbfe;color:#1d4ed8;" onclick="bukaModalAbsen({{ $siswa->id }}, '{{ $siswa->nama_lengkap }}', 'Dispensasi')"><i class="ti ti-speakerphone"></i> Dispensasi</button>
            <button type="button" class="btn btn-sm" style="background:#fecaca;color:#b91c1c;" onclick="bukaModalAbsen({{ $siswa->id }}, '{{ $siswa->nama_lengkap }}', 'Alpha')"><i class="ti ti-x"></i> Alfa</button>
        </div>
    </div>
    @endforeach
</div>
@elseif($kelasRombel || $cari)
<p style="text-align:center;color:#94a3b8;padding:30px;">Tidak ada siswa ditemukan.</p>
@else
<p style="text-align:center;color:#94a3b8;padding:30px;">Pilih kelas atau cari nama siswa dulu di atas.</p>
@endif

{{-- Modal tunggal, dipakai ulang utk semua siswa & status - diisi dinamis via JS --}}
<div id="modal-absen" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:50;align-items:center;justify-content:center;padding:20px;">
    <div class="card" style="max-width:420px;width:100%;padding:24px;text-align:left;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <p id="modal-absen-judul" style="font-size:15px;font-weight:800;color:#0f172a;margin:0;">Absen</p>
            <button type="button" onclick="document.getElementById('modal-absen').style.display='none'" style="background:none;border:none;font-size:18px;color:#94a3b8;cursor:pointer;">&times;</button>
        </div>
        <form id="form-modal-absen" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
            <input type="hidden" name="status" id="modal-absen-status">

            <label class="form-label">Foto Bukti (opsional)</label>
            <input type="file" name="foto" accept="image/*" class="form-input" style="margin-bottom:14px;">

            <label class="form-label">Keterangan (opsional)</label>
            <input type="text" name="keterangan" class="form-input" placeholder="contoh: acara keluarga" style="margin-bottom:6px;">
            <p style="font-size:11px;color:#94a3b8;margin:0 0 16px;">Foto & keterangan boleh dikosongkan - klik tombol di bawah untuk simpan langsung.</p>

            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" onclick="document.getElementById('modal-absen').style.display='none'" class="btn btn-secondary">Batal</button>
                <button type="submit" id="modal-absen-submit" class="btn" style="background:#16a34a;color:#fff;">Absen</button>
            </div>
        </form>
    </div>
</div>

<script>
    const WARNA_TOMBOL = { Sakit: '#d97706', Izin: '#16a34a', Dispensasi: '#2563EB', Alpha: '#dc2626' };

    function bukaModalAbsen(siswaId, namaSiswa, status) {
        document.getElementById('modal-absen-judul').textContent = `Absen ${status} - ${namaSiswa}`;
        document.getElementById('modal-absen-status').value = status;
        document.getElementById('form-modal-absen').action = `/manajemen-sekolah/absensi/${siswaId}/tandai`;
        const btn = document.getElementById('modal-absen-submit');
        btn.style.background = WARNA_TOMBOL[status] || '#16a34a';
        btn.textContent = 'Absen';
        document.getElementById('modal-absen').style.display = 'flex';
    }
</script>
@endsection
