<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sarpras_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sekolah_id')->constrained('sekolahs')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('icon')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('sarpras_categories')->nullOnDelete();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['sekolah_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sarpras_categories');
    }
};
