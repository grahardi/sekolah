@extends('layouts.kepegawaian')
@section('title', 'Tambah Pegawai')
@section('page-title', 'Tambah Pegawai')

@section('header-actions')
    <a href="{{ route('pegawai.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="card" style="max-width:720px;margin:0 auto;padding:24px;">
    <form action="{{ route('pegawai.store') }}" method="POST">
        @csrf
        @include('pegawai._form')
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;margin-top:10px;">
            <i class="ti ti-device-floppy"></i> Simpan Data Pegawai
        </button>
    </form>
</div>
@endsection
