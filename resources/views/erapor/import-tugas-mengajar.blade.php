@extends('layouts.erapor')
@section('title', 'Import Tugas Mengajar')
@section('page-title', 'Import Tugas Mengajar dari Excel')

@section('content')

<div class="card" style="padding:20px;max-width:560px;">
    <p style="font-size:13px;color:#64748b;margin:0 0 16px;">
        Import jadwal guru pengajar per kelas dari file Excel (format: blok "KELAS VII-A" diikuti baris No/Mapel/Guru).
        Mapel dicocokkan otomatis (dibuat baru kalau belum ada), guru dicocokkan berdasarkan nama - kalau ada nama yang tidak ketemu, akan dilaporkan di akhir (tidak menghentikan proses).
    </p>

    <form action="{{ route('erapor.tugas-mengajar.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label class="form-label">Tahun Ajaran</label>
        <select name="tahun_ajaran_id" class="form-input" required style="margin-bottom:14px;">
            <option value="">-- Pilih --</option>
            @foreach($tahunAjaranList as $ta)
            <option value="{{ $ta->id }}">{{ $ta->nama }}</option>
            @endforeach
        </select>

        <label class="form-label">File Excel</label>
        <input type="file" name="file" accept=".xlsx,.xls" required class="form-input" style="margin-bottom:16px;">

        <button type="submit" class="btn btn-primary"><i class="ti ti-file-import"></i> Import Sekarang</button>
    </form>
</div>

@endsection
