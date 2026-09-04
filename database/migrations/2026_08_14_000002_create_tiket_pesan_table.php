<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tiket_pesan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tiket_id')->constrained('tiket_dukungan')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // null kalau dari superadmin platform
            $table->boolean('dari_superadmin')->default(false);
            $table->text('pesan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tiket_pesan');
    }
};
