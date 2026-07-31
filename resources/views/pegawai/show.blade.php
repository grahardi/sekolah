@extends('layouts.kepegawaian')
@section('title', $pegawai->nama_lengkap)
@section('page-title', $pegawai->nama_lengkap)

@section('header-actions')
    @if(auth()->user()->isAdmin())
    <a href="{{ route('pegawai.edit', $pegawai) }}" class="btn btn-secondary"><i class="ti ti-pencil"></i> Edit Data Utama</a>
    @endif
    <a href="{{ route('pegawai.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="card" style="padding:20px; margin-bottom:18px; display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
    <img src="{{ $pegawai->foto_url }}" style="width:60px;height:60px;border-radius:50%;object-fit:cover;">
    <div style="flex:1;min-width:200px;">
        <p style="font-weight:700;font-size:16px;color:#0f172a;margin:0;">{{ $pegawai->nama_lengkap }}</p>
        <p style="font-size:12px;color:#94a3b8;margin:2px 0 0;">
            {{ $pegawai->jenis_kepegawaian }} &middot; {{ $pegawai->jabatan ?? '-' }}
            @if($pegawai->isAsn()) &middot; Gol. {{ $pegawai->golongan ?? '-' }} @endif
        </p>
    </div>
    <span class="badge {{ $pegawai->status_aktif === 'Aktif' ? 'badge-aktif' : 'badge-keluar' }}">{{ $pegawai->status_aktif }}</span>
</div>

{{-- Tab navigasi sederhana --}}
<div style="display:flex; gap:6px; margin-bottom:16px; flex-wrap:wrap;" x-data>
    <button onclick="showTab('pendidikan')" id="tab-btn-pendidikan" class="tab-btn active">Riwayat Pendidikan</button>
    <button onclick="showTab('keluarga')" id="tab-btn-keluarga" class="tab-btn">Tunjangan Keluarga</button>
    <button onclick="showTab('cuti')" id="tab-btn-cuti" class="tab-btn">Rekap Cuti</button>
    <button onclick="showTab('mutasi')" id="tab-btn-mutasi" class="tab-btn">Rekap Mutasi</button>
</div>

<style>
    .tab-btn { padding:8px 16px; border-radius:8px; border:1px solid #e2e8f0; background:#fff; color:#64748b; font-size:13px; font-weight:600; cursor:pointer; }
    .tab-btn.active { background:#2563EB; color:#fff; border-color:#2563EB; }
    .tab-pane { display:none; }
    .tab-pane.active { display:block; }
</style>

{{-- === Riwayat Pendidikan === --}}
<div id="tab-pendidikan" class="tab-pane active">
    @if(auth()->user()->isAdmin())
    <form action="{{ route('pegawai.pendidikan.store', $pegawai) }}" method="POST" class="card" style="padding:16px; margin-bottom:14px; display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:10px; align-items:end;">
        @csrf
        <div><label class="form-label">Jenjang</label><input name="jenjang" class="form-input" placeholder="S1" required></div>
        <div><label class="form-label">Institusi</label><input name="nama_institusi" class="form-input" required></div>
        <div><label class="form-label">Jurusan</label><input name="jurusan" class="form-input"></div>
        <div><label class="form-label">Tahun Lulus</label><input name="tahun_lulus" class="form-input" placeholder="2015"></div>
        <div><button class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-plus"></i> Tambah</button></div>
    </form>
    @endif
    <div class="card">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
                <th style="padding:10px 18px;">Jenjang</th><th style="padding:10px;">Institusi</th><th style="padding:10px;">Jurusan</th><th style="padding:10px;">Lulus</th>@if(auth()->user()->isAdmin())<th></th>@endif
            </tr></thead>
            <tbody>
                @forelse($pegawai->riwayatPendidikan as $r)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:10px 18px;font-weight:700;">{{ $r->jenjang }}</td>
                    <td style="padding:10px;">{{ $r->nama_institusi }}</td>
                    <td style="padding:10px;">{{ $r->jurusan ?? '-' }}</td>
                    <td style="padding:10px;">{{ $r->tahun_lulus ?? '-' }}</td>
                    @if(auth()->user()->isAdmin())
                    <td style="padding:10px;text-align:right;">
                        <form action="{{ route('pegawai.pendidikan.destroy', [$pegawai, $r]) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button></form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="5" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada riwayat pendidikan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- === Tunjangan Keluarga === --}}
<div id="tab-keluarga" class="tab-pane">
    @if(auth()->user()->isAdmin())
    <form action="{{ route('pegawai.keluarga.store', $pegawai) }}" method="POST" class="card" style="padding:16px; margin-bottom:14px; display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:10px; align-items:end;">
        @csrf
        <div><label class="form-label">Nama</label><input name="nama" class="form-input" required></div>
        <div><label class="form-label">Hubungan</label>
            <select name="hubungan" class="form-input" required>
                <option value="Istri">Istri</option><option value="Suami">Suami</option><option value="Anak">Anak</option>
            </select>
        </div>
        <div><label class="form-label">Tanggal Lahir</label><input type="date" name="tanggal_lahir" class="form-input"></div>
        <div><label class="form-label">Pekerjaan</label><input name="pekerjaan" class="form-input"></div>
        <div><button class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-plus"></i> Tambah</button></div>
    </form>
    @endif
    <div class="card">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
                <th style="padding:10px 18px;">Nama</th><th style="padding:10px;">Hubungan</th><th style="padding:10px;">Tgl Lahir</th><th style="padding:10px;">Pekerjaan</th>@if(auth()->user()->isAdmin())<th></th>@endif
            </tr></thead>
            <tbody>
                @forelse($pegawai->keluarga as $k)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:10px 18px;font-weight:700;">{{ $k->nama }}</td>
                    <td style="padding:10px;">{{ $k->hubungan }}</td>
                    <td style="padding:10px;">{{ $k->tanggal_lahir?->format('d-m-Y') ?? '-' }}</td>
                    <td style="padding:10px;">{{ $k->pekerjaan ?? '-' }}</td>
                    @if(auth()->user()->isAdmin())
                    <td style="padding:10px;text-align:right;">
                        <form action="{{ route('pegawai.keluarga.destroy', [$pegawai, $k]) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button></form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="5" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada data keluarga.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- === Rekap Cuti === --}}
<div id="tab-cuti" class="tab-pane">
    @if(auth()->user()->isAdmin())
    <form action="{{ route('pegawai.cuti.store', $pegawai) }}" method="POST" class="card" style="padding:16px; margin-bottom:14px; display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:10px; align-items:end;">
        @csrf
        <div><label class="form-label">Jenis Cuti</label>
            <select name="jenis_cuti" class="form-input" required>
                @foreach(['Tahunan','Sakit','Melahirkan','Besar','Alasan Penting','Diluar Tanggungan'] as $j)
                <option value="{{ $j }}">{{ $j }}</option>
                @endforeach
            </select>
        </div>
        <div><label class="form-label">Mulai</label><input type="date" name="tanggal_mulai" class="form-input" required></div>
        <div><label class="form-label">Selesai</label><input type="date" name="tanggal_selesai" class="form-input" required></div>
        <div><label class="form-label">No. Surat</label><input name="no_surat" class="form-input"></div>
        <div><button class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-plus"></i> Tambah</button></div>
    </form>
    @endif
    <div class="card">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
                <th style="padding:10px 18px;">Jenis</th><th style="padding:10px;">Periode</th><th style="padding:10px;">Hari</th><th style="padding:10px;">No. Surat</th>@if(auth()->user()->isAdmin())<th></th>@endif
            </tr></thead>
            <tbody>
                @forelse($pegawai->cuti as $c)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:10px 18px;font-weight:700;">{{ $c->jenis_cuti }}</td>
                    <td style="padding:10px;">{{ $c->tanggal_mulai->format('d-m-Y') }} s/d {{ $c->tanggal_selesai->format('d-m-Y') }}</td>
                    <td style="padding:10px;">{{ $c->jumlah_hari }} hari</td>
                    <td style="padding:10px;">{{ $c->no_surat ?? '-' }}</td>
                    @if(auth()->user()->isAdmin())
                    <td style="padding:10px;text-align:right;">
                        <form action="{{ route('pegawai.cuti.destroy', [$pegawai, $c]) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button></form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="5" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada data cuti.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- === Rekap Mutasi === --}}
<div id="tab-mutasi" class="tab-pane">
    @if(auth()->user()->isAdmin())
    <form action="{{ route('pegawai.mutasi.store', $pegawai) }}" method="POST" class="card" style="padding:16px; margin-bottom:14px; display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:10px; align-items:end;">
        @csrf
        <div><label class="form-label">Jenis</label>
            <select name="jenis_mutasi" class="form-input" required>
                <option value="Masuk">Masuk</option><option value="Keluar">Keluar</option><option value="Internal">Internal</option>
            </select>
        </div>
        <div><label class="form-label">Tanggal</label><input type="date" name="tanggal_mutasi" class="form-input" required></div>
        <div><label class="form-label">Asal</label><input name="asal" class="form-input"></div>
        <div><label class="form-label">Tujuan</label><input name="tujuan" class="form-input"></div>
        <div><label class="form-label">No. SK</label><input name="no_sk" class="form-input"></div>
        <div><button class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-plus"></i> Tambah</button></div>
    </form>
    @endif
    <div class="card">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
                <th style="padding:10px 18px;">Jenis</th><th style="padding:10px;">Tanggal</th><th style="padding:10px;">Asal → Tujuan</th><th style="padding:10px;">No. SK</th>@if(auth()->user()->isAdmin())<th></th>@endif
            </tr></thead>
            <tbody>
                @forelse($pegawai->mutasi as $m)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:10px 18px;font-weight:700;">{{ $m->jenis_mutasi }}</td>
                    <td style="padding:10px;">{{ $m->tanggal_mutasi->format('d-m-Y') }}</td>
                    <td style="padding:10px;">{{ $m->asal ?? '-' }} &rarr; {{ $m->tujuan ?? '-' }}</td>
                    <td style="padding:10px;">{{ $m->no_sk ?? '-' }}</td>
                    @if(auth()->user()->isAdmin())
                    <td style="padding:10px;text-align:right;">
                        <form action="{{ route('pegawai.mutasi.destroy', [$pegawai, $m]) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button></form>
                    </td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="5" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada data mutasi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function showTab(name) {
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        document.getElementById('tab-btn-' + name).classList.add('active');
    }
</script>
@endsection
