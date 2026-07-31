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
    <button onclick="showTab('mengajar')" id="tab-btn-mengajar" class="tab-btn">Tugas Mengajar</button>
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

{{-- === TUGAS MENGAJAR === --}}
<div id="tab-mengajar" class="tab-pane">
    <div style="margin-bottom:14px;">
        <input type="text" id="cari-mapel" placeholder="Cari mata pelajaran..." class="form-input" style="max-width:400px;" oninput="filterMapel(this.value)">
    </div>
    <div id="mengajar-loading" style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">Memuat data...</div>
    <div id="mengajar-container"></div>
</div>

<script>
    const PEGAWAI_ID = {{ $pegawai->id }};
    const URL_DATA = '{{ route('erapor.tugas-mengajar.data', $pegawai) }}';
    const URL_TOGGLE = '{{ route('erapor.tugas-mengajar.toggle', $pegawai) }}';
    let mengajarData = null;
    let mengajarLoaded = false;

    function showTab(name) {
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
        document.getElementById('tab-' + name).classList.add('active');
        document.getElementById('tab-btn-' + name).classList.add('active');

        if (name === 'mengajar' && !mengajarLoaded) {
            loadMengajarData();
        }
    }

    function loadMengajarData() {
        fetch(URL_DATA)
            .then(r => r.json())
            .then(json => {
                mengajarLoaded = true;
                document.getElementById('mengajar-loading').style.display = 'none';
                if (json.error) {
                    document.getElementById('mengajar-container').innerHTML =
                        '<p style="color:#dc2626;font-size:13px;padding:16px;background:#fef2f2;border-radius:8px;">' + json.error + '</p>';
                    return;
                }
                mengajarData = json.mapels;
                renderMengajar(mengajarData);
            });
    }

    function renderMengajar(mapels) {
        const container = document.getElementById('mengajar-container');
        container.innerHTML = mapels.map(m => `
            <div class="mapel-accordion" data-nama="${m.nama.toLowerCase()}">
                <button type="button" class="mapel-header" onclick="toggleAccordion(${m.mapel_id})">
                    <span>${m.nama}</span>
                    <span style="display:flex;align-items:center;gap:8px;">
                        ${m.jumlah_diampu > 0 ? `<span class="badge badge-aktif">${m.jumlah_diampu} Kelas</span>` : ''}
                        <i class="ti ti-chevron-down" id="chev-${m.mapel_id}"></i>
                    </span>
                </button>
                <div class="mapel-body" id="body-${m.mapel_id}" style="display:none;">
                    <div class="kelas-grid">
                        ${m.kelas_list.map(k => renderToggleBtn(m.mapel_id, k)).join('')}
                    </div>
                </div>
            </div>
        `).join('');
    }

    function renderToggleBtn(mapelId, k) {
        const idAttr = `btn-${mapelId}-${k.kelas}-${k.rombel ?? ''}`;
        if (k.assigned_to_other) {
            return `<button type="button" class="kelas-toggle disabled" title="Sudah diampu oleh: ${k.assigned_to_other}" disabled>
                        <i class="ti ti-lock"></i> ${k.label}
                    </button>`;
        }
        const activeClass = k.assigned_to_me ? 'active' : '';
        const icon = k.assigned_to_me ? 'ti-circle-check' : 'ti-user';
        return `<button type="button" id="${idAttr}" class="kelas-toggle ${activeClass}"
                    onclick="toggleKelas(${mapelId}, '${k.kelas}', '${k.rombel ?? ''}', this)">
                    <i class="ti ${icon}"></i> ${k.label}
                </button>`;
    }

    function toggleAccordion(mapelId) {
        const body = document.getElementById('body-' + mapelId);
        const chev = document.getElementById('chev-' + mapelId);
        const isOpen = body.style.display !== 'none';
        body.style.display = isOpen ? 'none' : 'block';
        chev.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
    }

    function toggleKelas(mapelId, kelas, rombel, btn) {
        btn.disabled = true;
        fetch(URL_TOGGLE, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            },
            body: JSON.stringify({ mata_pelajaran_id: mapelId, kelas: kelas, rombel: rombel }),
        })
            .then(async (r) => {
                const json = await r.json();
                if (!r.ok) { alert(json.error || 'Gagal.'); btn.disabled = false; return; }

                btn.classList.toggle('active');
                const icon = btn.querySelector('i');
                icon.className = btn.classList.contains('active') ? 'ti ti-circle-check' : 'ti ti-user';
                btn.disabled = false;

                // update badge jumlah kelas di header mapel
                const mapel = mengajarData.find(m => m.mapel_id === mapelId);
                const kInfo = mapel.kelas_list.find(k => k.kelas === kelas && (k.rombel ?? '') === rombel);
                kInfo.assigned_to_me = json.status === 'added';
                mapel.jumlah_diampu = mapel.kelas_list.filter(k => k.assigned_to_me).length;
                renderMengajar(mengajarData);
                // buka lagi accordion yang barusan dipakai
                document.getElementById('body-' + mapelId).style.display = 'block';
            });
    }

    function filterMapel(q) {
        q = q.toLowerCase();
        document.querySelectorAll('.mapel-accordion').forEach(el => {
            el.style.display = el.dataset.nama.includes(q) ? 'block' : 'none';
        });
    }
</script>

<style>
    .mapel-accordion { border:1px solid #e2e8f0; border-radius:10px; margin-bottom:8px; overflow:hidden; }
    .mapel-header { width:100%; display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:#fff; border:none; font-size:13px; font-weight:600; color:#1e293b; cursor:pointer; }
    .mapel-header:hover { background:#f8fafc; }
    .mapel-body { padding:14px 16px; background:#f8fafc; border-top:1px solid #e2e8f0; }
    .kelas-grid { display:flex; flex-wrap:wrap; gap:8px; }
    .kelas-toggle { display:inline-flex; align-items:center; gap:5px; padding:7px 12px; border-radius:8px; border:1px solid #e2e8f0; background:#fff; color:#64748b; font-size:12px; font-weight:600; cursor:pointer; }
    .kelas-toggle:hover:not(.disabled) { border-color:#2563EB; color:#2563EB; }
    .kelas-toggle.active { background:#0F5132; color:#fff; border-color:#0F5132; }
    .kelas-toggle.disabled { background:#f1f5f9; color:#cbd5e1; cursor:not-allowed; }
</style>
@endsection
