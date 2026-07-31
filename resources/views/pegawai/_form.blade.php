@php $p = $pegawai ?? null; $fv = fn($field, $default = '') => old($field, $p->{$field} ?? $default); @endphp

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
    <div>
        <label class="form-label">Nama Lengkap <span style="color:#ef4444">*</span></label>
        <input type="text" name="nama_lengkap" value="{{ $fv('nama_lengkap') }}" class="form-input" required>
    </div>
    <div>
        <label class="form-label">NIP / NUPTK</label>
        <input type="text" name="nip_nuptk" value="{{ $fv('nip_nuptk') }}" class="form-input" placeholder="Kosongkan kalau belum ada">
    </div>
    <div>
        <label class="form-label">Jenis Kelamin <span style="color:#ef4444">*</span></label>
        <select name="jenis_kelamin" class="form-input" required>
            <option value="L" {{ $fv('jenis_kelamin') === 'L' ? 'selected' : '' }}>Laki-laki</option>
            <option value="P" {{ $fv('jenis_kelamin') === 'P' ? 'selected' : '' }}>Perempuan</option>
        </select>
    </div>
    <div>
        <label class="form-label">Tempat, Tanggal Lahir</label>
        <div style="display:flex;gap:8px;">
            <input type="text" name="tempat_lahir" value="{{ $fv('tempat_lahir') }}" class="form-input" placeholder="Tempat">
            <input type="date" name="tanggal_lahir" value="{{ $fv('tanggal_lahir') }}" class="form-input">
        </div>
    </div>
</div>

<div style="border-top:1px solid #f1f5f9; margin: 18px 0; padding-top:18px;">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
        <div>
            <label class="form-label">Status Kepegawaian <span style="color:#ef4444">*</span></label>
            <select name="jenis_kepegawaian" id="jenis_kepegawaian" class="form-input" required onchange="toggleAsnFields()">
                @foreach($jenisList as $j)
                <option value="{{ $j }}" {{ $fv('jenis_kepegawaian', 'GTT') === $j ? 'selected' : '' }}>{{ $j }}</option>
                @endforeach
            </select>
            <p style="font-size:11px;color:#94a3b8;margin-top:4px;">ASN: PNS, PPPK &middot; Non-ASN: GTT, PTT, GTY, PTY, Lainnya</p>
        </div>
        <div>
            <label class="form-label">Status Aktif <span style="color:#ef4444">*</span></label>
            <select name="status_aktif" class="form-input" required>
                @foreach(['Aktif','Cuti','Nonaktif','Pensiun','Pindah'] as $s)
                <option value="{{ $s }}" {{ $fv('status_aktif', 'Aktif') === $s ? 'selected' : '' }}>{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Jabatan</label>
            <input type="text" name="jabatan" value="{{ $fv('jabatan') }}" class="form-input" placeholder="mis. Guru Mapel IPA">
        </div>
        <div>
            <label class="form-label">Unit Kerja</label>
            <input type="text" name="unit_kerja" value="{{ $fv('unit_kerja') }}" class="form-input" placeholder="mis. Tata Usaha, Guru Mapel">
        </div>
    </div>
</div>

<div id="asn-fields" style="border-top:1px solid #f1f5f9; margin-bottom:18px; padding-top:18px;">
    <p style="font-size:12px;font-weight:700;color:#2563EB;margin:0 0 12px;">Data Khusus ASN (PNS/PPPK)</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
        <div>
            <label class="form-label">Golongan</label>
            <input type="text" name="golongan" value="{{ $fv('golongan') }}" class="form-input" placeholder="mis. III/a">
        </div>
        <div>
            <label class="form-label">Pangkat</label>
            <input type="text" name="pangkat" value="{{ $fv('pangkat') }}" class="form-input" placeholder="mis. Penata Muda">
        </div>
        <div>
            <label class="form-label">TMT CPNS</label>
            <input type="date" name="tmt_cpns" value="{{ $fv('tmt_cpns') }}" class="form-input">
        </div>
        <div>
            <label class="form-label">TMT PNS</label>
            <input type="date" name="tmt_pns" value="{{ $fv('tmt_pns') }}" class="form-input">
        </div>
        <div style="grid-column:span 2;">
            <label class="form-label">No. SK Pangkat Terakhir</label>
            <input type="text" name="no_sk_pangkat" value="{{ $fv('no_sk_pangkat') }}" class="form-input">
        </div>
    </div>
</div>

<div style="border-top:1px solid #f1f5f9; padding-top:18px;">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div>
            <label class="form-label">Pendidikan Terakhir</label>
            <input type="text" name="pendidikan_terakhir" value="{{ $fv('pendidikan_terakhir') }}" class="form-input" placeholder="mis. S1 Pendidikan Matematika">
        </div>
        <div>
            <label class="form-label">Tanggal Masuk Kerja</label>
            <input type="date" name="tanggal_masuk" value="{{ $fv('tanggal_masuk') }}" class="form-input">
        </div>
        <div>
            <label class="form-label">No. HP / WhatsApp</label>
            <input type="text" name="no_hp" value="{{ $fv('no_hp') }}" class="form-input">
        </div>
        <div>
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ $fv('email') }}" class="form-input">
        </div>
        <div style="grid-column:span 2;">
            <label class="form-label">Alamat</label>
            <input type="text" name="alamat" value="{{ $fv('alamat') }}" class="form-input">
        </div>
    </div>
</div>

<script>
    function toggleAsnFields() {
        const jenis = document.getElementById('jenis_kepegawaian').value;
        const asnBlock = document.getElementById('asn-fields');
        asnBlock.style.display = (jenis === 'PNS' || jenis === 'PPPK') ? 'block' : 'none';
    }
    document.addEventListener('DOMContentLoaded', toggleAsnFields);
</script>
