<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotulenTable extends Migration
{
    public function up()
    {
        Schema::create('notulen', function (Blueprint $table) {
            $table->id(); 
            $table->dateTime('tanggal'); 
            $table->string('nama'); 
            $table->string('unit'); 
            $table->string('jabatan');
            $table->string('no_hp'); 
            $table->string('pertemuan'); 
            $table->string('jenis'); 
            // $table->text('hasil')->default('Belum ada hasil rekapan!'); 
            $table->string('status'); 
            $table->timestamps(); 
        });
    }

    public function down()
    {
        Schema::dropIfExists('notulen');
    }
}
