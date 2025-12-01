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
        Schema::create('sebaran_bantuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_tani_id')->constrained('kelompok_tanis')->onDelete('cascade');
            $table->text('catatan')->nullable();
            $table->date('tanggal_penetapan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sebaran_bantuans');
    }
};