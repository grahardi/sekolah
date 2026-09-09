@extends('layouts.erapor')
@section('title', 'Urutan Mapel')
@section('page-title', 'Urutan Mata Pelajaran')

@section('content')

<p style="font-size:13px;color:#64748b;margin:-8px 0 16px;max-width:600px;">
    Seret (drag) mata pelajaran untuk mengatur urutannya. Urutan ini dipakai saat cetak rapor - mata pelajaran akan dicetak sesuai urutan di sini, dari atas ke bawah.
</p>

<div id="pesan-simpan" style="display:none;background:#dcfce7;color:#166534;padding:10px 16px;border-radius:8px;margin-bottom:16px;font-size:13px;">
    <i class="ti ti-check"></i> Urutan tersimpan otomatis.
</div>

<div class="card" style="padding:0;overflow:hidden;max-width:520px;">
    <ul id="list-mapel" style="list-style:none;margin:0;padding:0;">
        @foreach($mapels as $m)
        <li data-id="{{ $m->id }}" style="padding:12px 16px;border-top:{{ $loop->first ? 'none' : '1px solid #f1f5f9' }};display:flex;align-items:center;gap:12px;background:#fff;cursor:grab;">
            <i class="ti ti-grip-vertical" style="color:#cbd5e1;font-size:18px;"></i>
            <span style="font-size:13px;color:#0f172a;font-weight:600;">{{ $m->nama }}</span>
            @if($m->is_agama)<span style="font-size:10px;background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:20px;margin-left:auto;">Agama</span>@endif
        </li>
        @endforeach
    </ul>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
<script>
const list = document.getElementById('list-mapel');
new Sortable(list, {
    animation: 150,
    ghostClass: 'sortable-ghost',
    onEnd: function () {
        const urutan = Array.from(list.children).map(li => li.dataset.id);
        fetch("{{ route('erapor.mata-pelajaran.urutan-simpan') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            },
            body: JSON.stringify({ urutan: urutan }),
        }).then(() => {
            const pesan = document.getElementById('pesan-simpan');
            pesan.style.display = 'block';
            setTimeout(() => pesan.style.display = 'none', 2000);
        });
    },
});
</script>

@endsection
