@extends('layouts.app')
@section('title', 'Pilih Model Kartu')
@section('page-title', 'Cetak Kartu Pelajar - ' . $siswa->nama_lengkap)

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 20px;">
    Pilih salah satu model kartu di bawah (pratinjau langsung, gambar PNG asli - bukan PDF). Ukuran standar ID card
    (85.6mm x 54mm) dengan barcode Code128 yang bisa dipindai scanner perpustakaan, membaca NIS siswa.
</p>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:20px;">
    @foreach([1 => 'Modern Navy', 2 => 'Colorful Badge', 3 => 'Minimalist'] as $model => $namaModel)
    <div class="card" style="padding:18px;text-align:center;">
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 12px;">Model {{ $model }}: {{ $namaModel }}</p>
        <div style="background:#f8fafc;border-radius:8px;padding:12px;margin-bottom:14px;">
            <img src="{{ route('siswa.kartu.pdf', ['siswa' => $siswa->id, 'model' => $model, 'preview' => 1]) }}" style="width:100%;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,.1);">
        </div>
        <a href="{{ route('siswa.kartu.pdf', ['siswa' => $siswa->id, 'model' => $model]) }}" class="btn btn-primary" style="width:100%;justify-content:center;">
            <i class="ti ti-download"></i> Unduh PNG Model {{ $model }}
        </a>
    </div>
    @endforeach
</div>

<div class="card" style="padding:18px;margin-top:24px;">
    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 12px;">Cetak Massal 1 Kelas Sekaligus (ZIP berisi PNG per siswa)</p>
    <form action="{{ route('siswa.kartu.massal') }}" method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:end;">
        <div style="min-width:160px;">
            <label class="form-label">Kelas</label>
            <select name="kelas_rombel" class="form-input" required>
                @foreach($kelasRombelList as $kr)
                @php [$kl,$rb] = explode('|', $kr); @endphp
                <option value="{{ $kr }}">{{ $rb ? "$kl - $rb" : $kl }}</option>
                @endforeach
            </select>
        </div>
        <div style="min-width:160px;">
            <label class="form-label">Model Kartu</label>
            <select name="model" class="form-input" required>
                <option value="1">Model 1: Modern Navy</option>
                <option value="2">Model 2: Colorful Badge</option>
                <option value="3">Model 3: Minimalist</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary"><i class="ti ti-download"></i> Unduh ZIP Kartu 1 Kelas</button>
    </form>
</div>
@endsection
