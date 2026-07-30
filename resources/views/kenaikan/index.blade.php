@extends('layouts.app')
@section('title', 'Kenaikan / Kelulusan Massal')
@section('page-title', 'Kenaikan & Kelulusan Massal')

@section('content')
@if(session('proses_errors') && count(session('proses_errors')) > 0)
<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:14px 18px;margin-bottom:20px;">
    <p style="font-size:13px;font-weight:700;color:#991b1b;margin:0 0 8px;">Beberapa siswa gagal diproses:</p>
    @foreach(session('proses_errors') as $e)<p style="font-size:12px;color:#991b1b;margin:0 0 3px;">• {{ $e }}</p>@endforeach
</div>
@endif

<div style="display:grid;grid-template-columns:420px 1fr;gap:24px;align-items:flex-start;">
    <div class="card">
        <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-arrow-up-circle" style="font-size:15px;vertical-align:-2px;margin-right:6px;color:#1d4ed8;"></i> Pengaturan Kenaikan / Kelulusan</span></div>
        <div class="card-body">
            <form action="{{ route('kenaikan.preview') }}" method="GET">
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div>
                        <label class="form-label">Tahun Ajaran <span style="color:#ef4444">*</span></label>
                        <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $tahunAjaran) }}" placeholder="2024/2025" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Kelas Asal <span style="color:#ef4444">*</span></label>
                        <select name="kelas" class="form-input" required id="sel-kelas">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $k)<option value="{{ $k }}">{{ $k }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Aksi <span style="color:#ef4444">*</span></label>
                        <select name="aksi" class="form-input" required id="sel-aksi" onchange="toggleTujuan(this.value)">
                            <option value="">-- Pilih Aksi --</option>
                            <option value="naik">⬆️ Naik Kelas</option>
                            <option value="lulus">🎓 Lulus (Kelas IX)</option>
                            <option value="tinggal">🔁 Tinggal Kelas</option>
                            <option value="pindah">🚌 Pindah Sekolah</option>
                            <option value="keluar">❌ Keluar</option>
                        </select>
                    </div>
                    <div id="div-tujuan" style="display:none;">
                        <label class="form-label">Kelas Tujuan <span style="color:#ef4444">*</span></label>
                        <select name="kelas_tujuan" class="form-input" id="sel-tujuan">
                            <option value="">-- Pilih --</option>
                            @foreach(['VII','VIII','IX'] as $k)<option value="{{ $k }}">Kelas {{ $k }}</option>@endforeach
                        </select>
                        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Angka rombel otomatis menyesuaikan (mis: 7A → 8A)</p>
                    </div>
                    <div>
                        <label class="form-label">Wali Kelas (opsional)</label>
                        <input type="text" name="wali_kelas" value="{{ old('wali_kelas') }}" placeholder="Nama wali kelas tahun ini" class="form-input">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;"><i class="ti ti-eye"></i> Lihat Preview Siswa</button>
                </div>
            </form>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="card">
            <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-chart-bar" style="font-size:15px;vertical-align:-2px;margin-right:6px;color:#1d4ed8;"></i> Rekap Siswa Aktif per Kelas</span></div>
            <div class="card-body">
                @php
                    $rekapKelas = \App\Models\Siswa::where('status','aktif')
                        ->select('kelas', \Illuminate\Support\Facades\DB::raw('count(*) as total'),
                                 \Illuminate\Support\Facades\DB::raw("sum(jenis_kelamin='L') as laki"),
                                 \Illuminate\Support\Facades\DB::raw("sum(jenis_kelamin='P') as perempuan"))
                        ->groupBy('kelas')->orderBy('kelas')->get();
                @endphp
                @if($rekapKelas->isEmpty())
                    <p style="font-size:13px;color:#94a3b8;text-align:center;padding:16px 0;">Belum ada siswa aktif.</p>
                @else
                <table style="width:100%;border-collapse:collapse;font-size:13px;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="padding:8px 14px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Kelas</th>
                            <th style="padding:8px 14px;text-align:center;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Total</th>
                            <th style="padding:8px 14px;text-align:center;font-size:11px;font-weight:700;color:#1d4ed8;text-transform:uppercase;">L</th>
                            <th style="padding:8px 14px;text-align:center;font-size:11px;font-weight:700;color:#be185d;text-transform:uppercase;">P</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rekapKelas as $r)
                        <tr style="border-top:1px solid #f1f5f9;">
                            <td style="padding:9px 14px;"><span style="background:#eff6ff;color:#1d4ed8;padding:2px 10px;border-radius:6px;font-weight:700;font-size:12px;">{{ $r->kelas }}</span></td>
                            <td style="padding:9px 14px;text-align:center;font-weight:800;font-size:15px;">{{ $r->total }}</td>
                            <td style="padding:9px 14px;text-align:center;color:#1d4ed8;font-weight:600;">{{ $r->laki }}</td>
                            <td style="padding:9px 14px;text-align:center;color:#be185d;font-weight:600;">{{ $r->perempuan }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-help-circle" style="font-size:15px;vertical-align:-2px;margin-right:6px;color:#f59e0b;"></i> Contoh Perubahan Rombel</span></div>
            <div class="card-body">
                <table style="width:100%;border-collapse:collapse;font-size:12px;">
                    <thead><tr style="background:#f8fafc;"><th style="padding:7px 12px;text-align:left;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;">Aksi</th><th style="padding:7px 12px;text-align:center;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;">Sekarang</th><th style="padding:7px 12px;text-align:center;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;">→ Baru</th></tr></thead>
                    <tbody>
                        @foreach([['Naik VII→VIII','7A','8A'],['Naik VII→VIII','VII A','VIII A'],['Naik VIII→IX','8B','9B'],['Lulus IX','9C','Alumni 9C'],['Tinggal Kelas','7A','7A (tetap)']] as [$al, $asal, $tujuan])
                        <tr style="border-top:1px solid #f1f5f9;">
                            <td style="padding:7px 12px;color:#374151;font-weight:600;">{{ $al }}</td>
                            <td style="padding:7px 12px;text-align:center;"><span style="background:#f1f5f9;color:#374151;padding:2px 8px;border-radius:5px;font-weight:700;">{{ $asal }}</span></td>
                            <td style="padding:7px 12px;text-align:center;"><span style="background:#dcfce7;color:#166534;padding:2px 8px;border-radius:5px;font-weight:700;">{{ $tujuan }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleTujuan(val) {
    const div = document.getElementById('div-tujuan');
    const sel = document.getElementById('sel-tujuan');
    if (val === 'naik') {
        div.style.display = 'block'; sel.required = true;
        const map = {'VII':'VIII','VIII':'IX'};
        const kelas = document.getElementById('sel-kelas').value;
        if (map[kelas]) sel.value = map[kelas];
    } else {
        div.style.display = 'none'; sel.required = false; sel.value = '';
    }
}
document.getElementById('sel-kelas').addEventListener('change', function() {
    if (document.getElementById('sel-aksi').value === 'naik') {
        const map = {'VII':'VIII','VIII':'IX'};
        const tujuan = document.getElementById('sel-tujuan');
        if (map[this.value]) tujuan.value = map[this.value];
    }
});
</script>
@endpush
@endsection
