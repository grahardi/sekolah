@extends('layouts.erapor')
@section('title', 'Tugas Mengajar - ' . $guru->nama)
@section('page-title', 'Tugas Mengajar: ' . $guru->nama)

@section('header-actions')
    <a href="{{ route('erapor.guru.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div style="margin-bottom:14px;">
    <input type="text" id="cari-mapel" placeholder="Cari mata pelajaran..." class="form-input" style="max-width:400px;" oninput="filterMapel(this.value)">
</div>
<div id="mengajar-loading" style="padding:30px;text-align:center;color:#94a3b8;font-size:13px;">Memuat data...</div>
<div id="mengajar-container"></div>

<script>
    const URL_DATA = '{{ route('erapor.tugas-mengajar.data', $guru) }}';
    const URL_TOGGLE = '{{ route('erapor.tugas-mengajar.toggle', $guru) }}';
    let mengajarData = null;

    document.addEventListener('DOMContentLoaded', loadMengajarData);

    function loadMengajarData() {
        fetch(URL_DATA)
            .then(r => r.json())
            .then(json => {
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
        if (k.assigned_to_other) {
            return `<button type="button" class="kelas-toggle disabled" title="Sudah diampu oleh: ${k.assigned_to_other}" disabled>
                        <i class="ti ti-lock"></i> ${k.label}
                    </button>`;
        }
        const activeClass = k.assigned_to_me ? 'active' : '';
        const icon = k.assigned_to_me ? 'ti-circle-check' : 'ti-user';
        return `<button type="button" class="kelas-toggle ${activeClass}"
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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            },
            body: JSON.stringify({ mata_pelajaran_id: mapelId, kelas: kelas, rombel: rombel }),
        })
            .then(async (r) => {
                const json = await r.json();
                if (!r.ok) { alert(json.error || 'Gagal.'); btn.disabled = false; return; }

                const mapel = mengajarData.find(m => m.mapel_id === mapelId);
                const kInfo = mapel.kelas_list.find(k => k.kelas === kelas && (k.rombel ?? '') === rombel);
                kInfo.assigned_to_me = json.status === 'added';
                mapel.jumlah_diampu = mapel.kelas_list.filter(k => k.assigned_to_me).length;
                renderMengajar(mengajarData);
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
