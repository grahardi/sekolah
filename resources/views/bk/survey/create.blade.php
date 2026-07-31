@extends('layouts.bk')
@section('title', 'Buat Survey')
@section('page-title', 'Buat Survey Baru')

@section('header-actions')
    <a href="{{ route('bk.survey.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<form action="{{ route('bk.survey.store') }}" method="POST" style="max-width:820px;margin:0 auto;">
    @csrf
    @include('bk.survey._form', ['survey' => null])
    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;margin-top:16px;">
        <i class="ti ti-device-floppy"></i> Simpan Survey
    </button>
</form>
@endsection
