<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            if (! Schema::hasColumn('sekolahs', 'is_demo')) {
                $table->boolean('is_demo')->default(false)->after('nama');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sekolahs', function (Blueprint $table) {
            if (Schema::hasColumn('sekolahs', 'is_demo')) {
                $table->dropColumn('is_demo');
            }
        });
    }
};
