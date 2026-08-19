<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Pengajuan Perubahan Data - {{ $siswa->nama_lengkap }}</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; background:#f8fafc; min-height:100vh; padding:20px; }
.wrap { max-width:640px; margin:0 auto; }
.card { background:#fff; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,.05); padding:20px; margin-bottom:16px; }
.form-label { display:block; font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; margin-bottom:6px; }
.baris { display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 0; border-bottom:1px solid #f1f5f9; }
.baris:last-child { border-bottom:none; }
.form-input { width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:6px; font-size:13px; }
.btn-pena { border:none; background:#eff6ff; color:#1d4ed8; width:30px; height:30px; border-radius:7px; cursor:pointer; flex-shrink:0; }
.btn-primary { background:#1E3A5F; color:#fff; border:none; padding:12px 20px; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; }
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
                'nama_lengkap' => 'Nama Lengkap', 'nik' => 'NIK', 'tempat_lahir' => 'Tempat Lahir',
                'tanggal_lahir' => 'Tanggal Lahir', 'agama' => 'Agama', 'no_telepon' => 'No. Telepon', 'email' => 'Email',
            ],
            'Alamat' => [
                'alamat' => 'Alamat', 'rt' => 'RT', 'rw' => 'RW', 'dusun' => 'Dusun',
                'kelurahan' => 'Kelurahan', 'kecamatan' => 'Kecamatan', 'kode_pos' => 'Kode Pos',
            ],
            'Data Ayah' => [
                'nama_ayah' => 'Nama Ayah', 'nik_ayah' => 'NIK Ayah', 'tahun_lahir_ayah' => 'Tahun Lahir',
                'pendidikan_ayah' => 'Pendidikan', 'pekerjaan_ayah' => 'Pekerjaan', 'penghasilan_ayah' => 'Penghasilan',
            ],
            'Data Ibu' => [
                'nama_ibu' => 'Nama Ibu', 'nik_ibu' => 'NIK Ibu', 'tahun_lahir_ibu' => 'Tahun Lahir',
                'pendidikan_ibu' => 'Pendidikan', 'pekerjaan_ibu' => 'Pekerjaan', 'penghasilan_ibu' => 'Penghasilan',
            ],
            'Data Wali' => [
                'nama_wali' => 'Nama Wali', 'pekerjaan_wali' => 'Pekerjaan Wali',
                'no_telepon_ortu' => 'No. Telepon Orang Tua/Wali', 'alamat_ortu' => 'Alamat Orang Tua/Wali',
            ],
        ];
        @endphp

        @foreach($grup as $judulGrup => $fields)
        <div class="card">
            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 8px;">{{ $judulGrup }}</p>
            @foreach($fields as $field => $label)
            @php $nilaiSekarang = $field === 'tanggal_lahir' ? $siswa->tanggal_lahir?->format('d-m-Y') : $siswa->{$field}; @endphp
            <div class="baris">
                <div style="flex:1;" id="tampil-{{ $field }}">
                    <p class="form-label" style="margin-bottom:2px;">{{ $label }}</p>
                    <p style="font-size:14px;color:#0f172a;margin:0;">{{ $nilaiSekarang ?: '-' }}</p>
                </div>
                <div style="flex:1;display:none;" id="edit-{{ $field }}">
                    <label class="form-label">{{ $label }} (baru)</label>
                    <input type="{{ $field === 'tanggal_lahir' ? 'date' : 'text' }}" name="perubahan[{{ $field }}]" class="form-input"
                        value="{{ $field === 'tanggal_lahir' ? $siswa->tanggal_lahir?->format('Y-m-d') : $siswa->{$field} }}">
                </div>
                <button type="button" class="btn-pena" onclick="document.getElementById('tampil-{{ $field }}').style.display='none';document.getElementById('edit-{{ $field }}').style.display='block';this.style.display='none';">
                    <i class="ti ti-pencil" style="font-size:14px;"></i>
                </button>
            </div>
            @endforeach
        </div>
        @endforeach

        <div class="card">
            <label class="form-label">Catatan (opsional)</label>
            <textarea name="catatan_siswa" class="form-input" rows="2" placeholder="Jelaskan alasan perubahan kalau perlu...">{{ $pengajuan->catatan_siswa }}</textarea>
        </div>

        <button type="submit" class="btn-primary" style="width:100%;">Kirim Pengajuan Perubahan</button>
    </form>
</div>
</body>
</html>
