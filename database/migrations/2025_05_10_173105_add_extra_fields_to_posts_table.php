<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // kontak
            $table->string('phone', 20)->nullable()->after('body');
            $table->string('email')->nullable()->after('phone');
            $table->string('alamat')->nullable()->after('email');

            // bpjs & pembayaran
            $table->enum('keterangan_bpjs', ['yes','no'])
                  ->nullable()
                  ->after('alamat');
            $table->string('pembayaran', 100)
                  ->nullable()
                  ->after('keterangan_bpjs');

            // periode kerjasama
            $table->date('tanggal_awal')
                  ->nullable()
                  ->after('pembayaran');
            $table->date('tanggal_akhir')
                  ->nullable()
                  ->after('tanggal_awal');

            // file upload
            $table->string('file_path')
                  ->nullable()
                  ->after('tanggal_akhir');

            // foreign‐key ke users.id
            $table->unsignedBigInteger('PIC')
                  ->nullable()
                  ->after('file_path');
            $table->foreign('PIC')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // drop FK first
            $table->dropForeign(['PIC']);

            // then drop columns
            $table->dropColumn([
                'phone',
                'email',
                'alamat',
                'keterangan_bpjs',
                'pembayaran',
                'tanggal_awal',
                'tanggal_akhir',
                'file_path',
                'PIC',
            ]);
        });
    }
};
