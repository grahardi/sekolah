@csrf
@if(isset($asset)) @method('PUT') @endif

<div class="card" style="padding:20px;max-width:640px;">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
        <div>
            <label class="form-label">Kode Barang <span style="color:#ef4444">*</span></label>
            <input type="text" name="kode_barang" class="form-input" required value="{{ old('kode_barang', $asset->kode_barang ?? $nextKode ?? '') }}">
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                <option value="baik" {{ old('status', $asset->status ?? 'baik') === 'baik' ? 'selected' : '' }}>Baik</option>
                <option value="rusak" {{ old('status', $asset->status ?? '') === 'rusak' ? 'selected' : '' }}>Rusak</option>
                <option value="dalam_perbaikan" {{ old('status', $asset->status ?? '') === 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
            </select>
        </div>
    </div>

    <div style="margin-bottom:14px;">
        <label class="form-label">Nama Barang <span style="color:#ef4444">*</span></label>
        <input type="text" name="nama_barang" class="form-input" required value="{{ old('nama_barang', $asset->nama_barang ?? '') }}" placeholder="mis. Laptop Asus X441">
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
        <div>
            <label class="form-label">Kode Umum</label>
            <input type="text" name="kode_umum" class="form-input" value="{{ old('kode_umum', $asset->kode_umum ?? '') }}" placeholder="mis. LPX">
        </div>
        <div>
            <label class="form-label">Kode Aset</label>
            <input type="text" name="kode_aset" class="form-input" value="{{ old('kode_aset', $asset->kode_aset ?? '') }}" placeholder="mis. 001">
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
        <div>
            <label class="form-label">Kategori</label>
            <select name="category_id" class="form-input">
                <option value="">-- Pilih Kategori --</option>
                @foreach($categories as $c)
                <option value="{{ $c->id }}" {{ old('category_id', $asset->category_id ?? '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Lokasi</label>
            <select name="location_id" class="form-input">
                <option value="">-- Pilih Lokasi --</option>
                @foreach($locations as $l)
                <option value="{{ $l->id }}" {{ old('location_id', $asset->location_id ?? '') == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;">
        <div>
            <label class="form-label">Tahun Pembelian</label>
            <input type="number" name="tahun_pembelian" class="form-input" value="{{ old('tahun_pembelian', $asset->tahun_pembelian ?? '') }}" min="1990" max="{{ date('Y') + 1 }}">
        </div>
        <div>
            <label class="form-label">Sumber Dana</label>
            <select name="funding_source_id" class="form-input">
                <option value="">-- Pilih Sumber Dana --</option>
                @foreach($fundingSources as $f)
                <option value="{{ $f->id }}" {{ old('funding_source_id', $asset->funding_source_id ?? '') == $f->id ? 'selected' : '' }}>{{ $f->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div style="margin-bottom:14px;">
        <label class="form-label">Keterangan</label>
        <textarea name="keterangan" class="form-input" rows="2">{{ old('keterangan', $asset->keterangan ?? '') }}</textarea>
    </div>

    <div style="margin-bottom:6px;">
        <label class="form-label">Foto Barang</label>
        @if(isset($asset) && $asset->foto)
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
            <img src="{{ $asset->foto_url }}" style="width:70px;height:70px;object-fit:cover;border-radius:8px;">
            <label style="font-size:12px;color:#64748b;display:flex;align-items:center;gap:6px;">
                <input type="checkbox" name="hapus_foto" value="1"> Hapus foto ini
            </label>
        </div>
        @endif
        <input type="file" name="foto" accept="image/*" class="form-input">
        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Format JPG/PNG/WEBP, maks 2MB.</p>
    </div>
</div>

<div style="display:flex;gap:10px;margin-top:16px;max-width:640px;">
    <a href="{{ route('sarpras.assets.index') }}" class="btn btn-secondary" style="flex:1;justify-content:center;">Batal</a>
    <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;"><i class="ti ti-device-floppy"></i> Simpan</button>
</div>
