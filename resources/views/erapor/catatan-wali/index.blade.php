@extends('layouts.erapor')
@section('title', 'Catatan Wali Kelas')
@section('page-title', 'Catatan Wali Kelas')

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Isi catatan untuk tiap siswa di kelasmu, lalu simpan semua sekaligus. Siswa dengan rapor berstatus
    Final tidak bisa diedit di sini - batalkan finalisasi dulu di halaman Cetak Rapor.
</p>

<form action="{{ route('erapor.catatan-wali.store') }}" method="POST">
    @csrf
    <div style="display:flex;flex-direction:column;gap:12px;">
        @foreach($siswaList as $siswa)
        <div class="card" style="padding:16px;">
            <div style="display:flex;align-items:center;justify-content:between;gap:10px;margin-bottom:8px;">
                <p style="font-weight:700;color:#0f172a;margin:0;flex:1;">{{ $siswa->nama_lengkap }}</p>
                @if($siswa->rapor->status === 'Final')
                <span class="badge badge-aktif">Final (terkunci)</span>
                @endif
            </div>
            <textarea name="catatan[{{ $siswa->rapor->id }}]" class="form-input" rows="2"
                      {{ $siswa->rapor->status === 'Final' ? 'disabled' : '' }}
                      placeholder="mis. Ananda sudah menunjukkan sikap yang baik...">{{ $siswa->rapor->catatan_wali_kelas }}</textarea>
        </div>
        @endforeach
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;margin-top:16px;"><i class="ti ti-device-floppy"></i> Simpan Semua Catatan</button>
</form>
@endsection
