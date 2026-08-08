<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'DejaVu Sans', sans-serif; font-size:9.5pt; color:#000; background:#fff; }
.page { padding:10mm 20mm 10mm 20mm; }
.halaman-baru { page-break-before: always; }
.judul-dok { text-align:center; margin-bottom:10px; width:100%; }
.judul-dok h2 { font-size:12pt; font-weight:bold; text-transform:uppercase; letter-spacing:2px; border:2.5px solid #000; display:inline-block; padding:5px 18px; }
.judul-dok p  { font-size:9pt; margin-top:5px; }
.no-induk     { text-align:left; font-size:8.5pt; margin-bottom:10px; }
.sek { background:#000; color:#fff; font-weight:bold; font-size:9pt; padding:3px 8px; margin:10px 0 0; letter-spacing:.5px; }
.section-blok { page-break-inside: avoid; margin-top: 8mm; }
table.data { width:100%; border-collapse:collapse; }
table.data td { padding:2.5px 6px; font-size:9pt; vertical-align:top; line-height:1.4; }
table.data td.no  { width:22px; text-align:right; padding-right:4px; }
table.data td.lbl { width:32%; text-align:left; }
table.data td.ttd { width:6px; text-align:center; }
table.data td.val { border-bottom:1px solid #777; }
table.nilai { width:100%; border-collapse:collapse; font-size:8.5pt; margin-top:4px; }
table.nilai th { background:#333; color:#fff; padding:4px 6px; text-align:center; border:1px solid #555; font-size:8pt; }
table.nilai td { padding:3px 6px; border:1px solid #aaa; text-align:center; }
table.nilai td.mapel { text-align:left; }
table.nilai tr.alt td { background:#f5f5f5; }
.foto-section { margin-top:14px; text-align:center; }
.foto-frame { width:90px; height:115px; border:2px solid #333; display:inline-block; overflow:hidden; vertical-align:top; }
.foto-frame img { width:100%; height:100%; object-fit:cover; object-position:top center; }
</style>
</head>
<body>
<div class="page">

@include('siswa._pdf-buku-induk-body', ['siswa' => $siswa])


</div>
</body>
</html>
