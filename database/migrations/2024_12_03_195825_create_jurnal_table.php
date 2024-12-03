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
        Schema::create('jurnal', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->time('waktu');
            $table->string('nama_santri');
            $table->foreignId('kelas_id')->constrained('kelas');
            $table->text('temuan_perilaku');
            $table->enum('jenis_temuan', ['positif', 'negatif']);
            $table->string('media_path')->nullable(); // Untuk foto atau video
            $table->foreignId('pelapor_id')->constrained('users'); // Ustadz atau pelapor
            $table->timestamps();

            // Indexes untuk optimasi pencarian
            $table->index('tanggal');
            $table->index('nama_santri');
            $table->index('jenis_temuan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jurnal');
    }
};
