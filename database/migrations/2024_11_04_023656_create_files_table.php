<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notulen_id')->constrained('notulen')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_type', 50);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('files');
    }
}
