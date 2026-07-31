@extends('layouts.bk')
@section('title', 'Edit Survey')
@section('page-title', 'Edit Survey')

@section('header-actions')
    <a href="{{ route('bk.survey.show', $survey) }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<form action="{{ route('bk.survey.update', $survey) }}" method="POST" style="max-width:820px;margin:0 auto;">
    @csrf
    @method('PUT')
    @include('bk.survey._form', ['survey' => $survey, 'kelasList' => $kelasList])
    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:12px;margin-top:16px;">
        <i class="ti ti-device-floppy"></i> Simpan Perubahan
    </button>
</form>
@endsection
