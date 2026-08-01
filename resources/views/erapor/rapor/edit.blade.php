@extends('layouts.erapor')
@section('title', 'Kelola Rapor - ' . $rapor->siswa->nama_lengkap)
@section('page-title', 'Rapor: ' . $rapor->siswa->nama_lengkap)

@section('header-actions')
    <a href="{{ route('erapor.rapor.cetak', $rapor) }}" class="btn btn-primary"><i class="ti ti-printer"></i> Cetak PDF</a>
    <a href="{{ route('erapor.rapor.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<form action="{{ route('erapor.rapor.update', $rapor) }}" method="POST">
    @csrf @method('PUT')

    <div class="card" style="padding:20px;margin-bottom:16px;">
        <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 14px;">A. Nilai Akademik (hasil hitung otomatis)</p>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
                <th style="padding:8px;">Mapel</th><th style="padding:8px;width:90px;">Nilai Sistem</th><th style="padding:8px;width:110px;">Nilai Katrol</th><th style="padding:8px;">Capaian Kompetensi</th>
            </tr></thead>
            <tbody>
                @forelse($rapor->detailAkademik as $d)
                <tr style="border-bottom:1px solid #f8fafc;">
                    <td style="padding:8px;font-weight:600;">{{ $d->mataPelajaran->nama }}</td>
                    <td style="padding:8px;font-weight:700;">{{ $d->nilai_akhir ?? '-' }}</td>
                    <td style="padding:8px;"><input type="number" name="nilai_katrol[{{ $d->id }}]" value="{{ $d->nilai_katrol }}" min="0" max="100" class="form-input" style="padding:5px 8px;"></td>
                    <td style="padding:8px;font-size:12px;color:#475569;">{{ $d->capaian_kompetensi ?: '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="padding:16px;text-align:center;color:#94a3b8;">Belum ada nilai. Klik "Hitung/Perbarui Nilai" di halaman sebelumnya dulu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card" style="padding:20px;margin-bottom:16px;">
        <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 14px;">B. Kokurikuler (Projek P5)</p>
        @if($daftarKegiatanP5->count() > 0)
        <select id="pilih-kegiatan-p5" class="form-input" style="margin-bottom:10px;" onchange="isiDeskripsiP5(this)">
            <option value="">-- Generate otomatis dari hasil Asesmen Kokurikuler --</option>
            @foreach($daftarKegiatanP5 as $k)
            <option value="{{ $k->id }}">{{ $k->nama_kegiatan }}{{ $k->tema ? " ($k->tema)" : '' }}</option>
            @endforeach
        </select>
        @endif
        <textarea name="deskripsi_kokurikuler" id="deskripsi_kokurikuler" class="form-input" rows="3" placeholder="mis. Dalam projek '...', ananda menunjukkan perkembangan yang sangat baik terutama pada...">{{ $rapor->deskripsi_kokurikuler }}</textarea>
    </div>

    <div class="card" style="padding:20px;margin-bottom:16px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0;">C. Ekstrakurikuler</p>
            <button type="button" onclick="tambahBarisEkskul()" class="btn btn-secondary btn-sm"><i class="ti ti-plus"></i> Tambah Baris</button>
        </div>
        <div id="ekskul-rows">
            @forelse($rapor->detailEkskul as $e)
            <div style="display:grid;grid-template-columns:1.5fr 1fr 1fr 1fr 1.5fr auto;gap:8px;margin-bottom:8px;">
                <input type="text" name="ekskul_nama[]" value="{{ $e->nama_ekskul }}" class="form-input" placeholder="Nama ekskul" list="daftar-ekskul">
                <input type="number" name="ekskul_hadir[]" value="{{ $e->kehadiran_hadir }}" class="form-input" placeholder="Hadir" min="0">
                <input type="number" name="ekskul_total[]" value="{{ $e->kehadiran_total }}" class="form-input" placeholder="Total" min="0">
                <select name="ekskul_evaluasi[]" class="form-input">
                    <option value="">Evaluasi</option>
                    @foreach(['Sangat Baik','Baik','Cukup','Kurang'] as $ev)
                    <option value="{{ $ev }}" {{ $e->evaluasi === $ev ? 'selected' : '' }}>{{ $ev }}</option>
                    @endforeach
                </select>
                <input type="text" name="ekskul_keterangan[]" value="{{ $e->keterangan }}" class="form-input" placeholder="Keterangan">
                <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button>
            </div>
            @empty
            @endforelse
        </div>
        <datalist id="daftar-ekskul">
            @foreach($daftarEkskul as $nama)
            <option value="{{ $nama }}">
            @endforeach
        </datalist>
        <p style="font-size:11px;color:#94a3b8;margin-top:6px;">Kosongkan semua baris kalau siswa tidak mengikuti ekstrakurikuler.</p>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
        <div class="card" style="padding:20px;">
            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 14px;">D. Ketidakhadiran</p>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;">
                <div><label class="form-label">Sakit</label><input type="number" name="sakit" value="{{ $rapor->sakit }}" min="0" class="form-input"></div>
                <div><label class="form-label">Izin</label><input type="number" name="izin" value="{{ $rapor->izin }}" min="0" class="form-input"></div>
                <div><label class="form-label">Tanpa Ket.</label><input type="number" name="tanpa_keterangan" value="{{ $rapor->tanpa_keterangan }}" min="0" class="form-input"></div>
            </div>
        </div>
        <div class="card" style="padding:20px;">
            <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 14px;">E. Catatan Wali Kelas</p>
            <textarea name="catatan_wali_kelas" class="form-input" rows="3" placeholder="mis. Ananda sudah menunjukkan sikap yang baik...">{{ $rapor->catatan_wali_kelas }}</textarea>
        </div>
    </div>

    @if($rapor->semester == 2)
    <div class="card" style="padding:20px;margin-bottom:16px;">
        <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 14px;">Keterangan Kelulusan / Kenaikan Kelas</p>
        <input type="text" name="keterangan_kelulusan" value="{{ $rapor->keterangan_kelulusan }}" class="form-input" placeholder="mis. NAIK KE KELAS VIII, atau LULUS">
        <p style="font-size:11px;color:#94a3b8;margin-top:6px;">Cuma muncul di semester Genap (akhir tahun ajaran), sesuai kapan kenaikan kelas ditetapkan.</p>
    </div>
    @else
    <input type="hidden" name="keterangan_kelulusan" value="">
    @endif

    <div class="card" style="padding:20px;margin-bottom:16px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
        <div>
            <label class="form-label">Status Rapor</label>
            <select name="status" class="form-input">
                <option value="Draft" {{ $rapor->status === 'Draft' ? 'selected' : '' }}>Draft</option>
                <option value="Final" {{ $rapor->status === 'Final' ? 'selected' : '' }}>Final</option>
            </select>
        </div>
        <div>
            <label class="form-label">Tanggal Rapor</label>
            <input type="date" name="tanggal_rapor" value="{{ $rapor->tanggal_rapor?->format('Y-m-d') }}" class="form-input">
        </div>
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;"><i class="ti ti-device-floppy"></i> Simpan Rapor</button>
</form>

<script>
    function tambahBarisEkskul() {
        const div = document.createElement('div');
        div.style.cssText = 'display:grid;grid-template-columns:1.5fr 1fr 1fr 1fr 1.5fr auto;gap:8px;margin-bottom:8px;';
        div.innerHTML = '<input type="text" name="ekskul_nama[]" class="form-input" placeholder="Nama ekskul" list="daftar-ekskul">'
            + '<input type="number" name="ekskul_hadir[]" class="form-input" placeholder="Hadir" min="0">'
            + '<input type="number" name="ekskul_total[]" class="form-input" placeholder="Total" min="0">'
            + '<select name="ekskul_evaluasi[]" class="form-input"><option value="">Evaluasi</option><option>Sangat Baik</option><option>Baik</option><option>Cukup</option><option>Kurang</option></select>'
            + '<input type="text" name="ekskul_keterangan[]" class="form-input" placeholder="Keterangan">'
            + '<button type="button" onclick="this.parentElement.remove()" class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button>';
        document.getElementById('ekskul-rows').appendChild(div);
    }

    function isiDeskripsiP5(select) {
        if (!select.value) return;
        fetch(`/erapor/kokurikuler/deskripsi-otomatis?kegiatan=${select.value}&siswa={{ $rapor->siswa_id }}`)
            .then(r => r.json())
            .then(json => { document.getElementById('deskripsi_kokurikuler').value = json.teks; });
    }
</script>
@endsection
