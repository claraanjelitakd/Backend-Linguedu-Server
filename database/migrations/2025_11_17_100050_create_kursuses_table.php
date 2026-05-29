<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('kursus', function (Blueprint $table) {
            $table->id('id_course');
            $table->foreignId('id_bahasa')->nullable()->constrained('bahasa')->onDelete('set null');
            $table->foreignId('id_paket')->nullable()->constrained('paket')->onDelete('set null');
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kursuses');
    }
};
