@extends('layouts.kepegawaian')
@section('title', 'Export Data Pegawai')
@section('page-title', 'Export Data Pegawai')

@section('header-actions')
    <a href="{{ route('pegawai.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@push('styles')
<style>
    .export-choice:hover { box-shadow: 0 4px 14px rgba(0,0,0,.08); border-color: #bfdbfe; }
</style>
@endpush

@section('content')
<div style="max-width:560px;margin:0 auto;">
    <p style="font-size:13px;color:#64748b;margin-bottom:20px;">Pilih format file yang ingin diunduh.</p>

    <div style="display:flex;flex-direction:column;gap:14px;">
        <a href="{{ route('pegawai.export.excel', $query) }}" class="card export-choice" style="display:flex;align-items:center;gap:16px;padding:20px;text-decoration:none;transition:box-shadow .12s;">
            <div style="width:52px;height:52px;background:#f0fdf4;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="ti ti-table-export" style="font-size:26px;color:#16a34a;"></i>
            </div>
            <div>
                <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 3px;">Export Excel</p>
                <p style="font-size:12px;color:#64748b;margin:0;">Format .xlsx, cocok untuk diolah lebih lanjut atau diarsipkan.</p>
            </div>
        </a>

        <a href="{{ route('pegawai.export.pdf', $query) }}" class="card export-choice" style="display:flex;align-items:center;gap:16px;padding:20px;text-decoration:none;transition:box-shadow .12s;">
            <div style="width:52px;height:52px;background:#fef2f2;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <i class="ti ti-file-type-pdf" style="font-size:26px;color:#dc2626;"></i>
            </div>
            <div>
                <p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 3px;">Export PDF Daftar</p>
                <p style="font-size:12px;color:#64748b;margin:0;">Daftar pegawai siap cetak dalam format PDF (A4 landscape).</p>
            </div>
        </a>
    </div>
</div>
@endsection
