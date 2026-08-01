@extends('layouts.erapor')
@section('title', 'Buat Penilaian')
@section('page-title', 'Buat Penilaian Baru')

@section('header-actions')
    <a href="{{ route('erapor.penilaian.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<form action="{{ route('erapor.penilaian.store') }}" method="POST" class="card" style="max-width:680px;margin:0 auto;padding:24px;" id="form-penilaian">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
        <div>
            <label class="form-label">Tahun Ajaran <span style="color:#ef4444">*</span></label>
            <select name="tahun_ajaran_id" id="tahun_ajaran_id" class="form-input" required>
                @foreach($tahunAjarans as $t)<option value="{{ $t->id }}" {{ $t->is_aktif ? 'selected' : '' }}>{{ $t->label }}</option>@endforeach
            </select>
        </div>

        @if($guruSaya)
        <input type="hidden" name="guru_id" value="{{ $guruSaya->id }}">
        <div>
            <label class="form-label">Guru</label>
            <input type="text" value="{{ $guruSaya->nama }} (kamu)" class="form-input" disabled style="background:#f8fafc;">
        </div>
        @else
        <div>
            <label class="form-label">Guru <span style="color:#ef4444">*</span></label>
            <select name="guru_id" class="form-input" required>
                <option value="">-- Pilih guru --</option>
                @foreach($guruList as $g)<option value="{{ $g->id }}">{{ $g->nama }}</option>@endforeach
            </select>
        </div>
        @endif

        <div>
            <label class="form-label">Mata Pelajaran <span style="color:#ef4444">*</span></label>
            <select name="mata_pelajaran_id" id="mata_pelajaran_id" class="form-input" required onchange="muatTp(); saringKelas();">
                <option value="">-- Pilih mapel --</option>
                @foreach($mapelList as $m)<option value="{{ $m->id }}">{{ $m->nama }}</option>@endforeach
            </select>
            @if($penugasanSaya !== null && $mapelList->isEmpty())
            <p style="font-size:11px;color:#dc2626;margin-top:4px;">Kamu belum ditugaskan mengajar mapel apapun. Hubungi admin sekolah.</p>
            @endif
        </div>
        <div>
            <label class="form-label">Kelas - Rombel <span style="color:#ef4444">*</span></label>
            <select name="kelas_rombel" id="kelas_rombel" class="form-input" required onchange="muatTp()">
                <option value="">-- Pilih kelas --</option>
                @foreach($kelasList as $k)@php [$kl,$rb]=explode('|',$k);@endphp<option value="{{ $k }}" data-mapel="{{ $penugasanSaya !== null ? $penugasanSaya->where('kelas', $kl)->where('rombel', $rb ?: null)->pluck('mata_pelajaran_id')->implode(',') : '' }}">{{ $rb ? "$kl - $rb" : $kl }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Semester <span style="color:#ef4444">*</span></label>
            <select name="semester" id="semester" class="form-input" required onchange="muatTp()">
                <option value="1">Ganjil</option>
                <option value="2">Genap</option>
            </select>
        </div>
        <div>
            <label class="form-label">Tanggal Penilaian</label>
            <input type="date" name="tanggal_penilaian" class="form-input">
        </div>
    </div>

    <div style="margin-bottom:16px;">
        <label class="form-label">Nama Penilaian <span style="color:#ef4444">*</span></label>
        <input type="text" name="nama_penilaian" class="form-input" placeholder="mis. Ulangan Harian Bab 1" required>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
        <div>
            <label class="form-label">Jenis Penilaian <span style="color:#ef4444">*</span></label>
            <select name="subjenis_penilaian" id="subjenis_penilaian" class="form-input" required onchange="muatTp(); aturBobotOtomatis();">
                <option value="Sumatif TP">Sumatif - TP</option>
                <option value="Sumatif Tengah Semester">Penilaian Tengah Semester</option>
                <option value="Sumatif Akhir Semester">Penilaian Semester Akhir</option>
            </select>
        </div>
        <div>
            <label class="form-label">Bobot <span style="color:#ef4444">*</span></label>
            <input type="number" name="bobot_penilaian" id="bobot_penilaian" class="form-input" value="1" min="1" max="100" required>
            <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Penilaian Semester Akhir otomatis diisi 2 (boleh diubah manual).</p>
        </div>
    </div>

    <div id="wrap-tp" style="display:none;margin-bottom:20px;">
        <label class="form-label">Tujuan Pembelajaran yang Diuji</label>
        <div id="tp-list" style="display:flex;flex-direction:column;gap:6px;margin-top:6px;">
            <p style="font-size:12px;color:#94a3b8;">Pilih mapel, kelas, dan semester dulu di atas.</p>
        </div>
        <p style="font-size:11px;color:#94a3b8;margin-top:6px;">
            Belum ada TP yang cocok? <a href="{{ route('erapor.tp.create') }}" target="_blank" style="color:#2563EB;">Buat TP baru di tab lain</a>, lalu pilih ulang mapel/kelas di atas.
        </p>
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;"><i class="ti ti-device-floppy"></i> Simpan &amp; Lanjut Input Nilai</button>
</form>

<script>
    function toggleSubjenis() { muatTp(); }

    function aturBobotOtomatis() {
        const subjenis = document.getElementById('subjenis_penilaian').value;
        if (subjenis === 'Sumatif Akhir Semester') {
            document.getElementById('bobot_penilaian').value = 2;
        }
    }

    function muatTp() {
        const subjenis = document.getElementById('subjenis_penilaian').value;
        if (subjenis !== 'Sumatif TP') {
            document.getElementById('wrap-tp').style.display = 'none';
            return;
        }

        const mapelId = document.getElementById('mata_pelajaran_id').value;
        const kelasRombel = document.getElementById('kelas_rombel').value;
        const semester = document.getElementById('semester').value;
        const tahunAjaranId = document.getElementById('tahun_ajaran_id').value;
        if (!mapelId || !kelasRombel) return;

        const [kelas, rombel] = kelasRombel.split('|');
        document.getElementById('wrap-tp').style.display = 'block';

        const params = new URLSearchParams({ mata_pelajaran_id: mapelId, kelas, rombel: rombel || '', semester, tahun_ajaran_id: tahunAjaranId });
        fetch('{{ route("erapor.penilaian.tp-untuk-konteks") }}?' + params.toString())
            .then(r => r.json())
            .then(tps => {
                const container = document.getElementById('tp-list');
                if (tps.length === 0) {
                    container.innerHTML = '<p style="font-size:12px;color:#94a3b8;">Belum ada TP untuk kombinasi mapel/kelas/semester ini.</p>';
                    return;
                }
                container.innerHTML = tps.map(tp => `
                    <label style="display:flex;align-items:start;gap:8px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:8px 10px;font-size:12px;cursor:pointer;">
                        <input type="checkbox" name="tp_ids[]" value="${tp.id}" style="margin-top:2px;">
                        <span>${tp.kode_tp ? '<strong>' + tp.kode_tp + '</strong> - ' : ''}${tp.deskripsi_tp}</span>
                    </label>
                `).join('');
            });
    }

    // Guru: cuma tampilkan pilihan kelas yg sesuai dgn mapel yg dipilih
    function saringKelas() {
        const kelasSelect = document.getElementById('kelas_rombel');
        const mapelId = document.getElementById('mata_pelajaran_id').value;
        if (!mapelId) return;
        Array.from(kelasSelect.options).forEach(opt => {
            if (!opt.value) return;
            const mapelValid = (opt.dataset.mapel || '').split(',');
            opt.hidden = mapelValid.length > 0 && mapelValid[0] !== '' && !mapelValid.includes(mapelId);
        });
        kelasSelect.value = '';
    }
</script>
@endsection
