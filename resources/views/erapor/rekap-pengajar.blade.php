@extends('layouts.erapor')
@section('title', 'Rekap Pengajar')
@section('page-title', 'Rekap Pengajar per Kelas')

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    Lihat sekilas mapel mana yang belum ada pengajarnya di kelas tertentu.
</p>

<form method="GET" style="margin-bottom:16px;max-width:300px;">
    <select name="kelas_rombel" class="form-input" onchange="this.form.submit()">
        @foreach($kelasList as $k)
        @php [$kl,$rb]=explode('|',$k); @endphp
        <option value="{{ $k }}" {{ $kelasDipilih === $k ? 'selected' : '' }}>{{ $rb ? "$kl - $rb" : $kl }}</option>
        @endforeach
    </select>
</form>

<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Mata Pelajaran</th><th style="padding:10px;">Pengajar</th>
        </tr></thead>
        <tbody>
            @forelse($rekap as $r)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;font-weight:700;">{{ $r['mapel'] }}</td>
                <td style="padding:10px;">
                    @if($r['guru'])
                    <span style="color:#0f172a;">{{ $r['guru'] }}</span>
                    @else
                    <span class="badge" style="background:#fef2f2;color:#dc2626;">Belum ada pengajar</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="2" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada mata pelajaran atau kelas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
