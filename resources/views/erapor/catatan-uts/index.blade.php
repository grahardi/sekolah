@extends('layouts.erapor')
@section('title', 'Catatan UTS/PTS')
@section('page-title', 'Catatan UTS/PTS')

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Catatan khusus yang muncul di lembar cetak UTS/PTS - terpisah dari Catatan Wali Kelas di rapor akhir
    semester. Tidak perlu finalisasi, bisa diedit kapan saja.
</p>

<form action="{{ route('erapor.catatan-uts.store') }}" method="POST">
    @csrf
    <div style="display:flex;flex-direction:column;gap:10px;">
        @foreach($siswaList as $i => $siswa)
        @php $rapor = $siswa->rapor; @endphp
        <div class="card" style="overflow:hidden;">
            <button type="button" onclick="toggleAkordeon({{ $siswa->id }})" style="width:100%;display:flex;justify-content:space-between;align-items:center;padding:14px 18px;background:#f1f5f9;border:none;cursor:pointer;text-align:left;">
                <span style="display:flex;align-items:center;gap:10px;">
                    <span style="width:26px;height:26px;border-radius:50%;background:#1E3A5F;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;">{{ $i + 1 }}</span>
                    <span style="font-weight:700;color:#1E3A5F;font-size:14px;">{{ strtoupper($siswa->nama_lengkap) }}</span>
                </span>
                <i class="ti ti-chevron-down" id="chev-{{ $siswa->id }}" style="transition:transform .15s;{{ $i === 0 ? 'transform:rotate(180deg);' : '' }}"></i>
            </button>

            <div id="body-{{ $siswa->id }}" style="display:{{ $i === 0 ? 'block' : 'none' }};padding:18px;">
                <textarea name="catatan_uts[{{ $rapor->id }}]" class="form-input" rows="3" placeholder="Catatan khusus di lembar cetak UTS/PTS...">{{ $rapor->catatan_uts }}</textarea>
            </div>
        </div>
        @endforeach
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;margin-top:16px;"><i class="ti ti-device-floppy"></i> Simpan Semua Catatan UTS</button>
</form>

<script>
    const SEMUA_SISWA_ID = {!! $siswaList->pluck('id')->toJson() !!};

    function toggleAkordeon(id) {
        const body = document.getElementById('body-' + id);
        const sedangTerbuka = body.style.display !== 'none';

        SEMUA_SISWA_ID.forEach((sid) => {
            document.getElementById('body-' + sid).style.display = 'none';
            document.getElementById('chev-' + sid).style.transform = 'rotate(0deg)';
        });

        if (!sedangTerbuka) {
            body.style.display = 'block';
            document.getElementById('chev-' + id).style.transform = 'rotate(180deg)';
        }
    }
</script>
@endsection
