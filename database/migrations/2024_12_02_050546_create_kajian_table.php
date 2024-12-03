<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kajian', function (Blueprint $table) {
            $table->id();
            $table->dateTime('tanggal_waktu');
            $table->foreignId('santri_id')->constrained('users');
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->foreignId('jenis_kajian_id')->constrained('jenis_kajian');
            $table->string('nama_ustadz');
            $table->string('judul_kitab');
            $table->string('media_path')->nullable(); // Untuk foto atau video
            $table->foreignId('pelapor_id')->constrained('users');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kajian');
    }
};
