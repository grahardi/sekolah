<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            // Flag role granular ala simt-spenza - 1 guru bisa punya beberapa
            // flag sekaligus (mis. guru mapel biasa YANG JUGA piket).
            $table->boolean('is_piket')->default(false)->after('keterangan');
            $table->boolean('is_tatib')->default(false)->after('is_piket');
            $table->boolean('is_bk')->default(false)->after('is_tatib');
            $table->boolean('is_kebersihan')->default(false)->after('is_bk');
            $table->boolean('is_keagamaan')->default(false)->after('is_kebersihan');
            $table->boolean('is_kepsek')->default(false)->after('is_keagamaan');
        });
    }

    public function down(): void
    {
        Schema::table('gurus', function (Blueprint $table) {
            $table->dropColumn(['is_piket', 'is_tatib', 'is_bk', 'is_kebersihan', 'is_keagamaan', 'is_kepsek']);
        });
    }
};
