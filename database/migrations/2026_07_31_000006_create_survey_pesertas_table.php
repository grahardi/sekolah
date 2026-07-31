<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_pesertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->string('target_kelas'); // mis. "7-A,7-B,8-A"
            $table->timestamps();
        });

        // Migrasi data lama: kalau survey yang sudah ada punya target_kelas
        // di kolom lama, pindahkan jadi 1 baris peserta pertama supaya tidak
        // hilang datanya.
        $old = DB::table('surveys')->whereNotNull('target_kelas')->get(['id', 'target_kelas']);
        foreach ($old as $s) {
            DB::table('survey_pesertas')->insert([
                'survey_id' => $s->id,
                'target_kelas' => $s->target_kelas,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_pesertas');
    }
};
