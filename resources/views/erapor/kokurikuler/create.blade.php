@extends('layouts.erapor')
@section('title', 'Rencanakan Kegiatan')
@section('page-title', 'Rencanakan Kegiatan Kokurikuler')

@section('header-actions')
    <a href="{{ route('erapor.kokurikuler.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<form action="{{ route('erapor.kokurikuler.store') }}" method="POST" id="form-kokurikuler">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <div class="card" style="padding:22px;">
            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 16px;"><span class="badge" style="background:#1E3A5F;color:#fff;">1</span> Detail Kegiatan</p>

            <div style="margin-bottom:14px;">
                <label class="form-label">Nama Kegiatan / Tema</label>
                <input type="text" name="nama_kegiatan" class="form-input" placeholder="mis. 7 Kebiasaan Anak Indonesia Hebat" required>
            </div>
            <div style="margin-bottom:14px;">
                <label class="form-label">Tema P5 (opsional)</label>
                <input type="text" name="tema" class="form-input" placeholder="mis. Gaya Hidup Berkelanjutan">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;">
                <div>
                    <label class="form-label">Semester</label>
                    <select name="semester" class="form-input" required>
                        <option value="1">Ganjil</option>
                        <option value="2">Genap</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Bentuk Kegiatan</label>
                    <select name="bentuk_kegiatan" class="form-input" required>
                        <option value="Lintas Disiplin">Lintas Disiplin</option>
                        <option value="G7KAIH">Gerakan 7KAIH</option>
                        <option value="Khas Sekolah">Khas Sekolah</option>
                    </select>
                </div>
            </div>
            <div style="margin-bottom:14px;">
                <label class="form-label">Koordinator Projek</label>
                <select name="koordinator_guru_id" class="form-input" required>
                    <option value="">-- Pilih guru koordinator --</option>
                    @foreach($guruList as $g)<option value="{{ $g->id }}">{{ $g->nama }}</option>@endforeach
                </select>
            </div>

            <label class="form-label">Pilih Kelas Peserta <span style="color:#ef4444">*</span></label>
            <div style="max-height:160px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:8px;padding:10px;margin-bottom:14px;">
                @forelse($kelasList as $k)
                @php [$kl,$rb]=explode('|',$k); @endphp
                <label style="display:flex;align-items:center;gap:6px;font-size:12px;padding:4px 0;cursor:pointer;">
                    <input type="checkbox" name="kelas_sasaran[]" value="{{ $k }}"> {{ $rb ? "$kl - $rb" : $kl }}
                </label>
                @empty
                <p style="font-size:12px;color:#94a3b8;">Tidak ada data kelas.</p>
                @endforelse
            </div>

            <label class="form-label">Mapel Terlibat <span style="color:#ef4444">*</span></label>
            <div style="max-height:160px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:8px;padding:10px;">
                @foreach($mapelList as $m)
                <label style="display:flex;align-items:center;gap:6px;font-size:12px;padding:4px 0;cursor:pointer;">
                    <input type="checkbox" name="mapel_terlibat[]" value="{{ $m->id }}"> {{ $m->nama }}
                </label>
                @endforeach
            </div>
        </div>

        <div class="card" style="padding:22px;">
            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 16px;"><span class="badge" style="background:#1E3A5F;color:#fff;">2</span> Pilih Dimensi Profil Lulusan</p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                @foreach($dimensiList as $dim)
                <label class="dimensi-card" style="border:2px solid #e2e8f0;border-radius:8px;padding:12px;cursor:pointer;display:block;">
                    <input type="checkbox" name="dimensi[]" value="{{ $dim }}" style="display:none;" onchange="this.closest('.dimensi-card').style.borderColor = this.checked ? '#1E3A5F' : '#e2e8f0'; this.closest('.dimensi-card').style.background = this.checked ? '#eff6ff' : '#fff';">
                    <p style="font-size:12px;font-weight:700;color:#0f172a;margin:0;">{{ $dim }}</p>
                </label>
                @endforeach
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:13px;margin-top:20px;font-size:14px;"><i class="ti ti-device-floppy"></i> Simpan Rencana Kegiatan</button>
</form>
@endsection
