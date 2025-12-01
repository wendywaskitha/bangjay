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
        Schema::create('sebaran_bantuan_jenis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sebaran_bantuan_id')->constrained('sebaran_bantuans')->onDelete('cascade');
            $table->foreignId('jenis_bantuan_id')->constrained('jenis_bantuans')->onDelete('cascade');
            $table->integer('volume')->nullable();
            $table->string('satuan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sebaran_bantuan_jenis');
    }
};