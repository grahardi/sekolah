@php
    $s = $survey;
    $existingQuestions = $s ? $s->pertanyaans : collect();
    $existingTargets = $s ? $s->target_kelas_array : [];
@endphp

<div class="card" style="padding:20px;margin-bottom:16px;">
    <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:14px;margin-bottom:14px;">
        <div>
            <label class="form-label">Judul Survey <span style="color:#ef4444">*</span></label>
            <input type="text" name="judul" value="{{ old('judul', $s->judul ?? '') }}" class="form-input" placeholder="mis. DCM Semester Ganjil 2026" required>
        </div>
        <div>
            <label class="form-label">Jenis</label>
            <select name="jenis" class="form-input">
                @foreach(['DCM','AUM','Custom'] as $j)
                <option value="{{ $j }}" {{ old('jenis', $s->jenis ?? 'Custom') === $j ? 'selected' : '' }}>{{ $j }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="draft" {{ old('status', $s->status ?? 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="aktif" {{ old('status', $s->status ?? '') === 'aktif' ? 'selected' : '' }}>Aktif (bisa diisi siswa)</option>
                <option value="ditutup" {{ old('status', $s->status ?? '') === 'ditutup' ? 'selected' : '' }}>Ditutup</option>
            </select>
        </div>
    </div>
    <div style="margin-bottom:14px;">
        <label class="form-label">Deskripsi / Petunjuk Pengisian</label>
        <textarea name="deskripsi" class="form-input" rows="2" placeholder="Contoh: Isi sejujur-jujurnya, jawaban dijamin kerahasiaannya.">{{ old('deskripsi', $s->deskripsi ?? '') }}</textarea>
    </div>
    <div>
        <label class="form-label">Target Kelas (kosongkan semua = berlaku utk semua kelas aktif)</label>
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:6px;">
            @forelse($kelasList as $k)
            <label style="display:flex;align-items:center;gap:5px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:6px 10px;font-size:12px;cursor:pointer;">
                <input type="checkbox" name="target_kelas[]" value="{{ $k }}" {{ in_array($k, old('target_kelas', $existingTargets)) ? 'checked' : '' }}>
                {{ $k }}
            </label>
            @empty
            <p style="font-size:12px;color:#94a3b8;">Belum ada data kelas siswa aktif di Buku Induk.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="card" style="padding:20px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
        <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">Daftar Pertanyaan</p>
        <button type="button" onclick="tambahPertanyaan()" class="btn btn-secondary btn-sm"><i class="ti ti-plus"></i> Tambah Pertanyaan</button>
    </div>

    <div id="pertanyaan-container"></div>
</div>

{{-- Template 1 baris pertanyaan (di-clone via JS) --}}
<template id="pertanyaan-template">
    <div class="pertanyaan-row" style="border:1px solid #e2e8f0;border-radius:10px;padding:14px;margin-bottom:12px;">
        <div style="display:flex;gap:10px;align-items:start;">
            <span style="background:#eff6ff;color:#2563EB;font-weight:700;font-size:12px;border-radius:6px;padding:4px 8px;margin-top:6px;" class="nomor-urut">#</span>
            <div style="flex:1;display:grid;grid-template-columns:2fr 1fr 1fr;gap:10px;">
                <div style="grid-column:span 3;">
                    <textarea name="__TEKS__" class="form-input" rows="2" placeholder="Tulis pertanyaan..." required></textarea>
                </div>
                <div>
                    <select name="__TIPE__" class="form-input tipe-select" onchange="toggleOpsi(this)">
                        <option value="pilihan_ganda">Pilihan Ganda</option>
                        <option value="checklist">Checklist (bisa lebih dari satu)</option>
                        <option value="skala">Skala 1-5</option>
                        <option value="esai">Esai / Teks Bebas</option>
                    </select>
                </div>
                <div>
                    <input type="text" name="__KATEGORI__" class="form-input" placeholder="Kategori (opsional), mis: Pribadi">
                </div>
                <div class="opsi-wrapper">
                    <textarea name="__OPSI__" class="form-input" rows="2" placeholder="Satu pilihan per baris"></textarea>
                </div>
            </div>
            <button type="button" onclick="this.closest('.pertanyaan-row').remove(); renumberPertanyaan();" class="btn btn-danger btn-sm" style="margin-top:6px;"><i class="ti ti-trash"></i></button>
        </div>
    </div>
</template>

<script>
    let pertanyaanIndex = 0;

    function tambahPertanyaan(data = null) {
        const template = document.getElementById('pertanyaan-template');
        const clone = template.content.cloneNode(true);
        const idx = pertanyaanIndex++;

        clone.querySelectorAll('[name="__TEKS__"]').forEach(el => { el.name = `pertanyaan[${idx}][teks]`; if (data) el.value = data.teks_pertanyaan; });
        clone.querySelectorAll('[name="__TIPE__"]').forEach(el => { el.name = `pertanyaan[${idx}][tipe]`; if (data) el.value = data.tipe_jawaban; });
        clone.querySelectorAll('[name="__KATEGORI__"]').forEach(el => { el.name = `pertanyaan[${idx}][kategori]`; if (data) el.value = data.kategori ?? ''; });
        clone.querySelectorAll('[name="__OPSI__"]').forEach(el => {
            el.name = `pertanyaan[${idx}][opsi]`;
            if (data && data.opsi) el.value = data.opsi.join("\n");
        });

        document.getElementById('pertanyaan-container').appendChild(clone);
        renumberPertanyaan();

        const lastRow = document.getElementById('pertanyaan-container').lastElementChild;
        toggleOpsi(lastRow.querySelector('.tipe-select'));
    }

    function toggleOpsi(select) {
        const row = select.closest('.pertanyaan-row');
        const opsiWrapper = row.querySelector('.opsi-wrapper');
        opsiWrapper.style.display = ['pilihan_ganda', 'checklist'].includes(select.value) ? 'block' : 'none';
    }

    function renumberPertanyaan() {
        document.querySelectorAll('#pertanyaan-container .nomor-urut').forEach((el, i) => {
            el.textContent = '#' + (i + 1);
        });
    }

    // Muat pertanyaan yang sudah ada (kalau mode edit)
    const existingPertanyaans = {!! $existingQuestions->map(fn($p) => [
        'teks_pertanyaan' => $p->teks_pertanyaan,
        'tipe_jawaban' => $p->tipe_jawaban,
        'kategori' => $p->kategori,
        'opsi' => $p->opsi,
    ])->toJson() !!};

    if (existingPertanyaans.length > 0) {
        existingPertanyaans.forEach(p => tambahPertanyaan(p));
    } else {
        tambahPertanyaan();
    }
</script>
