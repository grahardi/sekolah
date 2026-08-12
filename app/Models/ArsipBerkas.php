<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ArsipBerkas extends Model
{
    protected $table = 'arsip_berkas';

    protected $fillable = [
        'siswa_id',
        'foto','kartu_keluarga','akta_lahir','ijazah_sd',
        'ijazah','transkrip_nilai','sertifikat_tka',
        'berkas_lain',
        'catatan',
    ];

    protected $casts = [
        'berkas_lain' => 'array',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public static function berkasAktif(): array
    {
        return [
            'foto'           => ['label' => 'Foto Siswa',     'icon' => 'ti-photo'],
            'kartu_keluarga' => ['label' => 'Kartu Keluarga', 'icon' => 'ti-id'],
            'akta_lahir'     => ['label' => 'Akta Lahir',     'icon' => 'ti-certificate'],
            'ijazah_sd'      => ['label' => 'Ijazah SD/MI',   'icon' => 'ti-school'],
        ];
    }

    public static function berkasLulus(): array
    {
        return [
            'ijazah'          => ['label' => 'Ijazah SMP',      'icon' => 'ti-certificate-2'],
            'transkrip_nilai' => ['label' => 'Transkrip Nilai',  'icon' => 'ti-report'],
            'sertifikat_tka'  => ['label' => 'Sertifikat TKA',  'icon' => 'ti-rosette'],
        ];
    }

    public function isImage(string $field): bool
    {
        $path = $this->$field;
        if (!$path) return false;
        return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp']);
    }

    public function getUrl(string $field): ?string
    {
        return $this->$field ? Storage::disk('public')->url($this->$field) : null;
    }

    /** Tambah 1 entri ke berkas_lain (dipanggil dari import) */
    public function tambahBerkasLain(string $path, string $namaAsli, ?string $label = null): void
    {
        $daftar = $this->berkas_lain ?? [];
        $daftar[] = ['path' => $path, 'nama_asli' => $namaAsli, 'label' => $label];
        $this->berkas_lain = $daftar;
        $this->save();
    }

    /** Ubah label 1 entri berkas_lain berdasarkan index-nya di array */
    public function updateLabelBerkasLain(int $index, string $label): bool
    {
        // array_values() dulu - jaga2 ada key non-sequential dari proses
        // sebelumnya (mis. bekas unset() yg gak ke-reindex), krn kalau key
        // aslinya udah geser, index dari tampilan gak nyambung ke elemen yg benar.
        $daftar = array_values($this->berkas_lain ?? []);
        if (! array_key_exists($index, $daftar)) return false;

        $daftar[$index]['label'] = $label;

        return $this->newQuery()->where('id', $this->id)->update(['berkas_lain' => $daftar]) > 0;
    }

    /** Pindahkan 1 entri berkas_lain ke field spesifik (KK/Akta/dst), hapus dari array */
    public function pindahkanBerkasLain(int $index, string $fieldTujuan): ?string
    {
        $daftar = array_values($this->berkas_lain ?? []);
        if (! array_key_exists($index, $daftar)) return null;

        $path = $daftar[$index]['path'];
        unset($daftar[$index]);
        $daftar = array_values($daftar);

        $this->newQuery()->where('id', $this->id)->update([
            'berkas_lain' => $daftar,
            $fieldTujuan => $path,
        ]);

        return $path;
    }

    public function berkasLainUrls(): array
    {
        return collect($this->berkas_lain ?? [])->map(fn ($b, $i) => [
            'index' => $i,
            'url' => Storage::disk('public')->url($b['path']),
            'nama_asli' => $b['nama_asli'],
            'label' => $b['label'] ?? null,
            'is_image' => in_array(strtolower(pathinfo($b['path'], PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp']),
        ])->toArray();
    }
}
