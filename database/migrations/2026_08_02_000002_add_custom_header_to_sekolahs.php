<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->string('rapor_header_custom')->nullable()->after('watermark_rapor');
            $table->boolean('rapor_pakai_header_custom')->default(false)->after('rapor_header_custom');
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            $table->dropColumn(['rapor_header_custom', 'rapor_pakai_header_custom']);
        });
    }
};
