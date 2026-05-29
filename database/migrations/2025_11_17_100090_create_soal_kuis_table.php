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
        Schema::create('soal_kuis', function (Blueprint $table) {
            $table->id('id_soal_kuis');
            $table->unsignedBigInteger('id_kuis');
            $table->text('pertanyaan');
            $table->string('jawaban_bnr');
            $table->timestamps();

            $table->foreign('id_kuis')->references('id_kuis')->on('kuis');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soal_kuis');
    }
};
