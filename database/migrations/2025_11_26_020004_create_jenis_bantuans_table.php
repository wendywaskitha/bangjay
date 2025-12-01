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
        Schema::create('jenis_bantuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_bantuan_id')->constrained('kategori_bantuans')->onDelete('cascade');
            $table->string('nama_bantuan');
            $table->integer('periode_tahun');
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_bantuans');
    }
};