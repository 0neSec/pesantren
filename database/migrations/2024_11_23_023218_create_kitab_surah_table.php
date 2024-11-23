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
        Schema::create('kitab_surah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_setoran_id')->constrained('jenis_setoran'); // Fixed table name here
            $table->string('nama'); // Nama surah/hadits/matan/kitab
            $table->text('deskripsi')->nullable();
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
        Schema::dropIfExists('kitab_surah');
    }
};
