@extends('layouts.app')
@section('title', 'Edit — ' . $siswa->nama_lengkap)
@section('page-title', 'Edit Data Siswa')

@section('header-actions')
    <a href="{{ route('siswa.show', $siswa) }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<form action="{{ route('siswa.update', $siswa) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @if($errors->any())
    <div class="alert alert-error" style="margin-bottom:20px;">
        <i class="ti ti-circle-x"></i>
        <div>
            <strong>Terdapat {{ $errors->count() }} kesalahan:</strong>
            <ul style="margin:4px 0 0 16px;font-size:12px;">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    </div>
    @endif

    @include('siswa._form')

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
        <a href="{{ route('siswa.show', $siswa) }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Simpan Perubahan</button>
    </div>
</form>
@endsection
