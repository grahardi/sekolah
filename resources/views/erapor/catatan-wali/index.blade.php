@extends('layouts.erapor')
@section('title', 'Catatan Wali Kelas')
@section('page-title', 'Catatan Wali Kelas')

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Klik nama siswa untuk buka/tutup detail. Isi absensi & catatan, lalu simpan semua sekaligus di bawah.
    Siswa dengan rapor Final tidak bisa diedit di sini - batalkan finalisasi dulu di halaman Cetak Rapor.
</p>

<form action="{{ route('erapor.catatan-wali.store') }}" method="POST">
    @csrf
    <div style="display:flex;flex-direction:column;gap:10px;">
        @foreach($siswaList as $i => $siswa)
        @php $rapor = $siswa->rapor; $terkunci = $rapor->status === 'Final'; @endphp
        <div class="card" style="overflow:hidden;">
            <button type="button" onclick="toggleAkordeon({{ $siswa->id }})" style="width:100%;display:flex;justify-content:space-between;align-items:center;padding:14px 18px;background:#f1f5f9;border:none;cursor:pointer;text-align:left;">
                <span style="display:flex;align-items:center;gap:10px;">
                    <span style="width:26px;height:26px;border-radius:50%;background:#1E3A5F;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;">{{ $i + 1 }}</span>
                    <span style="font-weight:700;color:#1E3A5F;font-size:14px;">{{ strtoupper($siswa->nama_lengkap) }}</span>
                    @if($terkunci)<span class="badge badge-aktif">Final</span>@endif
                </span>
                <i class="ti ti-chevron-down" id="chev-{{ $siswa->id }}" style="transition:transform .15s;{{ $i === 0 ? 'transform:rotate(180deg);' : '' }}"></i>
            </button>

            <div id="body-{{ $siswa->id }}" style="display:{{ $i === 0 ? 'block' : 'none' }};padding:18px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                    <div>
                        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 8px;"><i class="ti ti-calendar-check"></i> Absensi (Jumlah Hari)</p>
                        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;margin-bottom:16px;">
                            <div><label class="form-label">Sakit</label><input type="number" name="sakit[{{ $rapor->id }}]" value="{{ $rapor->sakit }}" min="0" class="form-input" {{ $terkunci ? 'disabled' : '' }}></div>
                            <div><label class="form-label">Izin</label><input type="number" name="izin[{{ $rapor->id }}]" value="{{ $rapor->izin }}" min="0" class="form-input" {{ $terkunci ? 'disabled' : '' }}></div>
                            <div><label class="form-label">Alpha</label><input type="number" name="alpa[{{ $rapor->id }}]" value="{{ $rapor->tanpa_keterangan }}" min="0" class="form-input" {{ $terkunci ? 'disabled' : '' }}></div>
                        </div>

                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                            <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0;"><i class="ti ti-notes"></i> Catatan Wali Kelas</p>
                            @if(!$terkunci)
                            <button type="button" onclick="buatOtomatis({{ $siswa->id }}, {{ $rapor->id }})" class="btn btn-sm" style="background:#0891b2;color:#fff;"><i class="ti ti-wand"></i> Buat Otomatis</button>
                            @endif
                        </div>
                        <textarea name="catatan[{{ $rapor->id }}]" id="catatan-{{ $rapor->id }}" class="form-input" rows="6" {{ $terkunci ? 'disabled' : '' }}
                                  placeholder="mis. Ananda sudah menunjukkan sikap yang baik...">{{ $rapor->catatan_wali_kelas }}</textarea>
                    </div>

                    <div>
                        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 8px;"><i class="ti ti-bike"></i> Rangkuman Ekstrakurikuler</p>
                        <div style="display:flex;flex-direction:column;gap:8px;">
                            @forelse($rapor->detailEkskul as $e)
                            <div style="border-left:3px solid #2563EB;background:#f8fafc;border-radius:0 8px 8px 0;padding:10px 14px;">
                                <div style="display:flex;justify-content:space-between;align-items:start;gap:8px;">
                                    <p style="font-weight:700;color:#0f172a;margin:0;font-size:13px;">{{ $e->nama_ekskul }}</p>
                                    @if($e->kehadiran_total)
                                    <span class="badge" style="background:#1E293B;color:#fff;white-space:nowrap;">Kehadiran: {{ $e->kehadiran_hadir }} / {{ $e->kehadiran_total }}</span>
                                    @endif
                                </div>
                                <p style="font-size:12px;color:#64748b;margin:4px 0 0;">
                                    {{ $e->keterangan }}
                                    @if($e->evaluasi)<strong style="color:#374151;">: {{ $e->evaluasi }}</strong>@endif
                                </p>
                            </div>
                            @empty
                            <p style="font-size:12px;color:#94a3b8;font-style:italic;">Tidak mengikuti kegiatan ekstrakurikuler.</p>
                            @endforelse
                        </div>
                        <p style="font-size:11px;color:#94a3b8;margin-top:10px;">Kelola daftar ekstrakurikuler siswa ini di <a href="{{ route('erapor.rapor.edit', $rapor) }}" style="color:#2563EB;">halaman Kelola Rapor</a>.</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;margin-top:16px;"><i class="ti ti-device-floppy"></i> Simpan Semua</button>
</form>

<script>
    const SEMUA_SISWA_ID = {!! $siswaList->pluck('id')->toJson() !!};

    function toggleAkordeon(id) {
        const body = document.getElementById('body-' + id);
        const sedangTerbuka = body.style.display !== 'none';

        // Tutup semua dulu supaya cuma 1 yang terbuka (halaman gak memanjang)
        SEMUA_SISWA_ID.forEach((sid) => {
            document.getElementById('body-' + sid).style.display = 'none';
            document.getElementById('chev-' + sid).style.transform = 'rotate(0deg)';
        });

        // Kalau yg diklik sebelumnya TERTUTUP -> buka. Kalau sebelumnya
        // TERBUKA -> biarkan tertutup (klik lagi = tutup).
        if (!sedangTerbuka) {
            body.style.display = 'block';
            document.getElementById('chev-' + id).style.transform = 'rotate(180deg)';
        }
    }

    function buatOtomatis(siswaId, raporId) {
        fetch(`/erapor/rapor/${raporId}/catatan-otomatis`)
            .then(r => r.json())
            .then(json => {
                document.getElementById('catatan-' + raporId).value = json.teks;
            });
    }
</script>
@endsection
