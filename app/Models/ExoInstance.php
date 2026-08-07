<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExoInstance extends Model
{
    protected $table = 'exo_instances';
    protected $fillable = ['nama', 'slug', 'path', 'port', 'is_aktif', 'terakhir_dijalankan'];
    protected $casts = ['is_aktif' => 'boolean', 'terakhir_dijalankan' => 'datetime'];

    private function envPath(): string
    {
        return rtrim($this->path, '/') . '/.env';
    }

    /** Baca 1 variabel dari .env instance ini (mis. SERVER_PORT) */
    public function bacaEnv(string $key): ?string
    {
        $path = $this->envPath();
        if (! file_exists($path)) return null;

        foreach (file($path) as $line) {
            if (str_starts_with(trim($line), "{$key}=")) {
                return trim(substr(trim($line), strlen($key) + 1));
            }
        }
        return null;
    }

    /**
     * Tulis 1 variabel ke .env instance ini - GANTI baris yg ada, JANGAN
     * sentuh baris lain. Kalau variabelnya belum ada, ditambahkan di akhir.
     */
    public function tulisEnv(string $key, string $value): bool
    {
        $path = $this->envPath();
        if (! file_exists($path)) return false;

        $lines = file($path);
        $ditemukan = false;
        foreach ($lines as $i => $line) {
            if (str_starts_with(trim($line), "{$key}=")) {
                $lines[$i] = "{$key}={$value}\n";
                $ditemukan = true;
                break;
            }
        }
        if (! $ditemukan) {
            $lines[] = "{$key}={$value}\n";
        }

        return file_put_contents($path, implode('', $lines)) !== false;
    }
}
