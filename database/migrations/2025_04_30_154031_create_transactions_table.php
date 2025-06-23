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
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('jenis_transaksi', ['barang', 'jasa'])->default('barang');
            $table->string('tipe_pembayaran');
            $table->string('bukti_pembayaran')->nullable();
            $table->string('pic_rs');
            $table->boolean('approval_rs')->default('false');
            $table->string('pic_mitra');
            $table->boolean('approval_mitra')->default('false');
            $table->string('status')->default('proses');
            $table->foreignId('master_barang_id')->nullable()->constrained('master_barangs')->nullOnDelete();
            $table->foreignId('master_jasa_id')->nullable()->constrained('master_jasas')->nullOnDelete();

            $table->timestamps();
            
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
