@extends('layouts.app')
@section('title', 'Baris Error Import')
@section('page-title', 'Baris Error Import')

@section('header-actions')
    <a href="{{ route('siswa.import.form') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali ke Import</a>
@endsection

@section('content')
<div style="max-width:760px;margin:0 auto;">
    <div class="card">
        <div class="card-header">
            <p style="font-size:14px;font-weight:700;color:#991b1b;margin:0;">
                <i class="ti ti-circle-x" style="color:#dc2626;"></i> {{ $total }} baris gagal diimport
            </p>
        </div>
        <div class="card-body" style="padding:0;">
            @foreach($errors as $err)
            <div style="padding:12px 18px;border-bottom:1px solid #f1f5f9;font-size:13px;color:#991b1b;">
                <i class="ti ti-point-filled" style="font-size:10px;"></i> {{ $err }}
            </div>
            @endforeach
        </div>
    </div>

    <div style="margin-top:16px;">
        {{ $errors->links() }}
    </div>
</div>
@endsection
