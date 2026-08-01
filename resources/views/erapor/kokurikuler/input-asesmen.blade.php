@extends('layouts.erapor')
@section('title', 'Input Asesmen Kokurikuler')
@section('page-title', 'Input Asesmen Kokurikuler')

@section('header-actions')
    <a href="{{ route('erapor.kokurikuler.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<form method="GET" class="card" style="padding:16px;margin-bottom:16px;display:grid;grid-template-columns:1fr 1fr;gap:14px;">
    <div>
        <label class="form-label">1. Pilih Kegiatan Projek</label>
        <select name="kegiatan" class="form-input" onchange="this.form.submit()">
            <option value="">-- Pilih kegiatan --</option>
            @foreach($daftarKegiatan as $k)
            <option value="{{ $k->id }}" {{ (string)$kegiatanId === (string)$k->id ? 'selected' : '' }}>{{ $k->nama_kegiatan }} (Sem {{ $k->semester == 1 ? 'Ganjil' : 'Genap' }})</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label">2. Pilih Kelas Sasaran</label>
        <select name="kelas" class="form-input" onchange="this.form.submit()" {{ $daftarKelas->isEmpty() ? 'disabled' : '' }}>
            <option value="">-- Pilih kelas --</option>
            @foreach($daftarKelas as $kt)
            <option value="{{ $kt->rombel ? "{$kt->kelas}|{$kt->rombel}" : "{$kt->kelas}|" }}" {{ $kelasRombel === ($kt->rombel ? "{$kt->kelas}|{$kt->rombel}" : "{$kt->kelas}|") ? 'selected' : '' }}>{{ $kt->kelas_lengkap }}</option>
            @endforeach
        </select>
    </div>
</form>

@if($kegiatanId && $kelasRombel && $siswaList->count() > 0)
<form action="{{ route('erapor.kokurikuler.save-asesmen') }}" method="POST">
    @csrf
    <div class="card" style="margin-bottom:16px;">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;">Lembar Kerja Penilaian</p>
            <div style="display:flex;gap:6px;">
                <span class="badge" style="background:#16a34a;color:#fff;">Sangat Baik</span>
                <span class="badge" style="background:#2563EB;color:#fff;">Baik</span>
                <span class="badge" style="background:#eab308;color:#1E293B;">Cukup</span>
                <span class="badge" style="background:#dc2626;color:#fff;">Kurang</span>
            </div>
        </div>
        <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;font-size:12px;">
            <thead>
                <tr style="background:#f8fafc;text-align:center;">
                    <th style="padding:10px;text-align:left;border:1px solid #e2e8f0;min-width:200px;">Nama Siswa</th>
                    @foreach($daftarDimensi as $d)<th style="padding:10px;border:1px solid #e2e8f0;min-width:160px;">{{ $d->nama_dimensi }}</th>@endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($siswaList as $siswa)
                <tr>
                    <td style="padding:10px;font-weight:700;border:1px solid #f1f5f9;white-space:nowrap;">{{ $siswa->nama_lengkap }}</td>
                    @foreach($daftarDimensi as $d)
                    @php $nilaiSekarang = $nilaiTersimpan[$siswa->id][$d->id] ?? null; @endphp
                    <td style="padding:10px;border:1px solid #f1f5f9;">
                        <div style="display:flex;justify-content:center;gap:4px;margin-bottom:6px;" id="grup-{{ $siswa->id }}-{{ $d->id }}">
                            @foreach(['Sangat Baik'=>['A','#16a34a'],'Baik'=>['B','#2563EB'],'Cukup'=>['C','#eab308'],'Kurang'=>['D','#dc2626']] as $label => $info)
                            <button type="button"
                                onclick="pilihNilai({{ $siswa->id }}, {{ $d->id }}, '{{ $label }}', '{{ $info[1] }}')"
                                id="btn-{{ $siswa->id }}-{{ $d->id }}-{{ str_replace(' ', '', $label) }}"
                                class="btn-nilai-kokurikuler"
                                style="width:30px;height:30px;border-radius:50%;border:1px solid #e2e8f0;background:{{ $nilaiSekarang === $label ? $info[1] : '#fff' }};color:{{ $nilaiSekarang === $label ? '#fff' : '#64748b' }};font-weight:700;font-size:11px;cursor:pointer;">
                                {{ $info[0] }}
                            </button>
                            @endforeach
                        </div>
                        <input type="hidden" name="nilai[{{ $siswa->id }}][{{ $d->id }}]" id="input-{{ $siswa->id }}-{{ $d->id }}" value="{{ $nilaiSekarang }}">
                        <input type="text" name="catatan[{{ $siswa->id }}][{{ $d->id }}]" value="{{ $catatanTersimpan[$siswa->id][$d->id] ?? '' }}" placeholder="Catatan..." class="form-input" style="font-size:11px;padding:5px 8px;">
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    </div>
    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;"><i class="ti ti-device-floppy"></i> Simpan Semua Asesmen</button>
</form>
@elseif($kegiatanId)
<p style="text-align:center;color:#94a3b8;padding:30px;">Pilih kelas sasaran dulu di atas.</p>
@else
<p style="text-align:center;color:#94a3b8;padding:30px;">Pilih kegiatan projek dulu di atas.</p>
@endif

<script>
    function pilihNilai(siswaId, dimensiId, label, warna) {
        document.getElementById(`input-${siswaId}-${dimensiId}`).value = label;
        const grup = document.getElementById(`grup-${siswaId}-${dimensiId}`);
        grup.querySelectorAll('.btn-nilai-kokurikuler').forEach(btn => {
            btn.style.background = '#fff';
            btn.style.color = '#64748b';
        });
        document.getElementById(`btn-${siswaId}-${dimensiId}-${label.replace(' ', '')}`).style.background = warna;
        document.getElementById(`btn-${siswaId}-${dimensiId}-${label.replace(' ', '')}`).style.color = '#fff';
    }
</script>
@endsection
