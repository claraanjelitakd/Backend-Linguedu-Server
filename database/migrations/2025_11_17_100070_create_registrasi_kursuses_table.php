<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registrasi_kursus', function (Blueprint $table) {
            $table->id();

            // pakai users, bukan admin/member
            $table->unsignedBigInteger('id_admin')->nullable();
            $table->unsignedBigInteger('id_member');
            $table->unsignedBigInteger('id_course');

            $table->date('tgl_trans');
            $table->string('metode_bayar');
            $table->integer('total_byr');
            $table->string('bukti_byr')->nullable();
            $table->integer('progress')->default(0);
            $table->string('level');
            $table->timestamps();

            // FK ke users
            $table->foreign('id_admin')
                ->references('id')->on('users')
                ->onDelete('set null');

            $table->foreign('id_member')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('id_course')
                ->references('id_course')->on('kursus')
                ->onDelete('cascade');
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrasi_kursuses');
    }
};
