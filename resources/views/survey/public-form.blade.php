<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $survey->judul }}</title>
<link rel="icon" type="image/x-icon" href="/favicon.ico">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@700&display=swap" rel="stylesheet">
<style>
    * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
    body { margin:0; background:#F5F9FF; color:#1E293B; padding:20px; }
    .wrap { max-width:640px; margin:0 auto; }
    .header { text-align:center; margin-bottom:24px; }
    .header h1 { font-family:'Space Grotesk',sans-serif; font-size:20px; margin:8px 0 4px; }
    .header p { font-size:13px; color:#64748b; margin:0; }
    .card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:20px; margin-bottom:16px; }
    .form-label { display:block; font-size:13px; font-weight:700; color:#1E293B; margin-bottom:8px; }
    .form-input { width:100%; border:1px solid #cbd5e1; border-radius:8px; padding:10px 12px; font-size:14px; }
    .kategori-tag { display:inline-block; background:#eff6ff; color:#2563EB; font-size:11px; font-weight:700; border-radius:999px; padding:2px 10px; margin-bottom:8px; }
    .opsi-label { display:flex; align-items:center; gap:8px; padding:8px 10px; border:1px solid #e2e8f0; border-radius:8px; margin-bottom:6px; cursor:pointer; font-size:13px; }
    .opsi-label:hover { background:#f8fafc; }
    .skala-row { display:flex; gap:8px; }
    .skala-opt { flex:1; text-align:center; }
    .skala-opt input { display:none; }
    .skala-opt span { display:block; border:1px solid #cbd5e1; border-radius:8px; padding:10px 0; font-weight:700; cursor:pointer; }
    .skala-opt input:checked + span { background:#2563EB; color:#fff; border-color:#2563EB; }
    .btn-submit { width:100%; background:#FBBF24; color:#1E293B; font-weight:700; border:none; border-radius:10px; padding:14px; font-size:15px; cursor:pointer; }
    textarea.form-input { resize:vertical; min-height:70px; }
</style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <img src="/images/logo-icon.png" alt="" style="height:40px;">
        <h1>{{ $survey->judul }}</h1>
        @if($survey->deskripsi)<p>{{ $survey->deskripsi }}</p>@endif
    </div>

    <form action="{{ route('survey.public.submit', $project->token) }}" method="POST">
        @csrf
        <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">

        <div class="card" style="background:#eff6ff;border-color:#bfdbfe;">
            <p style="font-size:12px;color:#1e40af;margin:0;">Mengisi sebagai:</p>
            <p style="font-weight:700;color:#1e40af;margin:2px 0 0;">{{ $siswa->nama_lengkap }} ({{ $siswa->rombel_lengkap }})</p>
        </div>

        @foreach($survey->pertanyaans as $i => $p)
        <div class="card">
            @if($p->kategori)<span class="kategori-tag">{{ $p->kategori }}</span>@endif
            <label class="form-label">{{ $i + 1 }}. {{ $p->teks_pertanyaan }}</label>

            @if($p->tipe_jawaban === 'pilihan_ganda')
                @foreach($p->opsi ?? [] as $opsi)
                <label class="opsi-label"><input type="radio" name="jawaban_{{ $p->id }}" value="{{ $opsi }}"> {{ $opsi }}</label>
                @endforeach
            @elseif($p->tipe_jawaban === 'checklist')
                @foreach($p->opsi ?? [] as $opsi)
                <label class="opsi-label"><input type="checkbox" name="jawaban_{{ $p->id }}[]" value="{{ $opsi }}"> {{ $opsi }}</label>
                @endforeach
            @elseif($p->tipe_jawaban === 'skala')
                <div class="skala-row">
                    @for($n = 1; $n <= 5; $n++)
                    <label class="skala-opt">
                        <input type="radio" name="jawaban_{{ $p->id }}" value="{{ $n }}">
                        <span>{{ $n }}</span>
                    </label>
                    @endfor
                </div>
            @else
                <textarea name="jawaban_{{ $p->id }}" class="form-input" placeholder="Tulis jawabanmu..."></textarea>
            @endif
        </div>
        @endforeach

        <button type="submit" class="btn-submit">Kirim Jawaban</button>
    </form>
</div>
</body>
</html>
