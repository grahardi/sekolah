@extends('layouts.app')
@section('title', $siswa->nama_lengkap)
@section('page-title', 'Detail Siswa')

@section('header-actions')
    <a href="{{ route('siswa.buku-induk.pdf', $siswa) }}" class="btn btn-secondary"><i class="ti ti-printer"></i> Cetak Buku Induk</a>
    @if(auth()->user()->isAdmin())
    <a href="{{ route('siswa.edit', $siswa) }}" class="btn btn-primary"><i class="ti ti-pencil"></i> Edit</a>
    @endif
@endsection

@section('content')
@include('siswa._subnav', ['active' => 'pokok'])

<div style="display:grid;grid-template-columns:260px 1fr;gap:20px;align-items:flex-start;">
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="card" style="text-align:center;padding:24px 20px;">
            <div style="width:100px;height:125px;border-radius:10px;overflow:hidden;margin:0 auto 14px;border:2px solid #e9ecef;background:#f8fafc;">
                <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama_lengkap }}" style="width:100%;height:100%;object-fit:cover;"
                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($siswa->nama_lengkap) }}&background=dbeafe&color=1d4ed8&size=100'">
            </div>
            <h2 style="font-size:15px;font-weight:800;color:#0f172a;margin:0 0 4px;">{{ $siswa->nama_lengkap }}</h2>
            <p style="font-size:12px;color:#64748b;margin:0 0 10px;">{{ $siswa->jenis_kelamin_lengkap }}</p>
            <span class="badge badge-{{ $siswa->status }}" style="font-size:12px;padding:4px 14px;">{{ ucfirst($siswa->status) }}</span>
            <div style="margin-top:16px;padding-top:16px;border-top:1px solid #f1f5f9;display:flex;flex-direction:column;gap:8px;text-align:left;">
                <div style="display:flex;justify-content:space-between;font-size:12px;">
                    <span style="color:#94a3b8;font-weight:500;">Kelas</span>
                    <span style="font-weight:700;color:#1d4ed8;">{{ $siswa->rombel_lengkap }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;font-size:12px;">
                    <span style="color:#94a3b8;font-weight:500;">Tahun Masuk</span>
                    <span style="font-weight:600;color:#111827;">{{ $siswa->tahun_masuk }}</span>
                </div>
                @if($siswa->asal_sekolah)
                <div style="display:flex;justify-content:space-between;font-size:12px;">
                    <span style="color:#94a3b8;font-weight:500;">Asal SD</span>
                    <span style="font-weight:600;color:#111827;text-align:right;max-width:120px;">{{ $siswa->asal_sekolah }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="card" style="padding:14px;">
            <div style="display:flex;flex-direction:column;gap:8px;">
                <a href="{{ route('siswa.nilai.index', $siswa) }}" class="btn btn-secondary" style="justify-content:flex-start;"><i class="ti ti-clipboard-list"></i> Nilai Rapor</a>
                <a href="{{ route('siswa.arsip.show', $siswa) }}" class="btn btn-secondary" style="justify-content:flex-start;"><i class="ti ti-folder"></i> Arsip Berkas</a>
                <a href="{{ route('siswa.prestasi.index', $siswa) }}" class="btn btn-secondary" style="justify-content:flex-start;"><i class="ti ti-trophy"></i> Prestasi</a>
                <a href="{{ route('siswa.kartu.pdf', $siswa) }}" class="btn btn-secondary" style="justify-content:flex-start;"><i class="ti ti-id"></i> Kartu Siswa PDF</a>
                @if(auth()->user()->isAdmin())
                <div style="border-top:1px solid #f1f5f9;margin:4px 0;"></div>
                <form action="{{ route('siswa.destroy', $siswa) }}" method="POST" onsubmit="return confirm('Hapus siswa ini secara permanen?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;"><i class="ti ti-trash"></i> Hapus Siswa</button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="card">
            <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-id-badge-2" style="font-size:15px;vertical-align:-2px;margin-right:6px;color:#1d4ed8;"></i> Identitas Diri</span></div>
            <div class="card-body">
                @php
                    $rows = [
                        ['NISN', $siswa->nisn], ['NIS', $siswa->nis], ['NIK', $siswa->nik], ['No. KK', $siswa->no_kk],
                        ['Tempat Lahir', $siswa->tempat_lahir],
                        ['Tanggal Lahir', $siswa->tanggal_lahir?->locale('id')->translatedFormat('d F Y') . ' (' . $siswa->umur . ' tahun)'],
                        ['Agama', $siswa->agama], ['Golongan Darah', $siswa->golongan_darah], ['Anak ke-', $siswa->anak_ke],
                        ['No. Telepon', $siswa->no_telepon], ['Email', $siswa->email],
                    ];
                @endphp
                <div>
                    @foreach($rows as [$label,$val])
                        @if($val)
                        <div style="display:flex;padding:6px 0;border-bottom:1px solid #f8fafc;font-size:13px;">
                            <span style="width:140px;flex-shrink:0;color:#94a3b8;font-weight:600;font-size:12px;">{{ $label }}</span>
                            <span style="color:#111827;font-weight:500;">{{ $val }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-map-pin" style="font-size:15px;vertical-align:-2px;margin-right:6px;color:#1d4ed8;"></i> Alamat</span></div>
            <div class="card-body">
                <p style="font-size:13px;color:#111827;font-weight:500;margin:0 0 10px;">{{ $siswa->alamat_lengkap }}</p>
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:10px;">
                    @foreach([['RT',$siswa->rt],['RW',$siswa->rw],['Dusun',$siswa->dusun],['Kelurahan',$siswa->kelurahan],['Kecamatan',$siswa->kecamatan],['Kode Pos',$siswa->kode_pos]] as [$l,$v])
                    @if($v)
                    <div style="background:#f8fafc;border-radius:8px;padding:8px 12px;">
                        <p style="font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin:0 0 2px;">{{ $l }}</p>
                        <p style="font-size:13px;font-weight:600;color:#111827;margin:0;">{{ $v }}</p>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span style="font-size:13px;font-weight:700;color:#0f172a;"><i class="ti ti-users" style="font-size:15px;vertical-align:-2px;margin-right:6px;color:#1d4ed8;"></i> Data Orang Tua / Wali</span></div>
            <div class="card-body" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                <div>
                    <p style="font-size:11px;font-weight:800;color:#1d4ed8;text-transform:uppercase;letter-spacing:.06em;margin:0 0 10px;">Ayah</p>
                    @foreach([['Nama',$siswa->nama_ayah],['Tahun Lahir',$siswa->tahun_lahir_ayah],['Pendidikan',$siswa->pendidikan_ayah],['Pekerjaan',$siswa->pekerjaan_ayah],['Penghasilan',$siswa->penghasilan_ayah]] as [$l,$v])
                    @if($v)<div style="display:flex;padding:5px 0;font-size:12px;border-bottom:1px solid #f8fafc;"><span style="width:90px;flex-shrink:0;color:#94a3b8;font-weight:600;">{{ $l }}</span><span style="color:#111827;">{{ $v }}</span></div>@endif
                    @endforeach
                </div>
                <div>
                    <p style="font-size:11px;font-weight:800;color:#ec4899;text-transform:uppercase;letter-spacing:.06em;margin:0 0 10px;">Ibu</p>
                    @foreach([['Nama',$siswa->nama_ibu],['Tahun Lahir',$siswa->tahun_lahir_ibu],['Pendidikan',$siswa->pendidikan_ibu],['Pekerjaan',$siswa->pekerjaan_ibu],['Penghasilan',$siswa->penghasilan_ibu]] as [$l,$v])
                    @if($v)<div style="display:flex;padding:5px 0;font-size:12px;border-bottom:1px solid #f8fafc;"><span style="width:90px;flex-shrink:0;color:#94a3b8;font-weight:600;">{{ $l }}</span><span style="color:#111827;">{{ $v }}</span></div>@endif
                    @endforeach
                </div>
                @if($siswa->nama_wali)
                <div style="grid-column:span 2;padding-top:14px;border-top:1px solid #f1f5f9;">
                    <p style="font-size:11px;font-weight:800;color:#16a34a;text-transform:uppercase;letter-spacing:.06em;margin:0 0 10px;">Wali</p>
                    @foreach([['Nama',$siswa->nama_wali],['Pekerjaan',$siswa->pekerjaan_wali],['No. Telepon',$siswa->no_telepon_ortu]] as [$l,$v])
                    @if($v)<div style="display:flex;padding:5px 0;font-size:12px;border-bottom:1px solid #f8fafc;"><span style="width:110px;flex-shrink:0;color:#94a3b8;font-weight:600;">{{ $l }}</span><span style="color:#111827;">{{ $v }}</span></div>@endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
