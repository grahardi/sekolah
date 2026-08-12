<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scan_kk_hasil', function (Blueprint $table) {
            // Beda dgn discan_at (waktu OCR jalan) - ini waktu ADMIN benar2
            // konfirmasi (klik Terapkan minimal 1 field, ATAU Tandai Update
            // kalau semua data sudah sama persis). Baru dari sini "Sudah
            // Update KK" boleh dianggap benar.
            $table->timestamp('dikonfirmasi_at')->nullable()->after('discan_at');
            $table->foreignId('dikonfirmasi_oleh_user_id')->nullable()->after('dikonfirmasi_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('scan_kk_hasil', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dikonfirmasi_oleh_user_id');
            $table->dropColumn('dikonfirmasi_at');
        });
    }
};
