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
        Schema::create('kelompok_tani_anggotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelompok_tani_id')->constrained('kelompok_tanis')->onDelete('cascade');
            $table->string('nama_anggota');
            $table->enum('jabatan', ['Ketua', 'Sekretaris', 'Bendahara', 'Anggota']);
            $table->string('no_hp')->nullable();
            $table->decimal('luas_lahan', 8, 2)->nullable();
            $table->foreignId('jenis_komoditas_id')->constrained('jenis_komoditas')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelompok_tani_anggotas');
    }
};