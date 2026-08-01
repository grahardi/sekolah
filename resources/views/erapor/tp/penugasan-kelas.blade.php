@extends('layouts.erapor')
@section('title', 'Penugasan Kelas TP')
@section('page-title', 'Penugasan Kelas TP')

@section('header-actions')
    <a href="{{ route('erapor.tp.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Pilih kelas mana TP ini berlaku. {{ auth()->user()->role === 'guru' ? 'Cuma kelas yang kamu ampu untuk mapel ini yang bisa dipilih.' : '' }}
</p>

<form action="{{ route('erapor.tp.update-penugasan-kelas', $tp) }}" method="POST" class="card" style="max-width:680px;margin:0 auto;padding:24px;">
    @csrf
    @method('PUT')

    <div style="background:#f8fafc;border-radius:8px;padding:12px 14px;margin-bottom:18px;">
        <p style="font-size:12px;color:#94a3b8;margin:0 0 2px;">{{ $tp->mataPelajaran->nama }} &middot; {{ $tp->kode_tp }}</p>
        <p style="font-size:13px;color:#0f172a;margin:0;">{{ $tp->deskripsi_tp }}</p>
    </div>

    <label class="form-label">Berlaku untuk Kelas <span style="color:#ef4444">*</span></label>
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin:6px 0 20px;">
        @forelse($kelasList as $k)
        @php [$kl,$rb] = explode('|', $k); @endphp
        <label style="display:flex;align-items:center;gap:5px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:6px 10px;font-size:12px;cursor:pointer;">
            <input type="checkbox" name="kelas_rombel[]" value="{{ $k }}" {{ in_array($k, $kelasTerpilih) ? 'checked' : '' }}>
            {{ $rb ? "$kl - $rb" : $kl }}
        </label>
        @empty
        <p style="font-size:12px;color:#94a3b8;">
            @if(auth()->user()->role === 'guru')
            Kamu tidak ditugaskan mengajar mapel ini di kelas manapun.
            @else
            Belum ada data kelas siswa aktif.
            @endif
        </p>
        @endforelse
    </div>

    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;"><i class="ti ti-device-floppy"></i> Simpan Penugasan Kelas</button>
</form>
@endsection
