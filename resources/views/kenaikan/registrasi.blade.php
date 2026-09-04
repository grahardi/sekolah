@extends('layouts.app')
@section('title', 'Registrasi Mutasi')
@section('page-title', 'Registrasi Mutasi / Keluar')

@section('header-actions')
    <a href="{{ route('kenaikan.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Naik Kelas / Lulus Massal</a>
@endsection

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif

<p style="font-size:13px;color:#64748b;margin:-8px 0 16px;max-width:600px;">
    Cari siswa yang mau ditandai Tinggal Kelas, Mutasi, Mengundurkan Diri, Wafat, Hilang, atau alasan keluar lainnya.
</p>

<form method="GET" style="display:flex;gap:10px;margin-bottom:20px;max-width:500px;">
    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NIS, atau NISN..." class="form-input" autofocus>
    <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i> Cari</button>
</form>

@if($search)
<div class="card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
        <thead style="background:#f8fafc;">
            <tr>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;width:50px;"></th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Nama</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">Kelas</th>
                <th style="padding:10px 16px;text-align:left;font-size:11px;color:#64748b;">NISN</th>
                <th style="padding:10px 16px;text-align:right;font-size:11px;color:#64748b;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($siswaList as $s)
            <tr style="border-top:1px solid #f1f5f9;">
                <td style="padding:10px 16px;">
                    <div style="width:34px;height:34px;border-radius:8px;overflow:hidden;background:#f1f5f9;display:flex;align-items:center;justify-content:center;">
                        @if($s->foto)
                        <img src="{{ asset('storage/' . $s->foto) }}" style="width:100%;height:100%;object-fit:cover;">
                        @else
                        <i class="ti ti-user" style="color:#cbd5e1;font-size:16px;"></i>
                        @endif
                    </div>
                </td>
                <td style="padding:10px 16px;font-size:13px;font-weight:600;color:#0f172a;">{{ $s->nama_lengkap }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#475569;">{{ $s->kelas }}{{ $s->rombel ? " - $s->rombel" : '' }}</td>
                <td style="padding:10px 16px;font-size:13px;color:#475569;">{{ $s->nisn }}</td>
                <td style="padding:10px 16px;text-align:right;">
                    <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('modal-{{ $s->id }}').style.display='flex'">Registrasi</button>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">Tidak ada siswa aktif yang cocok dengan pencarian.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

@foreach($siswaList as $s)
<div id="modal-{{ $s->id }}" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:60;align-items:center;justify-content:center;padding:20px;">
    <div class="card" style="max-width:420px;width:100%;padding:22px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Registrasi - {{ $s->nama_lengkap }}</p>
            <button type="button" onclick="document.getElementById('modal-{{ $s->id }}').style.display='none'" style="border:none;background:none;font-size:20px;color:#94a3b8;cursor:pointer;">&times;</button>
        </div>
        <form action="{{ route('kenaikan.registrasi.proses') }}" method="POST" onsubmit="return confirm('Yakin? Perubahan ini akan langsung berlaku.')">
            @csrf
            <input type="hidden" name="siswa_id" value="{{ $s->id }}">
            <input type="hidden" name="tahun_ajaran" value="{{ $tahunAjaran }}">

            <label class="form-label">Aksi</label>
            <select name="aksi" class="form-input" required style="margin-bottom:12px;">
                <option value="">-- Pilih --</option>
                <option value="tinggal">🔁 Tinggal Kelas</option>
                <option value="mutasi">🚌 Mutasi</option>
                <option value="mengundurkan_diri">✋ Mengundurkan Diri</option>
                <option value="wafat">🕊️ Wafat</option>
                <option value="hilang">❓ Hilang</option>
                <option value="lainnya">⚪ Lainnya</option>
            </select>

            <label class="form-label">Keterangan (opsional)</label>
            <input type="text" name="keterangan_keluar" class="form-input" placeholder="mis. Pindah ke SMP Negeri 2 Malang" style="margin-bottom:16px;">

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">Simpan</button>
        </form>
    </div>
</div>
@endforeach

@endsection
