<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Data Alumni - {{ $siswa->nama_lengkap }}</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; background:#f0fdf4; min-height:100vh; padding:20px; }
.wrap { max-width:480px; margin:0 auto; }
.card { background:#fff; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,.05); padding:22px; margin-bottom:16px; }
.form-label { display:block; font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; margin-bottom:8px; }
.form-input { width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; margin-bottom:14px; }
.opsi-kategori { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.opsi-btn { border:2px solid transparent; border-radius:12px; padding:16px 10px; text-align:center; cursor:pointer; transition:.15s; }
.opsi-btn i { font-size:26px; display:block; margin-bottom:6px; }
.opsi-btn p { font-size:12.5px; font-weight:700; margin:0; }
.opsi-btn[data-kategori="lanjut_sekolah"] { background:#eff6ff; color:#1d4ed8; }
.opsi-btn[data-kategori="pondok_pesantren"] { background:#f5f3ff; color:#7c3aed; }
.opsi-btn[data-kategori="bekerja"] { background:#fffbeb; color:#d97706; }
.opsi-btn[data-kategori="tidak_melanjutkan"] { background:#f8fafc; color:#64748b; }
.opsi-btn[data-kategori="lainnya"] { background:#fdf2f8; color:#be185d; grid-column:span 2; }
.opsi-btn.aktif { border-color:currentColor; box-shadow:0 2px 8px rgba(0,0,0,.08); }
.btn-primary { background:#16a34a; color:#fff; border:none; padding:13px 20px; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; width:100%; }
.btn-primary:disabled { background:#cbd5e1; cursor:not-allowed; }
</style>
</head>
<body>
<div class="wrap">
    <div style="text-align:center;margin-bottom:20px;">
        <p style="font-size:12px;color:#94a3b8;margin:0 0 4px;">{{ $sekolah->nama }}</p>
        <p style="font-size:18px;font-weight:700;color:#0f172a;margin:0 0 12px;">Data Alumni</p>
        <div style="display:flex;align-items:center;justify-content:center;gap:12px;">
            <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama_lengkap }}" style="width:54px;height:54px;border-radius:12px;object-fit:cover;background:#dcfce7;"
                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($siswa->nama_lengkap) }}&background=dcfce7&color=166534&size=54'">
            <div style="text-align:left;">
                <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">{{ $siswa->nama_lengkap }}</p>
                <p style="font-size:12px;color:#64748b;margin:2px 0 0;">NISN {{ $siswa->nisn }} &middot; Lulus {{ $siswa->tahun_lulus ?: '-' }}</p>
            </div>
        </div>
        <form action="{{ route('alumni-publik.keluar', $npsn) }}" method="POST" style="margin-top:10px;">
            @csrf
            <button type="submit" style="border:none;background:none;color:#94a3b8;font-size:11px;text-decoration:underline;cursor:pointer;">Bukan {{ $siswa->nama_lengkap }}? Ganti akun</button>
        </form>
    </div>

    @if(session('success'))
    <div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;text-align:center;"><i class="ti ti-check"></i> {{ session('success') }}</div>
    @endif

    <form action="{{ route('alumni-publik.simpan', $npsn) }}" method="POST" id="form-alumni">
        @csrf

        <div class="card">
            <p class="form-label">Sekarang kamu...</p>
            <div class="opsi-kategori">
                <div class="opsi-btn" data-kategori="lanjut_sekolah" onclick="pilihKategori('lanjut_sekolah')">
                    <i class="ti ti-school"></i><p>Lanjut Sekolah</p>
                </div>
                <div class="opsi-btn" data-kategori="pondok_pesantren" onclick="pilihKategori('pondok_pesantren')">
                    <i class="ti ti-building-mosque"></i><p>Pondok Pesantren</p>
                </div>
                <div class="opsi-btn" data-kategori="bekerja" onclick="pilihKategori('bekerja')">
                    <i class="ti ti-briefcase"></i><p>Bekerja</p>
                </div>
                <div class="opsi-btn" data-kategori="tidak_melanjutkan" onclick="pilihKategori('tidak_melanjutkan')">
                    <i class="ti ti-home"></i><p>Tidak Melanjutkan</p>
                </div>
                <div class="opsi-btn" data-kategori="lainnya" onclick="pilihKategori('lainnya')">
                    <i class="ti ti-dots"></i><p>Lainnya</p>
                </div>
            </div>
            <input type="hidden" name="alumni_kategori" id="input-kategori" required>
        </div>

        <div class="card" id="wrap-sekolah" style="display:none;">
            <label class="form-label">Pilih Sekolah</label>
            <select name="alumni_sekolah_tujuan_id" id="select-sekolah" class="form-input" onchange="document.getElementById('wrap-sekolah-manual').style.display = this.value === 'lainnya' ? 'block' : 'none';">
                <option value="">-- Pilih --</option>
                @foreach($sekolahTujuanList as $st)
                <option value="{{ $st->id }}">{{ $st->nama_sekolah }}{{ $st->jenjang ? " ({$st->jenjang})" : '' }}</option>
                @endforeach
                <option value="lainnya">Lainnya (isi manual)</option>
            </select>
            <div id="wrap-sekolah-manual" style="display:none;">
                <label class="form-label">Nama Sekolah</label>
                <input type="text" id="input-sekolah-manual" class="form-input" placeholder="Tulis nama sekolah...">
            </div>
            <label class="form-label">Jurusan (opsional)</label>
            <input type="text" name="alumni_jurusan" class="form-input" placeholder="mis. IPA, Teknik Komputer Jaringan, dll">
        </div>

        <div class="card" id="wrap-pondok" style="display:none;">
            <label class="form-label">Nama Pondok Pesantren (opsional)</label>
            <input type="text" id="input-pondok-manual" class="form-input" placeholder="Tulis nama pondok pesantren...">
        </div>

        <div class="card" id="wrap-bekerja" style="display:none;">
            <label class="form-label">Kerja Apa / Di Mana?</label>
            <input type="text" id="input-bekerja" class="form-input" placeholder="mis. Karyawan Toko Berkah Jaya, Turen">
        </div>

        <div class="card" id="wrap-tidak-lanjut" style="display:none;">
            <label class="form-label">Alasan (opsional)</label>
            <input type="text" id="input-tidak-lanjut" class="form-input" placeholder="mis. Bantu usaha orang tua dulu">
        </div>

        <div class="card" id="wrap-lainnya" style="display:none;">
            <label class="form-label">Ceritakan kegiatanmu sekarang</label>
            <input type="text" id="input-lainnya" class="form-input" placeholder="Tulis kegiatanmu sekarang...">
        </div>

        <button type="submit" class="btn-primary" id="btn-simpan" disabled>Simpan Data</button>
    </form>
</div>

<script>
function pilihKategori(kategori) {
    document.querySelectorAll('.opsi-btn').forEach(el => el.classList.remove('aktif'));
    document.querySelector(`[data-kategori="${kategori}"]`).classList.add('aktif');
    document.getElementById('input-kategori').value = kategori;
    document.getElementById('btn-simpan').disabled = false;

    document.getElementById('wrap-sekolah').style.display = kategori === 'lanjut_sekolah' ? 'block' : 'none';
    document.getElementById('wrap-pondok').style.display = kategori === 'pondok_pesantren' ? 'block' : 'none';
    document.getElementById('wrap-bekerja').style.display = kategori === 'bekerja' ? 'block' : 'none';
    document.getElementById('wrap-tidak-lanjut').style.display = kategori === 'tidak_melanjutkan' ? 'block' : 'none';
    document.getElementById('wrap-lainnya').style.display = kategori === 'lainnya' ? 'block' : 'none';
}

document.getElementById('form-alumni').addEventListener('submit', function (e) {
    const kategori = document.getElementById('input-kategori').value;

    const hiddenManual = document.createElement('input');
    hiddenManual.type = 'hidden';
    hiddenManual.name = 'alumni_sekolah_tujuan_manual';

    const hiddenKeterangan = document.createElement('input');
    hiddenKeterangan.type = 'hidden';
    hiddenKeterangan.name = 'alumni_keterangan';

    if (kategori === 'lanjut_sekolah') {
        const select = document.getElementById('select-sekolah');
        if (select.value === 'lainnya') {
            hiddenManual.value = document.getElementById('input-sekolah-manual').value;
            select.removeAttribute('name');
        }
    } else if (kategori === 'pondok_pesantren') {
        hiddenManual.value = document.getElementById('input-pondok-manual').value;
    } else if (kategori === 'bekerja') {
        hiddenKeterangan.value = document.getElementById('input-bekerja').value;
    } else if (kategori === 'tidak_melanjutkan') {
        hiddenKeterangan.value = document.getElementById('input-tidak-lanjut').value;
    } else if (kategori === 'lainnya') {
        hiddenKeterangan.value = document.getElementById('input-lainnya').value;
    }

    if (hiddenManual.value) this.appendChild(hiddenManual);
    if (hiddenKeterangan.value) this.appendChild(hiddenKeterangan);
});
</script>
</body>
</html>
