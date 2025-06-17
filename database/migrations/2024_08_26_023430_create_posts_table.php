<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('pic_mitra');

            $table->foreignId('category_id')
                  ->constrained('categories', 'id')
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();

            $table->string('slug')->unique();
            $table->text('body')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('alamat')->nullable();

            $table->enum('keterangan_bpjs', ['yes', 'no'])
                  ->nullable();

            $table->string('pembayaran', 100)->nullable();
            $table->date('tanggal_awal')->nullable();
            $table->date('tanggal_akhir')->nullable();
            $table->string('file_path')->nullable();

            $table->foreignId('PIC')
                  ->nullable()
                  ->constrained('users', 'id')
                  ->cascadeOnUpdate()
                  ->nullOnDelete();

            // untuk anak perusahaan (jika ada)
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('posts')
                ->onDelete('set null');
            // Timestamps
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
