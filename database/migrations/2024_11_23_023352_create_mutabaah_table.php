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
        Schema::create('mutabaah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('users');
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->foreignId('ustadz_id')->constrained('users');
            $table->foreignId('jenis_storan_id')->constrained('jenis_setoran');
            $table->foreignId('kitab_surah_id')->constrained('kitab_surah');
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_selesai');
            $table->string('mulai_storan'); // Ayat/halaman awal
            $table->string('akhir_storan'); // Ayat/halaman akhir
            $table->decimal('nilai_bacaan', 5, 2);
            $table->decimal('nilai_hafalan', 5, 2);
            $table->text('kendala')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('media_path')->nullable(); // Path untuk foto/video
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
        Schema::dropIfExists('mutabaah');
    }
};
