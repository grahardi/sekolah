@extends('layouts.bk')
@section('title', 'Hasil Survey - ' . $siswa->nama_lengkap)
@section('page-title', 'Jawaban: ' . $siswa->nama_lengkap)

@section('header-actions')
    <a href="{{ route('bk.peserta.show', $project) }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<p style="font-size:13px;color:#64748b;margin:-10px 0 18px;">
    {{ $project->survey->judul }} &middot; Diisi pada {{ $jawaban->submitted_at->format('d F Y, H:i') }}
</p>

<div style="display:flex;flex-direction:column;gap:12px;max-width:760px;">
    @foreach($project->survey->pertanyaans as $p)
    @php $jawabanSiswa = $jawaban->data[$p->id] ?? null; @endphp
    <div class="card" style="padding:16px;">
        @if($p->kategori)<span class="badge" style="background:#eff6ff;color:#2563EB;margin-bottom:6px;">{{ $p->kategori }}</span>@endif
        <p style="font-size:13px;font-weight:700;color:#0f172a;margin:4px 0 8px;">{{ $p->teks_pertanyaan }}</p>
        <div style="background:#f8fafc;border-radius:8px;padding:10px 12px;font-size:13px;color:#334155;">
            @if($p->tipe_jawaban === 'checklist' && is_array($jawabanSiswa))
                @if(count($jawabanSiswa) > 0)
                    <ul style="margin:0;padding-left:18px;">@foreach($jawabanSiswa as $j)<li>{{ $j }}</li>@endforeach</ul>
                @else
                    <span style="color:#94a3b8;">Tidak ada yang dipilih</span>
                @endif
            @elseif($jawabanSiswa)
                {{ $jawabanSiswa }}
            @else
                <span style="color:#94a3b8;">Tidak dijawab</span>
            @endif
        </div>
    </div>
    @endforeach
</div>
@endsection
