@extends('layouts.manajemen-sekolah')
@section('title', 'Data Guru')
@section('page-title', 'Data Guru')

@section('content')
<p style="font-size:12px;color:#94a3b8;margin:-10px 0 16px;">
    Data ini sama dengan menu Guru di E-Rapor - untuk tambah/edit data guru, buka menu <a href="/erapor/guru" style="color:#2563EB;">E-Rapor &rarr; Guru</a>.
</p>

<form method="GET" style="margin-bottom:18px;max-width:400px;display:flex;gap:8px;">
    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama guru..." class="form-input">
    <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i></button>
</form>

<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Nama</th><th style="padding:10px;">NIP/NUPTK</th><th style="padding:10px;">Status</th>
            @if(auth()->user()->isAdmin())<th style="padding:10px;">Role Manajemen Sekolah</th>@endif
        </tr></thead>
        <tbody>
            @forelse($guruList as $g)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;font-weight:700;">{{ $g->nama }}</td>
                <td style="padding:10px;font-family:monospace;color:#64748b;">{{ $g->nip_nuptk ?? '-' }}</td>
                <td style="padding:10px;">
                    @if($g->isDariKepegawaian())
                    <span class="badge" style="background:#eff6ff;color:#1E3A5F;">Kepegawaian</span>
                    @else
                    <span class="badge" style="background:#fef3c7;color:#92400e;">Guru Bantu</span>
                    @endif
                </td>
                @if(auth()->user()->isAdmin())
                <td style="padding:10px;">
                    <div style="display:flex;gap:4px;flex-wrap:wrap;">
                        @foreach(['is_piket'=>'Piket','is_tatib'=>'Tatib','is_bk'=>'BK','is_kebersihan'=>'Kebersihan','is_keagamaan'=>'Keagamaan','is_kepsek'=>'Kepsek'] as $flag=>$label)
                        @if($g->{$flag})<span class="badge" style="background:#dcfce7;color:#166534;">{{ $label }}</span>@endif
                        @endforeach
                    </div>
                    <button type="button" onclick="document.getElementById('modal-role-{{ $g->id }}').style.display='flex'" class="btn btn-secondary btn-sm" style="margin-top:6px;"><i class="ti ti-shield-cog"></i> Atur</button>

                    <div id="modal-role-{{ $g->id }}" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:50;align-items:center;justify-content:center;padding:20px;">
                        <div class="card" style="max-width:380px;width:100%;padding:22px;text-align:left;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                                <p style="font-size:14px;font-weight:800;color:#0f172a;margin:0;">Role Manajemen Sekolah - {{ $g->nama }}</p>
                                <button type="button" onclick="document.getElementById('modal-role-{{ $g->id }}').style.display='none'" style="background:none;border:none;font-size:18px;color:#94a3b8;cursor:pointer;">&times;</button>
                            </div>
                            <form action="{{ route('manajemen-sekolah.data-guru.update-role', $g) }}" method="POST">
                                @csrf @method('PUT')
                                @foreach(['is_piket' => 'Piket', 'is_tatib' => 'Tata Tertib (Tatib)', 'is_bk' => 'Bimbingan Konseling (BK)', 'is_kebersihan' => 'Kebersihan', 'is_keagamaan' => 'Keagamaan', 'is_kepsek' => 'Kepala Sekolah'] as $flag => $label)
                                <label style="display:flex;align-items:center;gap:8px;padding:8px 0;border-bottom:1px solid #f1f5f9;">
                                    <input type="checkbox" name="{{ $flag }}" value="1" {{ $g->{$flag} ? 'checked' : '' }}>
                                    <span style="font-size:13px;">{{ $label }}</span>
                                </label>
                                @endforeach
                                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:10px;margin-top:14px;"><i class="ti ti-device-floppy"></i> Simpan Role</button>
                            </form>
                        </div>
                    </div>
                </td>
                @endif
            </tr>
            @empty
            <tr><td colspan="4" style="padding:20px;text-align:center;color:#94a3b8;">Tidak ada data guru.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $guruList->links() }}
@endsection
