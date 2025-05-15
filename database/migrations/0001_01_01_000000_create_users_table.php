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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable(false);
            $table->string('username', 100)->unique()->nullable(false);
            $table->string('email', 255)->unique()->nullable(false);
            $table->string('password', 255)->nullable(false);
            $table->string('jabatan', 100)->nullable()->default(null);
            $table->date('tgl_lahir')->nullable()->default(null);
            $table->date('tgl_masuk')->nullable()->default(null);
            $table->string('tempat_lahir', 100)->nullable()->default(null); 
            $table->string('no_telp')->nullable()->default(null); 
            $table->timestamps(); //created_at dan updated_at
            $table->primary('id');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
