<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('survey_pesertas', function (Blueprint $table) {
            $table->string('token', 40)->unique()->nullable()->after('id');
        });

        // Isi token utk baris yang sudah ada
        DB::table('survey_pesertas')->whereNull('token')->get()->each(function ($row) {
            DB::table('survey_pesertas')->where('id', $row->id)->update(['token' => Str::random(24)]);
        });

        Schema::table('survey_jawabans', function (Blueprint $table) {
            $table->foreignId('peserta_id')->nullable()->after('survey_id')
                ->constrained('survey_pesertas')->nullOnDelete();
        });

        // Hubungkan jawaban lama ke peserta/project pertama dari survey yang sama
        // (best effort - kalau 1 survey punya >1 project, jawaban lama diarahkan
        // ke project pertama karena tidak ada info lebih spesifik).
        $jawabans = DB::table('survey_jawabans')->whereNull('peserta_id')->get();
        foreach ($jawabans as $j) {
            $peserta = DB::table('survey_pesertas')->where('survey_id', $j->survey_id)->first();
            if ($peserta) {
                DB::table('survey_jawabans')->where('id', $j->id)->update(['peserta_id' => $peserta->id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('survey_jawabans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('peserta_id');
        });
        Schema::table('survey_pesertas', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
};
