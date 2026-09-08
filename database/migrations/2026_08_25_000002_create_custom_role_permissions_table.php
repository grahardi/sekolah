<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custom_role_id')->constrained('custom_roles')->cascadeOnDelete();
            $table->string('modul_key', 50);
            $table->boolean('boleh_akses')->default(false);
            $table->boolean('read_only')->default(true);
            $table->timestamps();

            $table->unique(['custom_role_id', 'modul_key']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('custom_role_id')->nullable()->after('role')->constrained('custom_roles')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('custom_role_id');
        });
        Schema::dropIfExists('custom_role_permissions');
    }
};
