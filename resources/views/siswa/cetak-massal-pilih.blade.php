@extends('layouts.app')
@section('title', 'Cetak Massal Buku Induk')
@section('page-title', 'Cetak Massal - Buku Induk Siswa')

@section('content')
<form action="{{ route('siswa.cetak-massal') }}" method="POST" id="form-cetak-massal">
    @csrf

    <div class="card" style="padding:18px;margin-bottom:16px;">
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 12px;">Pilih Cepat</p>
        <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
            <div>
                <label class="form-label">Berdasarkan Kelas</label>
                <select id="filter-kelas-rombel" class="form-input">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelasRombelList as $kr)
                    @php [$kl, $rb] = explode('|', $kr); @endphp
                    <option value="{{ $kr }}">{{ $rb ? "$kl - $rb" : $kl }}</option>
                    @endforeach
                </select>
            </div>
            <button type="button" onclick="pilihByKelasRombel()" class="btn btn-secondary">Centang Kelas Ini</button>

            <div style="margin-left:6px;">
                <label class="form-label">Berdasarkan Angkatan</label>
                <select id="filter-angkatan" class="form-input">
                    <option value="">-- Pilih Angkatan --</option>
                    @foreach($angkatanList as $a)
                    <option value="{{ $a }}">Kelas {{ $a }}</option>
                    @endforeach
                </select>
            </div>
            <button type="button" onclick="pilihByAngkatan()" class="btn btn-secondary">Centang Angkatan Ini</button>

            <button type="button" onclick="pilihSemua()" class="btn btn-secondary">Centang Semua</button>
            <button type="button" onclick="kosongkanSemua()" class="btn btn-secondary" style="color:#dc2626;">Kosongkan</button>
        </div>
    </div>

    <div class="card" style="padding:0;overflow:hidden;">
        <div style="padding:14px 18px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">
            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;"><span id="jumlah-terpilih">0</span> siswa dipilih</p>
            <button type="submit" class="btn btn-primary"><i class="ti ti-download"></i> Unduh ZIP (PDF per siswa)</button>
        </div>
        <div>
            <table style="width:100%;border-collapse:collapse;">
                <thead style="position:sticky;top:0;background:#f8fafc;">
                    <tr>
                        <th style="padding:10px 18px;text-align:left;width:40px;"></th>
                        <th style="padding:10px 18px;text-align:left;font-size:11px;color:#64748b;">Nama</th>
                        <th style="padding:10px 18px;text-align:left;font-size:11px;color:#64748b;">Kelas</th>
                        <th style="padding:10px 18px;text-align:left;font-size:11px;color:#64748b;">NIS/NISN</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswaList as $i => $s)
                    <tr class="row-siswa" data-halaman="{{ intdiv($i, 20) }}" style="border-top:1px solid #f1f5f9;" data-kelas-rombel="{{ $s->kelas }}|{{ $s->rombel }}" data-kelas="{{ $s->kelas }}">
                        <td style="padding:8px 18px;">
                            <input type="checkbox" name="siswa_ids[]" value="{{ $s->id }}" class="cb-siswa" onchange="updateJumlah()">
                        </td>
                        <td style="padding:8px 18px;font-size:13px;color:#0f172a;">{{ $s->nama_lengkap }}</td>
                        <td style="padding:8px 18px;font-size:13px;color:#475569;">{{ $s->kelas }}{{ $s->rombel ? " - $s->rombel" : '' }}</td>
                        <td style="padding:8px 18px;font-size:12px;color:#94a3b8;">{{ $s->nis }} / {{ $s->nisn }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div id="nav-halaman" style="display:flex;align-items:center;justify-content:center;gap:6px;padding:14px;border-top:1px solid #e2e8f0;flex-wrap:wrap;"></div>
    </div>
</form>

<script>
const UKURAN_HALAMAN = 20;
let halamanSekarang = 0;

function totalHalaman() {
    return Math.max(1, Math.ceil(document.querySelectorAll('.row-siswa').length / UKURAN_HALAMAN));
}

function tampilkanHalaman(n) {
    const total = totalHalaman();
    halamanSekarang = Math.min(Math.max(0, n), total - 1);
    document.querySelectorAll('.row-siswa').forEach(row => {
        row.style.display = (parseInt(row.dataset.halaman) === halamanSekarang) ? '' : 'none';
    });
    renderNav(total);
}

function renderNav(total) {
    const nav = document.getElementById('nav-halaman');
    if (total <= 1) { nav.innerHTML = ''; return; }
    let html = `<button type="button" onclick="tampilkanHalaman(${halamanSekarang - 1})" class="btn btn-secondary btn-sm" ${halamanSekarang === 0 ? 'disabled' : ''}>&laquo; Sebelumnya</button>`;
    for (let i = 0; i < total; i++) {
        html += `<button type="button" onclick="tampilkanHalaman(${i})" class="btn btn-sm" style="min-width:32px;${i === halamanSekarang ? 'background:#1E3A5F;color:#fff;' : 'background:#f1f5f9;color:#475569;'}">${i + 1}</button>`;
    }
    html += `<button type="button" onclick="tampilkanHalaman(${halamanSekarang + 1})" class="btn btn-secondary btn-sm" ${halamanSekarang === total - 1 ? 'disabled' : ''}>Selanjutnya &raquo;</button>`;
    nav.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', () => tampilkanHalaman(0));
</script>

<script>
function updateJumlah() {
    const jumlah = document.querySelectorAll('.cb-siswa:checked').length;
    document.getElementById('jumlah-terpilih').textContent = jumlah;
}
function pilihByKelasRombel() {
    const val = document.getElementById('filter-kelas-rombel').value;
    if (!val) return;
    document.querySelectorAll(`tr[data-kelas-rombel="${val}"] .cb-siswa`).forEach(cb => cb.checked = true);
    updateJumlah();
}
function pilihByAngkatan() {
    const val = document.getElementById('filter-angkatan').value;
    if (!val) return;
    document.querySelectorAll(`tr[data-kelas="${val}"] .cb-siswa`).forEach(cb => cb.checked = true);
    updateJumlah();
}
function pilihSemua() {
    document.querySelectorAll('.cb-siswa').forEach(cb => cb.checked = true);
    updateJumlah();
}
function kosongkanSemua() {
    document.querySelectorAll('.cb-siswa').forEach(cb => cb.checked = false);
    updateJumlah();
}
document.getElementById('form-cetak-massal').addEventListener('submit', function (e) {
    if (document.querySelectorAll('.cb-siswa:checked').length === 0) {
        e.preventDefault();
        alert('Pilih minimal 1 siswa dulu.');
    }
});
</script>
@endsection
