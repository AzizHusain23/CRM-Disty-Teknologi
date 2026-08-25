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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('nomer_dok')->nullable();
            $table->string('nama');
            $table->string('institusi')->index();
            $table->string('email_primary')->unique();
            $table->string('phone')->nullable();
            $table->enum('status', ['Uncontacted', 'Queuing', 'Delivered', 'Replied', 'Rejected'])->default('Uncontacted');
            $table->timestamps();
        });

        $koneksi = DB::connection()->getPdo();
        // Menambahkan Full-Text Search index agar fitur Searchbar nama dan institusi lebih cepat
        $koneksi->exec("ALTER TABLE leads ADD FULLTEXT search_index(nama, institusi)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
