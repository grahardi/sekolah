@php
    $s  = $siswa ?? null;
    $fv = fn($field, $default = '') => old($field, $s?->$field ?? $default);
    $pendidikanList = ['SD/MI','SMP/MTs','SMA/SMK/MA','D1','D2','D3','D4/S1','S2','S3','Tidak Sekolah'];
    $penghasilanList = [
        'Di bawah Rp 500.000','Rp 500.000 - Rp 1.000.000','Rp 1.000.000 - Rp 2.000.000',
        'Rp 2.000.000 - Rp 3.000.000','Rp 3.000.000 - Rp 5.000.000','Di atas Rp 5.000.000','Tidak Berpenghasilan',
    ];
@endphp

<div style="display:flex;gap:24px;align-items:flex-start;">
<div style="flex:2;min-width:0;display:flex;flex-direction:column;gap:20px;">

    <div class="card">
        <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-id-badge-2" style="font-size:16px;vertical-align:-3px;margin-right:6px;color:#1d4ed8;"></i> Identitas Diri</span></div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div style="grid-column:span 2;">
                <label class="form-label">Nama Lengkap <span style="color:#ef4444">*</span></label>
                <input type="text" name="nama_lengkap" value="{{ $fv('nama_lengkap') }}" class="form-input" required>
                @error('nama_lengkap')<p style="color:#ef4444;font-size:11px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">NISN <span style="color:#ef4444">*</span></label>
                <input type="text" name="nisn" value="{{ $fv('nisn') }}" class="form-input" style="font-family:monospace;" required>
                @error('nisn')<p style="color:#ef4444;font-size:11px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">NIS (Lokal)</label>
                <input type="text" name="nis" value="{{ $fv('nis') }}" class="form-input" style="font-family:monospace;">
            </div>
            <div>
                <label class="form-label">Jenis Kelamin <span style="color:#ef4444">*</span></label>
                <select name="jenis_kelamin" class="form-input" required>
                    <option value="">-- Pilih --</option>
                    <option value="L" {{ $fv('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ $fv('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="form-label">Agama <span style="color:#ef4444">*</span></label>
                <select name="agama" class="form-input" required>
                    @foreach(['Islam','Kristen','Katolik','Hindu','Buddha','Konghucu'] as $ag)
                        <option value="{{ $ag }}" {{ $fv('agama','Islam') == $ag ? 'selected' : '' }}>{{ $ag }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Tempat Lahir <span style="color:#ef4444">*</span></label>
                <input type="text" name="tempat_lahir" value="{{ $fv('tempat_lahir') }}" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Tanggal Lahir <span style="color:#ef4444">*</span></label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $s?->tanggal_lahir?->format('Y-m-d')) }}" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Anak ke-</label>
                <input type="number" name="anak_ke" value="{{ $fv('anak_ke') }}" min="1" max="20" class="form-input">
            </div>
            <div>
                <label class="form-label">Golongan Darah</label>
                <select name="golongan_darah" class="form-input">
                    @foreach(['Tidak Tahu','A','B','AB','O'] as $gd)
                        <option value="{{ $gd }}" {{ $fv('golongan_darah','Tidak Tahu') == $gd ? 'selected' : '' }}>{{ $gd }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">NIK</label>
                <input type="text" name="nik" value="{{ $fv('nik') }}" class="form-input" style="font-family:monospace;" maxlength="16">
            </div>
            <div>
                <label class="form-label">No. KK</label>
                <input type="text" name="no_kk" value="{{ $fv('no_kk') }}" class="form-input" style="font-family:monospace;" maxlength="16">
            </div>
            <div>
                <label class="form-label">No. Telepon Siswa</label>
                <input type="text" name="no_telepon" value="{{ $fv('no_telepon') }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ $fv('email') }}" class="form-input">
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-map-pin" style="font-size:16px;vertical-align:-3px;margin-right:6px;color:#1d4ed8;"></i> Alamat Tempat Tinggal</span></div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div style="grid-column:span 2;">
                <label class="form-label">Alamat (Jalan / Gg) <span style="color:#ef4444">*</span></label>
                <textarea name="alamat" rows="2" class="form-input" required>{{ $fv('alamat') }}</textarea>
            </div>
            <div><label class="form-label">RT</label><input type="text" name="rt" value="{{ $fv('rt') }}" class="form-input" maxlength="5" placeholder="001"></div>
            <div><label class="form-label">RW</label><input type="text" name="rw" value="{{ $fv('rw') }}" class="form-input" maxlength="5" placeholder="001"></div>
            <div><label class="form-label">Dusun / Desa</label><input type="text" name="dusun" value="{{ $fv('dusun') }}" class="form-input"></div>
            <div><label class="form-label">Kelurahan / Desa</label><input type="text" name="kelurahan" value="{{ $fv('kelurahan') }}" class="form-input"></div>
            <div><label class="form-label">Kecamatan</label><input type="text" name="kecamatan" value="{{ $fv('kecamatan') }}" class="form-input"></div>
            <div><label class="form-label">Kode Pos</label><input type="text" name="kode_pos" value="{{ $fv('kode_pos') }}" class="form-input" maxlength="5" style="font-family:monospace;"></div>
            <div><label class="form-label">Lintang (Latitude)</label><input type="number" step="0.0000001" name="lintang" value="{{ $fv('lintang') }}" class="form-input" placeholder="-7.2504"></div>
            <div><label class="form-label">Bujur (Longitude)</label><input type="number" step="0.0000001" name="bujur" value="{{ $fv('bujur') }}" class="form-input" placeholder="112.7688"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-school" style="font-size:16px;vertical-align:-3px;margin-right:6px;color:#1d4ed8;"></i> Data Sekolah</span></div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div>
                <label class="form-label">Kelas <span style="color:#ef4444">*</span></label>
                <select name="kelas" class="form-input" required>
                    <option value="">-- Pilih --</option>
                    @foreach(['7','8','9'] as $k)
                        <option value="{{ $k }}" {{ $fv('kelas') == $k ? 'selected' : '' }}>Kelas {{ $k }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="form-label">Rombel</label><input type="text" name="rombel" value="{{ $fv('rombel') }}" class="form-input" placeholder="mis: VII A"></div>
            <div>
                <label class="form-label">Diterima di Kelas</label>
                <input type="text" name="diterima_di_kelas" value="{{ $fv('diterima_di_kelas') }}" class="form-input" placeholder="mis: 7 A (kosongkan = ikut kelas saat ini)">
                <p style="font-size:11px;color:#94a3b8;margin-top:3px;">Isi manual kalau beda dari kelas sekarang (mis. siswa mutasi).</p>
            </div>
            <div>
                <label class="form-label">Tahun Masuk <span style="color:#ef4444">*</span></label>
                <input type="number" name="tahun_masuk" value="{{ $fv('tahun_masuk', date('Y')) }}" min="2000" max="{{ date('Y') }}" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Tanggal Diterima</label>
                <input type="date" name="tanggal_diterima" value="{{ old('tanggal_diterima', $s?->tanggal_diterima?->format('Y-m-d')) }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Status <span style="color:#ef4444">*</span></label>
                <select name="status" class="form-input" required>
                    @foreach(['aktif','lulus','keluar','pindah'] as $st)
                        <option value="{{ $st }}" {{ $fv('status','aktif') == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div><label class="form-label">Sekolah Asal (SD/MI)</label><input type="text" name="asal_sekolah" value="{{ $fv('asal_sekolah') }}" class="form-input"></div>
            <div><label class="form-label">No. STTB / Ijazah SD</label><input type="text" name="no_sttb_sd" value="{{ $fv('no_sttb_sd') }}" class="form-input" style="font-family:monospace;"></div>
            <div><label class="form-label">No. Sertifikat TKA</label><input type="text" name="no_un_sd" value="{{ $fv('no_un_sd') }}" class="form-input" style="font-family:monospace;"></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-man" style="font-size:16px;vertical-align:-3px;margin-right:6px;color:#1d4ed8;"></i> Data Ayah</span></div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div style="grid-column:span 2;"><label class="form-label">Nama Ayah</label><input type="text" name="nama_ayah" value="{{ $fv('nama_ayah') }}" class="form-input"></div>
            <div><label class="form-label">NIK Ayah</label><input type="text" name="nik_ayah" value="{{ $fv('nik_ayah') }}" class="form-input" maxlength="16" placeholder="16 digit"></div>
            <div><label class="form-label">Tahun Lahir</label><input type="number" name="tahun_lahir_ayah" value="{{ $fv('tahun_lahir_ayah') }}" min="1940" max="{{ date('Y') - 15 }}" class="form-input" placeholder="1980"></div>
            <div>
                <label class="form-label">Jenjang Pendidikan</label>
                <select name="pendidikan_ayah" class="form-input">
                    <option value="">-- Pilih --</option>
                    @foreach($pendidikanList as $p)<option value="{{ $p }}" {{ $fv('pendidikan_ayah') == $p ? 'selected' : '' }}>{{ $p }}</option>@endforeach
                </select>
            </div>
            <div><label class="form-label">Pekerjaan</label><input type="text" name="pekerjaan_ayah" value="{{ $fv('pekerjaan_ayah') }}" class="form-input"></div>
            <div>
                <label class="form-label">Penghasilan per Bulan</label>
                <select name="penghasilan_ayah" class="form-input">
                    <option value="">-- Pilih --</option>
                    @foreach($penghasilanList as $p)<option value="{{ $p }}" {{ $fv('penghasilan_ayah') == $p ? 'selected' : '' }}>{{ $p }}</option>@endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-woman" style="font-size:16px;vertical-align:-3px;margin-right:6px;color:#ec4899;"></i> Data Ibu</span></div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div style="grid-column:span 2;"><label class="form-label">Nama Ibu</label><input type="text" name="nama_ibu" value="{{ $fv('nama_ibu') }}" class="form-input"></div>
            <div><label class="form-label">NIK Ibu</label><input type="text" name="nik_ibu" value="{{ $fv('nik_ibu') }}" class="form-input" maxlength="16" placeholder="16 digit"></div>
            <div><label class="form-label">Tahun Lahir</label><input type="number" name="tahun_lahir_ibu" value="{{ $fv('tahun_lahir_ibu') }}" min="1940" max="{{ date('Y') - 15 }}" class="form-input" placeholder="1982"></div>
            <div>
                <label class="form-label">Jenjang Pendidikan</label>
                <select name="pendidikan_ibu" class="form-input">
                    <option value="">-- Pilih --</option>
                    @foreach($pendidikanList as $p)<option value="{{ $p }}" {{ $fv('pendidikan_ibu') == $p ? 'selected' : '' }}>{{ $p }}</option>@endforeach
                </select>
            </div>
            <div><label class="form-label">Pekerjaan</label><input type="text" name="pekerjaan_ibu" value="{{ $fv('pekerjaan_ibu') }}" class="form-input"></div>
            <div>
                <label class="form-label">Penghasilan per Bulan</label>
                <select name="penghasilan_ibu" class="form-input">
                    <option value="">-- Pilih --</option>
                    @foreach($penghasilanList as $p)<option value="{{ $p }}" {{ $fv('penghasilan_ibu') == $p ? 'selected' : '' }}>{{ $p }}</option>@endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-user-check" style="font-size:16px;vertical-align:-3px;margin-right:6px;color:#1d4ed8;"></i> Data Wali (jika berbeda dengan orang tua)</span></div>
        <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
            <div><label class="form-label">Nama Wali</label><input type="text" name="nama_wali" value="{{ $fv('nama_wali') }}" class="form-input"></div>
            <div><label class="form-label">Pekerjaan Wali</label><input type="text" name="pekerjaan_wali" value="{{ $fv('pekerjaan_wali') }}" class="form-input"></div>
            <div><label class="form-label">No. Telepon Orang Tua / Wali</label><input type="text" name="no_telepon_ortu" value="{{ $fv('no_telepon_ortu') }}" class="form-input"></div>
            <div><label class="form-label">Alamat Orang Tua / Wali</label><input type="text" name="alamat_ortu" value="{{ $fv('alamat_ortu') }}" class="form-input"></div>
        </div>
    </div>

</div>

<div style="flex:1;min-width:220px;display:flex;flex-direction:column;gap:20px;">
    <div class="card">
        <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-photo" style="font-size:16px;vertical-align:-3px;margin-right:6px;color:#1d4ed8;"></i> Foto Siswa</span></div>
        <div class="card-body" style="text-align:center;">
            <div style="width:120px;height:150px;margin:0 auto 14px;border-radius:10px;overflow:hidden;border:2px solid #e5e7eb;background:#f8fafc;">
                <img id="foto-preview"
                     src="{{ $s && $s->foto ? asset('storage/'.$s->foto) : 'https://ui-avatars.com/api/?name='.urlencode($s->nama_lengkap ?? 'S').'&background=dbeafe&color=1d4ed8&size=120' }}"
                     style="width:100%;height:100%;object-fit:cover;">
            </div>
            <label class="btn btn-secondary" style="cursor:pointer;font-size:12px;">
                <i class="ti ti-upload"></i> Pilih Foto
                <input type="file" name="foto" accept="image/*" style="display:none;" onchange="previewFoto(this)">
            </label>
            <p style="font-size:11px;color:#94a3b8;margin-top:8px;">JPG/PNG, maks 2MB<br>Rasio 3:4 (pas foto)</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-heart-rate-monitor" style="font-size:16px;vertical-align:-3px;margin-right:6px;color:#ef4444;"></i> Kesehatan</span></div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:12px;">
            <div><label class="form-label">Tinggi Badan (cm)</label><input type="number" name="tinggi_badan" value="{{ $fv('tinggi_badan') }}" min="50" max="250" class="form-input"></div>
            <div><label class="form-label">Berat Badan (kg)</label><input type="number" name="berat_badan" value="{{ $fv('berat_badan') }}" min="10" max="200" class="form-input"></div>
            <div><label class="form-label">Riwayat Penyakit</label><textarea name="riwayat_penyakit" rows="2" class="form-input" placeholder="Tulis jika ada...">{{ $fv('riwayat_penyakit') }}</textarea></div>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
function previewFoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => document.getElementById('foto-preview').src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
