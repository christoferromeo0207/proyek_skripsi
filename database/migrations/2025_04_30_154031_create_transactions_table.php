<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate(); //mengarah ke id posts
            $table->string('nama_produk');
            $table->integer('jumlah');
            $table->string('merk');
            $table->decimal('harga_satuan', 12, 2);
            $table->decimal('total_harga', 12, 2);
            $table->string('tipe_pembayaran');
            $table->string('bukti_pembayaran')->nullable();
            $table->string('pic_rs');
            $table->boolean('approval_rs')->default('false');
            $table->string('pic_mitra');
            $table->boolean('approval_mitra')->default('false');
            $table->string('status')->default('proses');
            $table->timestamps();
            
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
