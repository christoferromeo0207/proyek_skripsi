<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
       
        Schema::rename('users', 'marketing');


        Schema::table('marketing', function(Blueprint $t) {
            // primary key
            $t->renameColumn('id', 'id_pegawai');
            $t->renameColumn('name', 'nama_pegawai');
            $t->renameColumn('username', 'username_pegawai');
            $t->renameColumn('email', 'email');
            $t->string('posisi_pegawai')->nullable();
            $t->string('no_telp')->nullable();
            $t->string('tempat_lahir')->nullable();
            $t->date('tanggal_lahir')->nullable();
            $t->date('tanggal_masuk')->nullable();
            $t->boolean('PIC')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('marketing', function(Blueprint $t) {
            // drop kolom baru
            $t->dropColumn([
                'posisi_pegawai',
                'no_telp',
                'tempat_lahir',
                'tanggal_lahir',
                'tanggal_masuk',
                'PIC',
            ]);
            $t->renameColumn('nama_pegawai', 'name');
            $t->renameColumn('username_pegawai','username');
            $t->renameColumn('id_pegawai','id');
        });


        Schema::rename('marketing', 'users');
    }
};
