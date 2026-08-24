<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pengajuan Perubahan Data - {{ $siswa->nama_lengkap }}</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; background:#f1f5f9; min-height:100vh; padding:20px; }
.wrap { max-width:640px; margin:0 auto; }
.card { background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.06); margin-bottom:18px; overflow:hidden; border-top:4px solid var(--warna, #94a3b8); }
.card-judul { padding:14px 18px; display:flex; align-items:center; gap:8px; }
.card-isi { padding:4px 18px 8px; }
.form-label { display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:6px; }
.baris { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:12px 10px; border-radius:8px; margin:4px 0; }
.baris.genap { background:#fafbfc; }
.form-input { width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:13px; }
.btn-pena { border:none; background:var(--warna-terang, #eff6ff); color:var(--warna, #1d4ed8); width:32px; height:32px; border-radius:8px; cursor:pointer; flex-shrink:0; font-size:14px; }
.btn-primary { background:#1E3A5F; color:#fff; border:none; padding:13px 20px; border-radius:10px; font-size:14px; font-weight:700; cursor:pointer; box-shadow:0 4px 12px rgba(30,58,95,.25); }
.btn-primary:disabled { background:#cbd5e1; box-shadow:none; cursor:not-allowed; }
</style>
</head>
<body>
<div class="wrap">
    <div style="text-align:center;margin-bottom:20px;">
        <p style="font-size:12px;color:#94a3b8;margin:0 0 4px;">{{ $sekolah->nama }}</p>
        <p style="font-size:18px;font-weight:700;color:#0f172a;margin:0;">Pengajuan Perubahan Data</p>
        <p style="font-size:13px;color:#64748b;margin:6px 0 0;">{{ $siswa->nama_lengkap }} &middot; {{ $siswa->kelas }}{{ $siswa->rombel ? " - $siswa->rombel" : '' }}</p>
        <form action="{{ route('pengajuan-publik.keluar', $npsn) }}" method="POST" style="margin-top:8px;">
            @csrf
            <button type="submit" style="border:none;background:none;color:#94a3b8;font-size:11px;text-decoration:underline;cursor:pointer;">Bukan {{ $siswa->nama_lengkap }}? Ganti akun</button>
        </form>
    </div>

    @if(session('success'))
    <div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('error') }}</div>
    @endif

    @if($pengajuan->status === 'menunggu_approval')
    <div style="background:#fef9c3;color:#854d0e;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
        <i class="ti ti-clock"></i> Pengajuan sebelumnya sedang <strong>menunggu persetujuan</strong> wali kelas. Mengisi ulang akan menggantikan pengajuan lama.
    </div>
    @elseif($pengajuan->status === 'sudah_approve')
    <div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
        <i class="ti ti-circle-check"></i> Pengajuan sebelumnya sudah <strong>disetujui</strong> dan diterapkan.
    </div>
    @endif

    <form action="{{ route('pengajuan-publik.simpan', $npsn) }}" method="POST">
        @csrf

        @php
        $grup = [
            'Data Pribadi' => [
                'warna' => '#2563eb', 'warnaTerang' => '#eff6ff', 'icon' => 'ti-user',
                'fields' => [
                    'nama_lengkap' => 'Nama Lengkap', 'nik' => 'NIK', 'tempat_lahir' => 'Tempat Lahir',
                    'tanggal_lahir' => 'Tanggal Lahir', 'agama' => 'Agama', 'no_telepon' => 'No. Telepon', 'email' => 'Email',
                ],
            ],
            'Alamat' => [
                'warna' => '#16a34a', 'warnaTerang' => '#f0fdf4', 'icon' => 'ti-map-pin',
                'fields' => [
                    'alamat' => 'Alamat', 'rt' => 'RT', 'rw' => 'RW', 'dusun' => 'Dusun',
                    'kelurahan' => 'Kelurahan', 'kecamatan' => 'Kecamatan', 'kode_pos' => 'Kode Pos',
                ],
            ],
            'Data Ayah' => [
                'warna' => '#0891b2', 'warnaTerang' => '#ecfeff', 'icon' => 'ti-man',
                'fields' => [
                    'nama_ayah' => 'Nama Ayah', 'nik_ayah' => 'NIK Ayah', 'tahun_lahir_ayah' => 'Tahun Lahir',
                    'pendidikan_ayah' => 'Pendidikan', 'pekerjaan_ayah' => 'Pekerjaan', 'penghasilan_ayah' => 'Penghasilan',
                ],
            ],
            'Data Ibu' => [
                'warna' => '#db2777', 'warnaTerang' => '#fdf2f8', 'icon' => 'ti-woman',
                'fields' => [
                    'nama_ibu' => 'Nama Ibu', 'nik_ibu' => 'NIK Ibu', 'tahun_lahir_ibu' => 'Tahun Lahir',
                    'pendidikan_ibu' => 'Pendidikan', 'pekerjaan_ibu' => 'Pekerjaan', 'penghasilan_ibu' => 'Penghasilan',
                ],
            ],
            'Data Wali' => [
                'warna' => '#ca8a04', 'warnaTerang' => '#fefce8', 'icon' => 'ti-users',
                'fields' => [
                    'nama_wali' => 'Nama Wali', 'pekerjaan_wali' => 'Pekerjaan Wali',
                    'no_telepon_ortu' => 'No. Telepon Orang Tua/Wali', 'alamat_ortu' => 'Alamat Orang Tua/Wali',
                ],
            ],
        ];
        @endphp

        @foreach($grup as $judulGrup => $g)
        <div class="card" style="--warna:{{ $g['warna'] }};--warna-terang:{{ $g['warnaTerang'] }};">
            <div class="card-judul" style="background:{{ $g['warnaTerang'] }};">
                <i class="ti {{ $g['icon'] }}" style="color:{{ $g['warna'] }};font-size:16px;"></i>
                <p style="font-size:13px;font-weight:700;color:{{ $g['warna'] }};margin:0;">{{ $judulGrup }}</p>
            </div>
            <div class="card-isi">
            @php $i = 0; @endphp
            @foreach($g['fields'] as $field => $label)
            @php
            $nilaiSekarang = $field === 'tanggal_lahir' ? $siswa->tanggal_lahir?->format('d-m-Y') : $siswa->{$field};
            $i++;
            $opsiAgama = ['Islam','Kristen','Katholik','Hindu','Budha','Khonghucu','Kepercayaan kpd Tuhan YME','Lainnya'];
            $opsiPenghasilan = ['Kurang dari Rp. 500,000','Rp. 500,000 - Rp. 999,999','Rp. 1,000,000 - Rp. 1,999,999','Rp. 2,000,000 - Rp. 4,999,999','Rp. 5,000,000 - Rp. 10,000,000','Rp. 10,000,000 - Rp. 20,000,000','Lebih dari Rp. 20,000,000'];
            $opsiPendidikan = ['Tidak Sekolah','Putus SD','SD / Sederajat','SMP / Sederajat','SMA / Sederajat','D1','D2','D3','D4/S1','S2','S3'];
            @endphp
            <div class="baris {{ $i % 2 === 0 ? 'genap' : '' }}">
                <div style="flex:1;" id="tampil-{{ $field }}">
                    <p class="form-label" style="margin-bottom:2px;">{{ $label }}</p>
                    <p style="font-size:14px;color:#0f172a;margin:0;">{{ $nilaiSekarang ?: '-' }}</p>
                </div>
                <div style="flex:1;display:none;" id="edit-{{ $field }}">
                    <label class="form-label">{{ $label }} (baru)</label>
                    @if($field === 'agama')
                    <select name="perubahan[{{ $field }}]" class="form-input">
                        @foreach($opsiAgama as $opt)<option value="{{ $opt }}" {{ $siswa->agama === $opt ? 'selected' : '' }}>{{ $opt }}</option>@endforeach
                    </select>
                    @elseif(in_array($field, ['penghasilan_ayah', 'penghasilan_ibu']))
                    <select name="perubahan[{{ $field }}]" class="form-input">
                        <option value="">-- Pilih --</option>
                        @foreach($opsiPenghasilan as $opt)<option value="{{ $opt }}" {{ $siswa->{$field} === $opt ? 'selected' : '' }}>{{ $opt }}</option>@endforeach
                    </select>
                    @elseif(in_array($field, ['pendidikan_ayah', 'pendidikan_ibu']))
                    <select name="perubahan[{{ $field }}]" class="form-input">
                        <option value="">-- Pilih --</option>
                        @foreach($opsiPendidikan as $opt)<option value="{{ $opt }}" {{ $siswa->{$field} === $opt ? 'selected' : '' }}>{{ $opt }}</option>@endforeach
                    </select>
                    @else
                    <input type="{{ $field === 'tanggal_lahir' ? 'date' : 'text' }}" name="perubahan[{{ $field }}]" class="form-input"
                        value="{{ $field === 'tanggal_lahir' ? $siswa->tanggal_lahir?->format('Y-m-d') : $siswa->{$field} }}">
                    @endif
                </div>
                <button type="button" class="btn-pena" onclick="document.getElementById('tampil-{{ $field }}').style.display='none';document.getElementById('edit-{{ $field }}').style.display='block';this.style.display='none';">
                    <i class="ti ti-pencil"></i>
                </button>
            </div>
            @endforeach
            </div>
        </div>
        @endforeach

        <div class="card" style="--warna:#64748b;">
            <div class="card-judul">
                <i class="ti ti-message-2" style="color:#64748b;font-size:16px;"></i>
                <p style="font-size:13px;font-weight:700;color:#334155;margin:0;">Catatan</p>
            </div>
            <div class="card-isi" style="padding-bottom:16px;">
                <textarea name="catatan_siswa" class="form-input" rows="2" placeholder="Jelaskan alasan perubahan kalau perlu...">{{ $pengajuan->catatan_siswa }}</textarea>
            </div>
        </div>

        <div class="card" style="--warna:#d97706;padding:0;">
            <div class="card-isi" style="padding:16px 18px;background:#fffbeb;">
                <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
                    <input type="checkbox" id="cb-yakin" style="margin-top:2px;width:16px;height:16px;flex-shrink:0;" onchange="document.getElementById('btn-kirim').disabled = !this.checked;">
                    <span style="font-size:13px;color:#92400e;"><i class="ti ti-alert-triangle"></i> <strong>Saya yakin</strong> data yang saya isi/ubah di atas sudah benar. Perubahan ini akan diperiksa oleh wali kelas sebelum diterapkan.</span>
                </label>
            </div>
        </div>

        <button type="submit" id="btn-kirim" class="btn-primary" style="width:100%;" disabled>Kirim Pengajuan Perubahan</button>
    </form>
</div>
</body>
</html>
