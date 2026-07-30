@extends('layouts.app')

@section('title', 'Daftar Siswa')
@section('page-title', 'Buku Induk Siswa')

@section('header-actions')
    @if(auth()->user()->isAdmin())
    <a href="{{ route('siswa.export.choice', request()->query()) }}" class="btn btn-secondary">
        <i class="ti ti-download"></i> Export
    </a>
    <a href="{{ route('siswa.create') }}" class="btn btn-primary">
        <i class="ti ti-user-plus"></i> Tambah Siswa
    </a>
    @endif
@endsection

@section('content')

<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
    <div class="card" style="padding:18px 20px;">
        <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Total Siswa</p>
        <p style="font-size:28px;font-weight:800;color:#0f172a;line-height:1;">{{ $totalSiswa }}</p>
        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Semua status</p>
    </div>
    <div class="card" style="padding:18px 20px;border-left:3px solid #22c55e;">
        <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Aktif</p>
        <p style="font-size:28px;font-weight:800;color:#16a34a;line-height:1;">{{ $totalAktif }}</p>
        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Siswa aktif</p>
    </div>
    <div class="card" style="padding:18px 20px;border-left:3px solid #3b82f6;">
        <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Laki-laki</p>
        <p style="font-size:28px;font-weight:800;color:#1d4ed8;line-height:1;">{{ $totalLaki }}</p>
        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Siswa putra</p>
    </div>
    <div class="card" style="padding:18px 20px;border-left:3px solid #ec4899;">
        <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Perempuan</p>
        <p style="font-size:28px;font-weight:800;color:#be185d;line-height:1;">{{ $totalPerempu }}</p>
        <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Siswa putri</p>
    </div>
</div>

<div class="card" style="margin-bottom:20px;">
    <div class="card-body">
        <form method="GET" action="{{ route('siswa.index') }}"
              style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;">
            <div style="flex:1;min-width:200px;">
                <label class="form-label">Cari Siswa</label>
                <div style="position:relative;">
                    <i class="ti ti-search" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:16px;"></i>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="Nama, NISN, atau NIS..."
                           class="form-input" style="padding-left:34px;">
                </div>
            </div>
            <div style="min-width:120px;">
                <label class="form-label">Kelas</label>
                <select name="kelas" class="form-input">
                    <option value="">Semua</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas }}" {{ ($filters['kelas'] ?? '') == $kelas ? 'selected' : '' }}>{{ $kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:130px;">
                <label class="form-label">Status</label>
                <select name="status" class="form-input">
                    <option value="">Semua</option>
                    @foreach(['aktif','lulus','keluar','pindah'] as $st)
                        <option value="{{ $st }}" {{ ($filters['status'] ?? '') == $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:130px;">
                <label class="form-label">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-input">
                    <option value="">Semua</option>
                    <option value="L" {{ ($filters['jenis_kelamin'] ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ ($filters['jenis_kelamin'] ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div style="min-width:130px;">
                <label class="form-label">Tahun Masuk</label>
                <select name="tahun_masuk" class="form-input">
                    <option value="">Semua</option>
                    @foreach($tahunList as $tahun)
                        <option value="{{ $tahun }}" {{ ($filters['tahun_masuk'] ?? '') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-primary"><i class="ti ti-filter"></i> Filter</button>
                @if(array_filter($filters ?? []))
                    <a href="{{ route('siswa.index') }}" class="btn btn-secondary"><i class="ti ti-x"></i> Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <span style="font-size:13px;font-weight:600;color:#374151;">
            Menampilkan <strong>{{ $siswas->firstItem() ?? 0 }}–{{ $siswas->lastItem() ?? 0 }}</strong>
            dari <strong>{{ $siswas->total() }}</strong> siswa
        </span>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('siswa.import.form') }}" style="font-size:12px;color:#1d4ed8;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:4px;">
            <i class="ti ti-file-import" style="font-size:15px;"></i> Import Excel
        </a>
        @endif
    </div>

    @if($siswas->isEmpty())
        <div style="padding:60px;text-align:center;">
            <i class="ti ti-users" style="font-size:52px;color:#e2e8f0;display:block;margin-bottom:14px;"></i>
            <p style="font-size:15px;font-weight:700;color:#374151;margin-bottom:6px;">Belum ada data siswa</p>
            <p style="font-size:13px;color:#94a3b8;margin-bottom:18px;">Mulai dengan menambah siswa atau import dari Excel</p>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('siswa.create') }}" class="btn btn-primary"><i class="ti ti-user-plus"></i> Tambah Siswa</a>
            @endif
        </div>
    @else
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8fafc;">
                        <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">No</th>
                        <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Siswa</th>
                        <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">NISN / NIS</th>
                        <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Kelas</th>
                        <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Tgl. Lahir</th>
                        <th style="padding:11px 16px;text-align:left;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Status</th>
                        <th style="padding:11px 16px;text-align:center;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($siswas as $i => $siswa)
                    <tr style="border-top:1px solid #f1f5f9;">
                        <td style="padding:12px 16px;font-size:12px;color:#94a3b8;">{{ $siswas->firstItem() + $i }}</td>
                        <td style="padding:12px 16px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:9px;overflow:hidden;background:#dbeafe;flex-shrink:0;">
                                    <img src="{{ $siswa->foto_url }}" alt="{{ $siswa->nama_lengkap }}"
                                         style="width:100%;height:100%;object-fit:cover;"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($siswa->nama_lengkap) }}&background=dbeafe&color=1d4ed8&size=36'">
                                </div>
                                <div>
                                    <p style="font-size:13px;font-weight:700;color:#111827;margin:0;">{{ $siswa->nama_lengkap }}</p>
                                    <p style="font-size:11px;color:#94a3b8;margin:0;">{{ $siswa->jenis_kelamin_lengkap }}</p>
                                </div>
                            </div>
                        </td>
                        <td style="padding:12px 16px;">
                            <p style="font-family:monospace;font-size:12px;font-weight:700;color:#374151;margin:0;">{{ $siswa->nisn }}</p>
                            @if($siswa->nis)<p style="font-family:monospace;font-size:11px;color:#94a3b8;margin:0;">{{ $siswa->nis }}</p>@endif
                        </td>
                        <td style="padding:12px 16px;">
                            <span style="background:#eff6ff;color:#1d4ed8;padding:3px 10px;border-radius:6px;font-size:12px;font-weight:700;">
                                {{ $siswa->rombel_lengkap }}
                            </span>
                        </td>
                        <td style="padding:12px 16px;font-size:12px;color:#374151;">
                            {{ $siswa->tempat_lahir }},<br>
                            <span style="color:#64748b;">{{ $siswa->tanggal_lahir->format('d M Y') }}</span>
                        </td>
                        <td style="padding:12px 16px;">
                            <span class="badge badge-{{ $siswa->status }}">{{ ucfirst($siswa->status) }}</span>
                        </td>
                        <td style="padding:12px 16px;">
                            <div style="display:flex;align-items:center;justify-content:center;gap:4px;">
                                <a href="{{ route('siswa.show', $siswa) }}" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;background:#f1f5f9;color:#374151;text-decoration:none;" title="Detail">
                                    <i class="ti ti-eye" style="font-size:15px;"></i>
                                </a>
                                @if(auth()->user()->isAdmin())
                                <a href="{{ route('siswa.edit', $siswa) }}" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;background:#eff6ff;color:#1d4ed8;text-decoration:none;" title="Edit">
                                    <i class="ti ti-pencil" style="font-size:15px;"></i>
                                </a>
                                @endif
                                <a href="{{ route('siswa.arsip.show', $siswa) }}" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;background:#f0fdf4;color:#16a34a;text-decoration:none;" title="Arsip Berkas">
                                    <i class="ti ti-folder" style="font-size:15px;"></i>
                                </a>
                                <a href="{{ route('siswa.buku-induk.pdf', $siswa) }}" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;background:#fff7ed;color:#c2410c;text-decoration:none;" title="Cetak Buku Induk">
                                    <i class="ti ti-printer" style="font-size:15px;"></i>
                                </a>
                                @if(auth()->user()->isAdmin())
                                <form action="{{ route('siswa.destroy', $siswa) }}" method="POST" style="display:inline;"
                                      onsubmit="return confirm('Hapus siswa {{ addslashes($siswa->nama_lengkap) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;background:#fef2f2;color:#dc2626;border:none;cursor:pointer;" title="Hapus">
                                        <i class="ti ti-trash" style="font-size:15px;"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($siswas->hasPages())
        <div style="padding:14px 18px;border-top:1px solid #f1f5f9;">
            {{ $siswas->links() }}
        </div>
        @endif
    @endif
</div>

@endsection
