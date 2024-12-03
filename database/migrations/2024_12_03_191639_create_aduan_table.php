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
        Schema::create('aduan', function (Blueprint $table) {
            $table->id();
            $table->dateTime('tanggal_waktu');
            $table->foreignId('jenis_aduan_id')->constrained('jenis_aduan');
            $table->string('alasan');
            $table->text('keterangan');
            $table->boolean('dalam_tekanan')->default(false);
            $table->boolean('kesadaran_penuh')->default(true);
            $table->string('media_path')->nullable(); // Untuk foto atau video
            $table->foreignId('pelapor_id')->constrained('users');
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
        Schema::dropIfExists('aduan');
    }
};
