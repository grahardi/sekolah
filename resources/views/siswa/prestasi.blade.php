@extends('layouts.app')
@section('title', 'Prestasi — ' . $siswa->nama_lengkap)
@section('page-title', 'Prestasi Siswa')

@section('header-actions')
    <a href="{{ route('siswa.show', $siswa) }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
@include('siswa._subnav', ['active' => 'prestasi'])

<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:flex-start;">
    <div class="card">
        <div class="card-header">
            <span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-trophy" style="font-size:16px;vertical-align:-3px;margin-right:6px;color:#f59e0b;"></i> Daftar Prestasi</span>
            <span style="font-size:12px;color:#94a3b8;">{{ $prestasis->total() }} prestasi</span>
        </div>

        @if($prestasis->isEmpty())
            <div style="padding:48px;text-align:center;color:#94a3b8;">
                <i class="ti ti-trophy" style="font-size:48px;display:block;margin-bottom:12px;color:#e9ecef;"></i>
                <p style="font-size:14px;font-weight:600;color:#374151;">Belum ada data prestasi</p>
            </div>
        @else
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:12px;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;">Tanggal</th>
                            <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;">Kegiatan</th>
                            <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;">Tingkat</th>
                            <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;">Juara</th>
                            <th style="padding:10px 14px;text-align:left;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;">Sertifikat</th>
                            <th style="padding:10px 14px;text-align:center;font-size:10px;font-weight:700;color:#64748b;text-transform:uppercase;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prestasis as $p)
                        @php
                            $colors = ['Internasional'=>['bg'=>'#f3e8ff','color'=>'#7c3aed'],'Nasional'=>['bg'=>'#fee2e2','color'=>'#991b1b'],'Provinsi'=>['bg'=>'#dbeafe','color'=>'#1e40af'],'Kabupaten/Kota'=>['bg'=>'#dcfce7','color'=>'#166534'],'Kecamatan'=>['bg'=>'#fef9c3','color'=>'#854d0e'],'Sekolah'=>['bg'=>'#f1f5f9','color'=>'#475569']];
                            $c = $colors[$p->tingkat_lomba] ?? $colors['Sekolah'];
                        @endphp
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:10px 14px;color:#374151;white-space:nowrap;">{{ $p->tanggal_kegiatan->format('d M Y') }}</td>
                            <td style="padding:10px 14px;">
                                <p style="font-weight:700;color:#111827;margin:0;">{{ $p->jenis_lomba }}</p>
                                @if($p->penyelenggara)<p style="font-size:11px;color:#94a3b8;margin:0;">{{ $p->penyelenggara }}</p>@endif
                            </td>
                            <td style="padding:10px 14px;white-space:nowrap;"><span style="background:{{ $c['bg'] }};color:{{ $c['color'] }};padding:3px 8px;border-radius:999px;font-size:11px;font-weight:700;">{{ $p->tingkat_lomba }}</span></td>
                            <td style="padding:10px 14px;font-weight:700;color:#111827;white-space:nowrap;">🏅 {{ $p->juara }}</td>
                            <td style="padding:10px 14px;">
                                @if($p->sertifikat)
                                    <a href="{{ $p->sertifikat_url }}" target="_blank" class="btn btn-secondary btn-sm"><i class="ti ti-paperclip"></i> Lihat</a>
                                @elseif(auth()->user()->isAdmin())
                                    <form action="{{ route('siswa.prestasi.sertifikat', [$siswa, $p]) }}" method="POST" enctype="multipart/form-data" style="display:inline;">
                                        @csrf
                                        <label class="btn btn-secondary btn-sm" style="cursor:pointer;">
                                            <i class="ti ti-upload"></i> Upload
                                            <input type="file" name="sertifikat" accept=".jpg,.jpeg,.png,.pdf" style="display:none;" onchange="this.closest('form').submit()">
                                        </label>
                                    </form>
                                @else
                                    <span style="color:#cbd5e1;font-size:11px;">—</span>
                                @endif
                            </td>
                            <td style="padding:10px 14px;text-align:center;">
                                @if(auth()->user()->isAdmin())
                                <form action="{{ route('siswa.prestasi.destroy', [$siswa, $p]) }}" method="POST" onsubmit="return confirm('Hapus prestasi ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#991b1b;border:none;cursor:pointer;"><i class="ti ti-trash"></i></button>
                                </form>
                                @else
                                <span style="color:#cbd5e1;">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="padding:14px 16px;border-top:1px solid #f1f5f9;">{{ $prestasis->links() }}</div>
        @endif
    </div>

@if(auth()->user()->isAdmin())
        <div class="card" style="position:sticky;top:78px;">
        <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-plus" style="font-size:16px;vertical-align:-3px;margin-right:6px;color:#1d4ed8;"></i> Tambah Prestasi</span></div>
        <div class="card-body">
            <form action="{{ route('siswa.prestasi.store', $siswa) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div>
                        <label class="form-label">Tanggal Kegiatan <span style="color:#ef4444">*</span></label>
                        <input type="date" name="tanggal_kegiatan" value="{{ old('tanggal_kegiatan') }}" class="form-input" required>
                        @error('tanggal_kegiatan')<p style="color:#ef4444;font-size:11px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Jenis Lomba / Kegiatan <span style="color:#ef4444">*</span></label>
                        <input type="text" name="jenis_lomba" value="{{ old('jenis_lomba') }}" class="form-input" placeholder="mis: Olimpiade Matematika" required>
                        @error('jenis_lomba')<p style="color:#ef4444;font-size:11px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Tingkat Lomba <span style="color:#ef4444">*</span></label>
                        <select name="tingkat_lomba" class="form-input" required>
                            <option value="">-- Pilih --</option>
                            @foreach(\App\Models\Prestasi::tingkatList() as $t)<option value="{{ $t }}" {{ old('tingkat_lomba') == $t ? 'selected' : '' }}>{{ $t }}</option>@endforeach
                        </select>
                        @error('tingkat_lomba')<p style="color:#ef4444;font-size:11px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Juara / Peringkat <span style="color:#ef4444">*</span></label>
                        <input type="text" name="juara" value="{{ old('juara') }}" class="form-input" placeholder="mis: Juara 1" required>
                        @error('juara')<p style="color:#ef4444;font-size:11px;margin-top:4px;">{{ $message }}</p>@enderror
                    </div>
                    <div><label class="form-label">Penyelenggara</label><input type="text" name="penyelenggara" value="{{ old('penyelenggara') }}" class="form-input"></div>
                    <div><label class="form-label">Keterangan</label><textarea name="keterangan" rows="2" class="form-input">{{ old('keterangan') }}</textarea></div>
                    <div>
                        <label class="form-label">Upload Sertifikat</label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;padding:8px 12px;border:1px dashed #d1d5db;border-radius:8px;background:#f9fafb;">
                            <i class="ti ti-paperclip" style="font-size:16px;color:#1d4ed8;"></i>
                            <span style="font-size:12px;color:#374151;" id="sertifikat-label">Pilih file...</span>
                            <input type="file" name="sertifikat" accept=".jpg,.jpeg,.png,.pdf" style="display:none;" onchange="document.getElementById('sertifikat-label').textContent = this.files[0]?.name ?? 'Pilih file...'">
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;"><i class="ti ti-plus"></i> Tambah Prestasi</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection