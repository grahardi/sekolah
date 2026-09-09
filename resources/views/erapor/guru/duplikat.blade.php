@extends('layouts.erapor')
@section('title', 'Guru Duplikat')
@section('page-title', 'Deteksi & Gabungkan Guru Duplikat')

@section('content')

@if(session('success'))
<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">{{ session('success') }}</div>
@endif

<p style="font-size:13px;color:#64748b;margin:-8px 0 20px;max-width:640px;">
    Guru dengan nama yang sama (setelah huruf besar/kecil & spasi dirapikan) dikelompokkan di sini - biasanya terjadi kalau orang yang sama sempat tercatat dua kali (mis. sebelum & sesudah terhubung ke Kepegawaian). Pilih satu sebagai "utama", sisanya akan digabungkan ke situ.
</p>

@forelse($grup as $nama => $items)
<div class="card" style="padding:18px;margin-bottom:16px;">
    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 12px;text-transform:capitalize;">{{ $nama }} ({{ $items->count() }} record)</p>

    <form action="{{ route('erapor.guru.duplikat-gabung') }}" method="POST" onsubmit="return confirm('Gabungkan semua data ke record yang dipilih sebagai utama? Record lain akan DIHAPUS setelah datanya dipindah.')">
        @csrf
        <table style="width:100%;border-collapse:collapse;margin-bottom:14px;">
            <thead style="background:#f8fafc;">
                <tr>
                    <th style="padding:8px 10px;text-align:center;font-size:11px;color:#64748b;width:60px;">Jadikan Utama</th>
                    <th style="padding:8px 10px;text-align:left;font-size:11px;color:#64748b;">ID</th>
                    <th style="padding:8px 10px;text-align:left;font-size:11px;color:#64748b;">Terhubung</th>
                    <th style="padding:8px 10px;text-align:center;font-size:11px;color:#64748b;">Tugas Mengajar</th>
                    <th style="padding:8px 10px;text-align:center;font-size:11px;color:#64748b;">Ekskul</th>
                    <th style="padding:8px 10px;text-align:center;font-size:11px;color:#64748b;">Kokurikuler</th>
                    <th style="padding:8px 10px;text-align:center;font-size:11px;color:#64748b;">Wali Kelas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $g)
                <tr style="border-top:1px solid #f1f5f9;">
                    <td style="padding:8px 10px;text-align:center;">
                        <input type="radio" name="guru_utama_id" value="{{ $g->id }}" required {{ $g->pegawai_id ? 'checked' : '' }}>
                    </td>
                    <td style="padding:8px 10px;font-size:12px;color:#94a3b8;">
                        #{{ $g->id }}
                        <input type="checkbox" name="guru_duplikat_ids[]" value="{{ $g->id }}" checked style="margin-left:6px;" title="Ikutkan dalam penggabungan">
                    </td>
                    <td style="padding:8px 10px;font-size:12px;">
                        @if($g->pegawai_id)<span style="color:#166534;">Kepegawaian</span>@endif
                        @if($g->user_id)<span style="color:#1e40af;"> · Akun Login</span>@endif
                        @if(! $g->pegawai_id && ! $g->user_id)<span style="color:#94a3b8;">Standalone</span>@endif
                    </td>
                    <td style="padding:8px 10px;text-align:center;font-size:13px;font-weight:600;color:#0f172a;">{{ $g->jumlah_pengajar }}</td>
                    <td style="padding:8px 10px;text-align:center;font-size:13px;color:#334155;">{{ $g->jumlah_ekskul }}</td>
                    <td style="padding:8px 10px;text-align:center;font-size:13px;color:#334155;">{{ $g->jumlah_kokurikuler }}</td>
                    <td style="padding:8px 10px;text-align:center;font-size:13px;color:#334155;">{{ $g->jumlah_wali_kelas }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p style="font-size:11px;color:#94a3b8;margin:0 0 10px;">Centang "ID" di baris mana saja yang mau digabungkan (defaultnya semua tercentang). Radio "Jadikan Utama" otomatis pilih yang sudah terhubung Kepegawaian.</p>
        <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-git-merge"></i> Gabungkan</button>
    </form>
</div>
@empty
<div class="card" style="padding:30px;text-align:center;">
    <p style="font-size:13px;color:#94a3b8;margin:0;">Tidak ada guru duplikat terdeteksi. Semua nama unik.</p>
</div>
@endforelse

@endsection
