<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            // Nomor seri ijazah dari Manajemen Ijazah Dapodik/Penerbitan NIN -
            // beda dari no_sttb_sd/no_un_sd yg utk ijazah SD sebelumnya.
            $table->string('no_ijazah', 50)->nullable()->after('no_un_sd');
        });
    }

    public function down(): void
    {
        Schema::table('siswas', function (Blueprint $table) {
            $table->dropColumn('no_ijazah');
        });
    }
};
