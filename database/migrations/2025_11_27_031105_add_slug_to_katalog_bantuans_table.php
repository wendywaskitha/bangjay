<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if the column already exists
        if (!Schema::hasColumn('katalog_bantuans', 'slug')) {
            Schema::table('katalog_bantuans', function (Blueprint $table) {
                $table->string('slug')->unique()->nullable();
            });
        }

        // Populate the slug field for existing records
        $katalogBantuans = DB::table('katalog_bantuans')->get();
        foreach ($katalogBantuans as $katalog) {
            // Skip if slug already exists for this record
            if (empty($katalog->slug)) {
                DB::table('katalog_bantuans')
                  ->where('id', $katalog->id)
                  ->update(['slug' => Str::slug($katalog->judul)]);
            }
        }

        // Make the slug field non-nullable after populating
        Schema::table('katalog_bantuans', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('katalog_bantuans', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
