@extends('layouts.app')
@section('title', 'Scan KK & Akta')
@section('page-title', 'Scan KK & Akta (OCR)')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif
@if(session('warning'))
<div style="background:#fef9c3;color:#854d0e;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('warning') }}</div>
@endif

<p style="font-size:13px;color:#64748b;margin:-8px 0 16px;max-width:600px;">
    Scan otomatis dokumen KK & Akta Lahir pakai AI, ambil data nama ayah/ibu dan nomor akta.
    Siswa yang <strong>sudah pernah discan</strong> otomatis dilewati (hemat kuota API) kecuali kamu centang "Paksa scan ulang".
</p>

<form action="{{ route('siswa.scan-kk.bulk') }}" method="POST" id="form-scan">
    @csrf

    <div class="card" style="padding:18px;margin-bottom:16px;">
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
            <button type="button" onclick="pilihBelumScan()" class="btn btn-secondary">Centang yang Belum Discan</button>
            <button type="button" onclick="kosongkanSemua()" class="btn btn-secondary" style="color:#dc2626;">Kosongkan</button>

            <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:#64748b;margin-left:auto;">
                <input type="checkbox" name="paksa_ulang" value="1"> Paksa scan ulang (yg sudah discan ikut di-scan lagi)
            </label>
        </div>
    </div>

    <div class="card" style="padding:0;overflow:hidden;">
        <div style="padding:14px 18px;border-bottom:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">
            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;"><span id="jumlah-terpilih">0</span> siswa dipilih</p>
            <button type="submit" class="btn btn-primary" onclick="return confirm('Mulai scan? Proses ini butuh waktu tergantung jumlah siswa yg dipilih.')"><i class="ti ti-scan"></i> Mulai Scan</button>
        </div>
        <table style="width:100%;border-collapse:collapse;">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="padding:10px 18px;text-align:left;width:40px;"></th>
                    <th style="padding:10px 18px;text-align:left;font-size:11px;color:#64748b;">Nama</th>
                    <th style="padding:10px 18px;text-align:left;font-size:11px;color:#64748b;">Kelas</th>
                    <th style="padding:10px 18px;text-align:left;font-size:11px;color:#64748b;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siswaList as $s)
                @php
                $hasil = $s->scanKkHasil;
                $sudahScan = $hasil && $hasil->sudahDiscan();
                @endphp
                <tr style="border-top:1px solid #f1f5f9;" data-kelas-rombel="{{ $s->kelas }}|{{ $s->rombel }}" data-sudah-scan="{{ $sudahScan ? '1' : '0' }}">
                    <td style="padding:8px 18px;"><input type="checkbox" name="siswa_ids[]" value="{{ $s->id }}" class="cb-siswa" onchange="updateJumlah()"></td>
                    <td style="padding:8px 18px;font-size:13px;">
                        @if($sudahScan)
                        <a href="{{ route('siswa.scan-kk.show', $s) }}" style="color:#0f172a;text-decoration:none;">{{ $s->nama_lengkap }}</a>
                        @else
                        <span style="color:#0f172a;">{{ $s->nama_lengkap }}</span>
                        @endif
                    </td>
                    <td style="padding:8px 18px;font-size:13px;color:#475569;">{{ $s->kelas }}{{ $s->rombel ? " - $s->rombel" : '' }}</td>
                    <td style="padding:8px 18px;">
                        @if(! $s->arsipBerkas?->kartu_keluarga && ! $s->arsipBerkas?->akta_lahir)
                        <span style="font-size:11px;color:#94a3b8;">Belum ada berkas</span>
                        @elseif($sudahScan)
                        <span style="background:#dcfce7;color:#166534;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;"><i class="ti ti-check"></i> Sudah discan</span>
                        @else
                        <span style="background:#f1f5f9;color:#64748b;font-size:11px;font-weight:600;padding:3px 9px;border-radius:20px;">Belum discan</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</form>

<script>
function updateJumlah() {
    document.getElementById('jumlah-terpilih').textContent = document.querySelectorAll('.cb-siswa:checked').length;
}
function pilihByKelasRombel() {
    const val = document.getElementById('filter-kelas-rombel').value;
    if (!val) return;
    document.querySelectorAll(`tr[data-kelas-rombel="${val}"] .cb-siswa`).forEach(cb => cb.checked = true);
    updateJumlah();
}
function pilihBelumScan() {
    document.querySelectorAll('tr[data-sudah-scan="0"] .cb-siswa').forEach(cb => cb.checked = true);
    updateJumlah();
}
function kosongkanSemua() {
    document.querySelectorAll('.cb-siswa').forEach(cb => cb.checked = false);
    updateJumlah();
}
document.getElementById('form-scan').addEventListener('submit', function (e) {
    if (document.querySelectorAll('.cb-siswa:checked').length === 0) {
        e.preventDefault();
        alert('Pilih minimal 1 siswa dulu.');
    }
});
</script>
@endsection
