@extends('layouts.erapor')
@section('title', 'Penugasan Guru')
@section('page-title', 'Wali Kelas & Penugasan Guru')

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Guru diambil dari data Kepegawaian, kelas diambil dari data Buku Induk. Import Excel massal menyusul.
</p>

<div style="display:flex;gap:6px;margin-bottom:16px;flex-wrap:wrap;">
    <button onclick="showTab('wali')" id="tab-btn-wali" class="tab-btn active">Wali Kelas</button>
    <button onclick="showTab('pengajar')" id="tab-btn-pengajar" class="tab-btn">Guru Pengajar</button>
    <button onclick="showTab('ekskul')" id="tab-btn-ekskul" class="tab-btn">Ekstrakurikuler</button>
    <button onclick="showTab('kokurikuler')" id="tab-btn-kokurikuler" class="tab-btn">Kokurikuler (P5)</button>
</div>

<style>
    .tab-btn { padding:8px 16px; border-radius:8px; border:1px solid #e2e8f0; background:#fff; color:#64748b; font-size:13px; font-weight:600; cursor:pointer; }
    .tab-btn.active { background:#2563EB; color:#fff; border-color:#2563EB; }
    .tab-pane { display:none; }
    .tab-pane.active { display:block; }
    .assign-form { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:10px; align-items:end; }
</style>

@if($tahunAjarans->isEmpty())
<div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px;margin-bottom:16px;">
    <p style="font-size:13px;color:#92400e;margin:0;">Belum ada Tahun Ajaran. <a href="{{ route('erapor.tahun-ajaran') }}" style="text-decoration:underline;">Tambah dulu di sini</a> sebelum bisa menetapkan penugasan.</p>
</div>
@endif

<div id="tab-wali" class="tab-pane active">
    <form action="{{ route('erapor.wali-kelas.store') }}" method="POST" class="card assign-form" style="padding:16px;margin-bottom:14px;">
        @csrf
        <div><label class="form-label">Tahun Ajaran</label>
            <select name="tahun_ajaran_id" class="form-input" required>
                @foreach($tahunAjarans as $t)<option value="{{ $t->id }}">{{ $t->label }}</option>@endforeach
            </select>
        </div>
        <div><label class="form-label">Guru</label>
            <select name="pegawai_id" class="form-input" required>
                <option value="">-- Pilih guru --</option>
                @foreach($guruList as $g)<option value="{{ $g->id }}">{{ $g->nama_lengkap }}</option>@endforeach
            </select>
        </div>
        <div><label class="form-label">Kelas - Rombel</label>
            <select name="kelas_rombel" class="form-input kelas-select" required onchange="splitKelasRombel(this, 'wali')">
                <option value="">-- Pilih kelas --</option>
                @foreach($kelasList as $k)@php [$kl,$rb]=explode('|',$k);@endphp<option value="{{ $k }}">{{ $rb ? "$kl - $rb" : $kl }}</option>@endforeach
            </select>
            <input type="hidden" name="kelas" id="wali-kelas">
            <input type="hidden" name="rombel" id="wali-rombel">
        </div>
        <div><button class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-plus"></i> Tambah</button></div>
    </form>
    <div class="card">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
                <th style="padding:10px 18px;">Guru</th><th style="padding:10px;">Kelas</th><th style="padding:10px;">Tahun Ajaran</th><th></th>
            </tr></thead>
            <tbody>
                @forelse($waliKelas as $w)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:10px 18px;font-weight:700;">{{ $w->pegawai->nama_lengkap }}</td>
                    <td style="padding:10px;">{{ $w->kelas_lengkap }}</td>
                    <td style="padding:10px;color:#94a3b8;">{{ $w->tahunAjaran->label }}</td>
                    <td style="padding:10px;text-align:right;">
                        <form action="{{ route('erapor.wali-kelas.destroy', $w) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada wali kelas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="tab-pengajar" class="tab-pane">
    <form action="{{ route('erapor.guru-pengajar.store') }}" method="POST" class="card assign-form" style="padding:16px;margin-bottom:14px;">
        @csrf
        <div><label class="form-label">Tahun Ajaran</label>
            <select name="tahun_ajaran_id" class="form-input" required>
                @foreach($tahunAjarans as $t)<option value="{{ $t->id }}">{{ $t->label }}</option>@endforeach
            </select>
        </div>
        <div><label class="form-label">Guru</label>
            <select name="pegawai_id" class="form-input" required>
                <option value="">-- Pilih guru --</option>
                @foreach($guruList as $g)<option value="{{ $g->id }}">{{ $g->nama_lengkap }}</option>@endforeach
            </select>
        </div>
        <div><label class="form-label">Mata Pelajaran</label>
            <select name="mata_pelajaran_id" class="form-input" required>
                <option value="">-- Pilih mapel --</option>
                @foreach($mapelList as $m)<option value="{{ $m->id }}">{{ $m->nama }}</option>@endforeach
            </select>
        </div>
        <div><label class="form-label">Kelas - Rombel</label>
            <select name="kelas_rombel" class="form-input kelas-select" required onchange="splitKelasRombel(this, 'pengajar')">
                <option value="">-- Pilih kelas --</option>
                @foreach($kelasList as $k)@php [$kl,$rb]=explode('|',$k);@endphp<option value="{{ $k }}">{{ $rb ? "$kl - $rb" : $kl }}</option>@endforeach
            </select>
            <input type="hidden" name="kelas" id="pengajar-kelas">
            <input type="hidden" name="rombel" id="pengajar-rombel">
        </div>
        <div><button class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-plus"></i> Tambah</button></div>
    </form>
    <div class="card">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
                <th style="padding:10px 18px;">Guru</th><th style="padding:10px;">Mapel</th><th style="padding:10px;">Kelas</th><th></th>
            </tr></thead>
            <tbody>
                @forelse($guruPengajars as $g)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:10px 18px;font-weight:700;">{{ $g->pegawai->nama_lengkap }}</td>
                    <td style="padding:10px;">{{ $g->mataPelajaran->nama }}</td>
                    <td style="padding:10px;">{{ $g->kelas_lengkap }}</td>
                    <td style="padding:10px;text-align:right;">
                        <form action="{{ route('erapor.guru-pengajar.destroy', $g) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada penugasan guru pengajar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="tab-ekskul" class="tab-pane">
    <form action="{{ route('erapor.guru-ekstrakurikuler.store') }}" method="POST" class="card assign-form" style="padding:16px;margin-bottom:14px;">
        @csrf
        <div><label class="form-label">Tahun Ajaran</label>
            <select name="tahun_ajaran_id" class="form-input" required>
                @foreach($tahunAjarans as $t)<option value="{{ $t->id }}">{{ $t->label }}</option>@endforeach
            </select>
        </div>
        <div><label class="form-label">Guru Pembina</label>
            <select name="pegawai_id" class="form-input" required>
                <option value="">-- Pilih guru --</option>
                @foreach($guruList as $g)<option value="{{ $g->id }}">{{ $g->nama_lengkap }}</option>@endforeach
            </select>
        </div>
        <div><label class="form-label">Nama Ekstrakurikuler</label><input name="nama_ekstrakurikuler" class="form-input" placeholder="mis. Pramuka" required></div>
        <div><button class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-plus"></i> Tambah</button></div>
    </form>
    <div class="card">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
                <th style="padding:10px 18px;">Guru Pembina</th><th style="padding:10px;">Ekstrakurikuler</th><th></th>
            </tr></thead>
            <tbody>
                @forelse($guruEkstrakurikulers as $e)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:10px 18px;font-weight:700;">{{ $e->pegawai->nama_lengkap }}</td>
                    <td style="padding:10px;">{{ $e->nama_ekstrakurikuler }}</td>
                    <td style="padding:10px;text-align:right;">
                        <form action="{{ route('erapor.guru-ekstrakurikuler.destroy', $e) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada guru ekstrakurikuler.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="tab-kokurikuler" class="tab-pane">
    <form action="{{ route('erapor.guru-kokurikuler.store') }}" method="POST" class="card assign-form" style="padding:16px;margin-bottom:14px;">
        @csrf
        <div><label class="form-label">Tahun Ajaran</label>
            <select name="tahun_ajaran_id" class="form-input" required>
                @foreach($tahunAjarans as $t)<option value="{{ $t->id }}">{{ $t->label }}</option>@endforeach
            </select>
        </div>
        <div><label class="form-label">Guru Pembina P5</label>
            <select name="pegawai_id" class="form-input" required>
                <option value="">-- Pilih guru --</option>
                @foreach($guruList as $g)<option value="{{ $g->id }}">{{ $g->nama_lengkap }}</option>@endforeach
            </select>
        </div>
        <div><label class="form-label">Tema P5 (opsional)</label><input name="tema_p5" class="form-input" placeholder="mis. Gaya Hidup Berkelanjutan"></div>
        <div><label class="form-label">Kelas - Rombel</label>
            <select name="kelas_rombel" class="form-input kelas-select" required onchange="splitKelasRombel(this, 'kokurikuler')">
                <option value="">-- Pilih kelas --</option>
                @foreach($kelasList as $k)@php [$kl,$rb]=explode('|',$k);@endphp<option value="{{ $k }}">{{ $rb ? "$kl - $rb" : $kl }}</option>@endforeach
            </select>
            <input type="hidden" name="kelas" id="kokurikuler-kelas">
            <input type="hidden" name="rombel" id="kokurikuler-rombel">
        </div>
        <div><button class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-plus"></i> Tambah</button></div>
    </form>
    <div class="card">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
                <th style="padding:10px 18px;">Guru Pembina</th><th style="padding:10px;">Tema P5</th><th style="padding:10px;">Kelas</th><th></th>
            </tr></thead>
            <tbody>
                @forelse($guruKokurikulers as $k)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:10px 18px;font-weight:700;">{{ $k->pegawai->nama_lengkap }}</td>
                    <td style="padding:10px;">{{ $k->tema_p5 ?? '-' }}</td>
                    <td style="padding:10px;">{{ $k->kelas_lengkap }}</td>
                    <td style="padding:10px;text-align:right;">
                        <form action="{{ route('erapor.guru-kokurikuler.destroy', $k) }}" method="POST" onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button></form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada guru kokurikuler.</td></tr>
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
    function splitKelasRombel(select, prefix) {
        const [kelas, rombel] = select.value.split('|');
        document.getElementById(prefix + '-kelas').value = kelas || '';
        document.getElementById(prefix + '-rombel').value = rombel || '';
    }
</script>
@endsection
