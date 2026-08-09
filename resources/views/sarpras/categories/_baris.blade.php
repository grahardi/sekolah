<div style="border-bottom:1px solid #f1f5f9;">
    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 18px;padding-left:{{ 18 + ($level * 28) }}px;">
        <div style="display:flex;align-items:center;gap:10px;">
            @if($kategori->icon)
            <i class="ti {{ $kategori->icon }}" style="font-size:16px;color:#4338ca;"></i>
            @else
            <i class="ti ti-folder" style="font-size:16px;color:#94a3b8;"></i>
            @endif
            <span style="font-size:13px;color:#0f172a;{{ $level === 0 ? 'font-weight:600;' : '' }}">{{ $kategori->name }}</span>
            @if($kategori->assets_count ?? false)
            <span style="font-size:11px;color:#94a3b8;">({{ $kategori->assets_count }} barang)</span>
            @endif
        </div>
        <div style="display:flex;gap:6px;">
            <button onclick="document.getElementById('modal-edit-{{ $kategori->id }}').style.display='flex'" class="btn btn-secondary btn-sm"><i class="ti ti-pencil"></i></button>
            <form action="{{ route('sarpras.categories.destroy', $kategori) }}" method="POST" onsubmit="return confirm('Hapus kategori {{ $kategori->name }}?')" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="modal-edit-{{ $kategori->id }}" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:50;align-items:center;justify-content:center;padding:20px;">
        <div class="card" style="max-width:420px;width:100%;padding:22px;">
            <p style="font-size:15px;font-weight:700;color:#0f172a;margin:0 0 16px;">Edit Kategori</p>
            <form action="{{ route('sarpras.categories.update', $kategori) }}" method="POST">
                @csrf @method('PUT')
                <div style="margin-bottom:12px;">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name" class="form-input" required value="{{ $kategori->name }}">
                </div>
                <div style="margin-bottom:12px;">
                    <label class="form-label">Induk Kategori</label>
                    <select name="parent_id" class="form-input">
                        <option value="">-- Kategori Utama (tanpa induk) --</option>
                        @foreach($allCategories as $c)
                        @continue($c->id === $kategori->id)
                        <option value="{{ $c->id }}" {{ $kategori->parent_id === $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom:16px;">
                    <label class="form-label">Ikon</label>
                    <select name="icon" class="form-input">
                        <option value="">-- Tanpa Ikon --</option>
                        @foreach($iconOptions as $kelas => $label)
                        <option value="{{ $kelas }}" {{ $kategori->icon === $kelas ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex;gap:8px;">
                    <button type="button" onclick="document.getElementById('modal-edit-{{ $kategori->id }}').style.display='none'" class="btn btn-secondary" style="flex:1;justify-content:center;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="flex:1;justify-content:center;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($kategori->childrenRecursive as $anak)
    @include('sarpras.categories._baris', ['kategori' => $anak, 'level' => $level + 1, 'iconOptions' => $iconOptions, 'allCategories' => $allCategories])
@endforeach
