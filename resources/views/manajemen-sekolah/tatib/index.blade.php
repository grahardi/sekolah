@extends('layouts.manajemen-sekolah')
@section('title', 'Tata Tertib')

@php
    $warnaKategori = \App\Models\PelanggaranSiswa::warnaKategori();
    $daftarKategori = \App\Models\PelanggaranSiswa::daftarKategori();
@endphp

@section('content')
<h2 style="font-size:18px;font-weight:800;color:#0f172a;margin:0 0 16px;">Tata Tertib Siswa</h2>

<form method="GET" style="margin-bottom:18px;max-width:400px;display:flex;gap:8px;">
    <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari nama siswa..." class="form-input">
    <button type="submit" class="btn btn-primary"><i class="ti ti-search"></i></button>
</form>

@if($siswaList->count() > 0)
<div style="display:flex;flex-direction:column;gap:10px;margin-bottom:24px;">
    @foreach($siswaList as $siswa)
    @php $kelasLengkap = $siswa->rombel ? "{$siswa->kelas}-{$siswa->rombel}" : $siswa->kelas; @endphp
    <div class="card" style="padding:16px 20px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
            <img src="{{ $siswa->foto_url }}" style="width:42px;height:42px;border-radius:50%;object-fit:cover;">
            <div>
                <p style="font-weight:800;color:#0f172a;margin:0;font-size:14px;">{{ $siswa->nama_lengkap }}</p>
                <p style="font-size:12px;color:#64748b;margin:0;">No. Induk {{ $siswa->nis }} &middot; Kelas {{ $kelasLengkap }}</p>
            </div>
        </div>
        <p style="font-size:12px;color:#64748b;margin:0 0 8px;">Pilih jenis tindakan:</p>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
            <button type="button" onclick="bukaModalNotif({{ $siswa->id }}, {{ Js::from($siswa->nama_lengkap) }})" class="btn" style="background:#fff;border:1px solid #bfdbfe;color:#1d4ed8;justify-content:center;">
                <i class="ti ti-bell"></i> Notif Wali Kelas
            </button>
            <button type="button" onclick="bukaModalBk({{ $siswa->id }}, {{ Js::from($siswa->nama_lengkap) }})" class="btn" style="background:#fff;border:1px solid #bbf7d0;color:#15803d;justify-content:center;">
                <i class="ti ti-arrows-exchange"></i> Ajukan BK
            </button>
            <button type="button" onclick="bukaModalPelanggaran({{ $siswa->id }}, {{ Js::from($siswa->nama_lengkap) }}, {{ Js::from($siswa->nis) }}, {{ Js::from($kelasLengkap) }}, {{ Js::from($siswa->foto_url) }})" class="btn" style="background:#fff;border:1px solid #fecaca;color:#b91c1c;justify-content:center;">
                <i class="ti ti-gavel"></i> Ajukan Pelanggaran
            </button>
        </div>
    </div>
    @endforeach
</div>
@elseif($cari)
<p style="text-align:center;color:#94a3b8;padding:20px;">Tidak ada siswa ditemukan.</p>
@endif

<div class="card" style="padding:18px;margin-bottom:20px;">
    <p style="font-size:13px;font-weight:700;color:#0f172a;margin:0 0 12px;">Top 10 Poin Pelanggaran Tertinggi</p>
    <div style="display:flex;flex-wrap:wrap;gap:8px;">
        @forelse($rekapPoin as $r)
        <div style="background:#fef2f2;border-radius:8px;padding:8px 14px;">
            <span style="font-size:12px;font-weight:700;color:#0f172a;">{{ $r->siswa->nama_lengkap ?? '-' }}</span>
            <span style="font-size:12px;color:#dc2626;font-weight:800;margin-left:6px;">{{ $r->total_poin }} poin</span>
        </div>
        @empty
        <p style="font-size:12px;color:#94a3b8;">Belum ada data pelanggaran.</p>
        @endforelse
    </div>
</div>

<p style="font-size:14px;font-weight:700;color:#0f172a;margin:0 0 10px;">Riwayat Pelanggaran</p>
<div class="card">
    <table style="width:100%;border-collapse:collapse;font-size:13px;">
        <thead><tr style="text-align:left;color:#64748b;font-size:11px;text-transform:uppercase;border-bottom:1px solid #f1f5f9;">
            <th style="padding:10px 18px;">Tanggal</th><th style="padding:10px;">Siswa</th><th style="padding:10px;">Tingkat</th><th style="padding:10px;">Poin</th><th style="padding:10px;">Keterangan</th><th style="padding:10px;">Pelapor</th><th style="padding:10px;">Status</th><th style="padding:10px 18px;text-align:right;">Aksi</th>
        </tr></thead>
        <tbody>
            @forelse($riwayat as $p)
            <tr style="border-bottom:1px solid #f8fafc;">
                <td style="padding:10px 18px;color:#64748b;">{{ $p->tanggal->locale('id')->translatedFormat('d M Y') }}</td>
                <td style="padding:10px;font-weight:700;">{{ $p->siswa->nama_lengkap ?? '-' }}</td>
                <td style="padding:10px;"><span class="badge" style="background:{{ $warnaKategori[$p->kategori][0] ?? '#f1f5f9' }};color:{{ $warnaKategori[$p->kategori][1] ?? '#334155' }};">{{ $p->kategori }}</span></td>
                <td style="padding:10px;color:#dc2626;font-weight:700;">{{ $p->poin }}</td>
                <td style="padding:10px;color:#64748b;max-width:220px;">{{ $p->deskripsi ?: '-' }}</td>
                <td style="padding:10px;color:#64748b;">{{ $p->pelapor->nama ?? '-' }}</td>
                <td style="padding:10px;">
                    @if($p->status === 'Sudah Ditindak')<span class="badge badge-aktif">Sudah</span>
                    @else<span class="badge" style="background:#fef3c7;color:#92400e;">Belum</span>@endif
                </td>
                <td style="padding:10px 18px;text-align:right;">
                    <button type="button" onclick="document.getElementById('modal-tl-{{ $p->id }}').style.display='flex'" class="btn btn-secondary btn-sm"><i class="ti ti-edit"></i></button>
                    <form action="{{ route('manajemen-sekolah.tatib.destroy', $p) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus catatan ini?')">
                        @csrf @method('DELETE')<button class="btn btn-danger btn-sm"><i class="ti ti-trash"></i></button>
                    </form>
                    <div id="modal-tl-{{ $p->id }}" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:50;align-items:center;justify-content:center;padding:20px;">
                        <div class="card" style="max-width:400px;width:100%;padding:22px;text-align:left;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                                <p style="font-size:14px;font-weight:800;color:#0f172a;margin:0;">Tindak Lanjut - {{ $p->siswa->nama_lengkap }}</p>
                                <button type="button" onclick="document.getElementById('modal-tl-{{ $p->id }}').style.display='none'" style="background:none;border:none;font-size:18px;color:#94a3b8;cursor:pointer;">&times;</button>
                            </div>
                            <form action="{{ route('manajemen-sekolah.tatib.tindak-lanjut', $p) }}" method="POST">
                                @csrf @method('PUT')
                                <textarea name="tindak_lanjut" class="form-input" rows="3">{{ $p->tindak_lanjut }}</textarea>
                                <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:10px;margin-top:10px;">Simpan</button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="padding:20px;text-align:center;color:#94a3b8;">Belum ada catatan pelanggaran.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
{{ $riwayat->links() }}

{{-- ============ MODAL: Notif Wali Kelas ============ --}}
<div id="modal-notif" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:50;align-items:center;justify-content:center;padding:20px;">
    <div class="card" style="max-width:420px;width:100%;padding:24px;text-align:left;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <p id="modal-notif-judul" style="font-size:15px;font-weight:800;color:#0f172a;margin:0;">Notif Wali Kelas</p>
            <button type="button" onclick="document.getElementById('modal-notif').style.display='none'" style="background:none;border:none;font-size:18px;color:#94a3b8;cursor:pointer;">&times;</button>
        </div>
        <form action="{{ route('manajemen-sekolah.tatib.notif-wali-kelas') }}" method="POST">
            @csrf
            <input type="hidden" name="siswa_id" id="modal-notif-siswa-id">
            <label class="form-label">Pesan utk Wali Kelas</label>
            <textarea name="pesan" class="form-input" rows="3" placeholder="mis. Perlu pembinaan terkait kedisiplinan..." required></textarea>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:11px;margin-top:14px;"><i class="ti ti-bell"></i> Kirim Notifikasi</button>
        </form>
    </div>
</div>

{{-- ============ MODAL: Ajukan BK ============ --}}
<div id="modal-bk" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:50;align-items:center;justify-content:center;padding:20px;">
    <div class="card" style="max-width:420px;width:100%;padding:24px;text-align:left;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
            <p id="modal-bk-judul" style="font-size:15px;font-weight:800;color:#0f172a;margin:0;">Ajukan BK</p>
            <button type="button" onclick="document.getElementById('modal-bk').style.display='none'" style="background:none;border:none;font-size:18px;color:#94a3b8;cursor:pointer;">&times;</button>
        </div>
        <form action="{{ route('manajemen-sekolah.tatib.ajuan-bk') }}" method="POST">
            @csrf
            <input type="hidden" name="siswa_id" id="modal-bk-siswa-id">
            <label class="form-label">Alasan Pengajuan (opsional)</label>
            <textarea name="alasan" class="form-input" rows="3" placeholder="mis. Perlu konseling terkait masalah pribadi/akademik..."></textarea>
            <button type="submit" class="btn" style="width:100%;justify-content:center;padding:11px;margin-top:14px;background:#16a34a;color:#fff;"><i class="ti ti-arrows-exchange"></i> Ajukan ke BK</button>
        </form>
    </div>
</div>

{{-- ============ MODAL: Ajukan Pelanggaran ============ --}}
<div id="modal-pelanggaran" style="display:none;position:fixed;inset:0;background:rgba(15,23,42,.5);z-index:50;align-items:center;justify-content:center;padding:20px;">
    <div class="card" style="max-width:440px;width:100%;padding:24px;text-align:left;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
            <img id="modal-p-foto" src="" style="width:42px;height:42px;border-radius:50%;object-fit:cover;">
            <div>
                <p id="modal-p-nama" style="font-weight:800;color:#0f172a;margin:0;font-size:14px;"></p>
                <p id="modal-p-info" style="font-size:12px;color:#64748b;margin:0;"></p>
            </div>
        </div>
        <hr style="border:none;border-top:1px solid #f1f5f9;margin:0 0 14px;">
        <form action="{{ route('manajemen-sekolah.tatib.store') }}" method="POST">
            @csrf
            <input type="hidden" name="siswa_id" id="modal-p-siswa-id">
            <input type="hidden" name="kategori" id="modal-p-kategori" value="Ringan">

            <label class="form-label">Jenis Pelanggaran</label>
            <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;">
                @foreach($daftarKategori as $kat => $poin)
                <span onclick="pilihKategori('{{ $kat }}', {{ $poin }})" id="pill-kat-{{ $kat }}"
                      style="cursor:pointer;padding:7px 16px;border-radius:20px;font-size:13px;font-weight:700;border:2px solid {{ $warnaKategori[$kat][0] }};background:{{ $kat === 'Ringan' ? $warnaKategori[$kat][0] : '#fff' }};color:{{ $warnaKategori[$kat][1] }};">
                    {{ $kat }}
                </span>
                @endforeach
            </div>

            <label class="form-label">Poin</label>
            <input type="number" name="poin" id="modal-p-poin" class="form-input" min="0" required style="margin-bottom:14px;">

            <label class="form-label">Keterangan Pelanggaran</label>
            <textarea name="deskripsi" class="form-input" rows="3" placeholder="Ceritakan kejadiannya..." style="margin-bottom:16px;"></textarea>

            <div style="display:flex;gap:8px;">
                <button type="button" onclick="document.getElementById('modal-pelanggaran').style.display='none'" class="btn btn-secondary" style="flex:1;justify-content:center;">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex:2;justify-content:center;"><i class="ti ti-device-floppy"></i> Simpan Laporan</button>
            </div>
        </form>
    </div>
</div>

<script>
    const WARNA_KAT = @json($warnaKategori);

    function bukaModalNotif(siswaId, nama) {
        document.getElementById('modal-notif-judul').textContent = 'Notif Wali Kelas - ' + nama;
        document.getElementById('modal-notif-siswa-id').value = siswaId;
        document.getElementById('modal-notif').style.display = 'flex';
    }

    function bukaModalBk(siswaId, nama) {
        document.getElementById('modal-bk-judul').textContent = 'Ajukan BK - ' + nama;
        document.getElementById('modal-bk-siswa-id').value = siswaId;
        document.getElementById('modal-bk').style.display = 'flex';
    }

    function bukaModalPelanggaran(siswaId, nama, nis, kelas, foto) {
        document.getElementById('modal-p-siswa-id').value = siswaId;
        document.getElementById('modal-p-nama').textContent = nama;
        document.getElementById('modal-p-info').textContent = `No. Induk ${nis} · Kelas ${kelas}`;
        document.getElementById('modal-p-foto').src = foto;
        pilihKategori('Ringan', {{ $daftarKategori['Ringan'] }});
        document.getElementById('modal-pelanggaran').style.display = 'flex';
    }

    function pilihKategori(kat, poin) {
        document.getElementById('modal-p-kategori').value = kat;
        document.getElementById('modal-p-poin').value = poin;
        Object.keys(WARNA_KAT).forEach((k) => {
            const el = document.getElementById('pill-kat-' + k);
            el.style.background = (k === kat) ? WARNA_KAT[k][0] : '#fff';
        });
    }
</script>
@endsection
