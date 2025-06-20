<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

   public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
        
            $table->morphs('fileable');
           
            $table->string('original_name');
            $table->string('filename');
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();

            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
