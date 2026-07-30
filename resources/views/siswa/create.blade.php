@extends('layouts.app')
@section('title', 'Tambah Siswa')
@section('page-title', 'Tambah Siswa Baru')

@section('header-actions')
    <a href="{{ route('siswa.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<form action="{{ route('siswa.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if($errors->any())
    <div class="alert alert-error" style="margin-bottom:20px;">
        <i class="ti ti-circle-x"></i>
        <div>
            <strong>Terdapat {{ $errors->count() }} kesalahan input:</strong>
            <ul style="margin:4px 0 0 16px;font-size:12px;">
                @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
            </ul>
        </div>
    </div>
    @endif

    @include('siswa._form')

    <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px;">
        <a href="{{ route('siswa.index') }}" class="btn btn-secondary">Batal</a>
        <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy"></i> Simpan Siswa</button>
    </div>
</form>
@endsection
