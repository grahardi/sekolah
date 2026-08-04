@extends('layouts.manajemen-sekolah')
@section('title', 'Absensi Harian')
@section('page-title', 'Absensi Harian')

@section('content')
<form method="GET" class="card" style="padding:16px;margin-bottom:16px;display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div>
        <label class="form-label">Tanggal</label>
        <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-input" onchange="this.form.submit()">
    </div>
    <div>
        <label class="form-label">Kelas</label>
        <select name="kelas_rombel" class="form-input" onchange="this.form.submit()">
            <option value="">-- Pilih kelas --</option>
            @foreach($kelasList as $k)
            @php [$kl,$rb]=explode('|',$k); @endphp
            <option value="{{ $k }}" {{ $kelasRombel === $k ? 'selected' : '' }}>{{ $rb ? "$kl - $rb" : $kl }}</option>
            @endforeach
        </select>
    </div>
</form>

@if($kelasRombel && $siswaList->count() > 0)
<form action="{{ route('manajemen-sekolah.absensi.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
    <div class="card">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
                <th style="padding:10px 18px;width:40px;">No</th>
                <th style="padding:10px;">Nama Siswa</th>
                <th style="padding:10px;width:340px;">Status</th>
                <th style="padding:10px;">Keterangan</th>
                <th style="padding:10px;width:120px;">Foto Bukti</th>
            </tr></thead>
            <tbody>
                @foreach($siswaList as $i => $siswa)
                @php $existing = $absensiTersimpan[$siswa->id] ?? null; @endphp
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:10px 18px;color:#94a3b8;">{{ $i + 1 }}</td>
                    <td style="padding:10px;font-weight:700;">{{ $siswa->nama_lengkap }}</td>
                    <td style="padding:10px;">
                        <div id="grp-{{ $siswa->id }}" style="display:flex;gap:4px;">
                            @foreach(['Hadir'=>'#16a34a','Sakit'=>'#dc2626','Izin'=>'#d97706','Alpha'=>'#64748b','Dispensasi'=>'#2563EB'] as $status => $warna)
                            <label style="flex:1;text-align:center;">
                                <input type="radio" name="status[{{ $siswa->id }}]" value="{{ $status }}" style="display:none;"
                                       {{ ($existing->status ?? 'Hadir') === $status ? 'checked' : '' }}
                                       onchange="document.querySelectorAll('#grp-{{ $siswa->id }} span').forEach(s=>{s.style.background='#f1f5f9';s.style.color='#64748b';}); this.nextElementSibling.style.background='{{ $warna }}'; this.nextElementSibling.style.color='#fff';">
                                <span onclick="this.previousElementSibling.checked=true; this.previousElementSibling.dispatchEvent(new Event('change'));"
                                      style="display:block;padding:5px 2px;border-radius:6px;font-size:10px;cursor:pointer;background:{{ ($existing->status ?? 'Hadir') === $status ? $warna : '#f1f5f9' }};color:{{ ($existing->status ?? 'Hadir') === $status ? '#fff' : '#64748b' }};">{{ substr($status,0,4) }}</span>
                            </label>
                            @endforeach
                        </div>
                    </td>
                    <td style="padding:10px;">
                        <input type="text" name="keterangan[{{ $siswa->id }}]" value="{{ $existing->keterangan ?? '' }}" class="form-input" placeholder="Opsional" style="font-size:12px;padding:6px 8px;">
                    </td>
                    <td style="padding:10px;">
                        <input type="file" name="foto[{{ $siswa->id }}]" accept="image/*" style="font-size:11px;width:110px;">
                        @if($existing?->foto_bukti)<a href="{{ asset('storage/'.$existing->foto_bukti) }}" target="_blank" style="font-size:11px;color:#2563EB;">Lihat foto</a>@endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="padding:16px 18px;">
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;"><i class="ti ti-device-floppy"></i> Simpan Absensi</button>
        </div>
    </div>
</form>
@elseif($kelasRombel)
<p style="text-align:center;color:#94a3b8;padding:30px;">Tidak ada siswa aktif di kelas ini.</p>
@else
<p style="text-align:center;color:#94a3b8;padding:30px;">Pilih kelas dulu di atas untuk mulai absensi.</p>
@endif
@endsection
