<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mata_pelajarans', function (Blueprint $table) {
            $table->boolean('is_non_formal')->default(false)->after('urutan');
        });
    }

    public function down(): void
    {
        Schema::table('mata_pelajarans', function (Blueprint $table) {
            $table->dropColumn('is_non_formal');
        });
    }
};
