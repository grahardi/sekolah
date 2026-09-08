<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'custom_role_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('custom_role_id');
            });
        }

        Schema::dropIfExists('custom_role_permissions');
        Schema::dropIfExists('custom_roles');
    }

    public function down(): void
    {
        // Gak perlu restore - fitur ini dibatalkan sepenuhnya.
    }
};
