<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profil_bang_jais', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->longText('konten_profil');
            $table->string('foto_profil')->nullable();
            $table->string('foto_banner')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_bang_jais');
    }
};