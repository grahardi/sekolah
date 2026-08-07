<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('exo_instances', 'db_host')) return; // sudah ada dari migration awal

        Schema::table('exo_instances', function (Blueprint $table) {
            $table->text('db_host')->nullable();
            $table->text('db_port')->nullable();
            $table->text('db_name')->nullable();
            $table->text('db_user')->nullable();
            $table->text('db_pass')->nullable();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('exo_instances', 'db_host')) return;

        Schema::table('exo_instances', function (Blueprint $table) {
            $table->dropColumn(['db_host', 'db_port', 'db_name', 'db_user', 'db_pass']);
        });
    }
};
