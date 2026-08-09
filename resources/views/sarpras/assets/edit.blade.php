@extends('layouts.sarpras')
@section('title', 'Edit Barang')
@section('page-title', 'Edit Barang - ' . $asset->nama_barang)

@section('content')
@if($errors->any())
<div style="background:#fef2f2;color:#991b1b;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;max-width:640px;">
    @foreach($errors->all() as $e)<p style="margin:2px 0;">{{ $e }}</p>@endforeach
</div>
@endif

<form action="{{ route('sarpras.assets.update', $asset) }}" method="POST" enctype="multipart/form-data">
    @include('sarpras.assets._form')
</form>
@endsection
