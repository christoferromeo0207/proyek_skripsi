<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

   public function up()
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('parent_post_id');
            $table->unsignedBigInteger('child_post_id');
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->decimal('commission_pct', 5, 2)->default(0.00);
            $table->decimal('commission_amount', 15, 2)->default(0.00);
            $table->enum('status', ['Cairkan', 'Sudah Diambil'])->default('Cairkan'); 
            $table->timestamps();

            // Index & foreign key
            $table->index('parent_post_id');
            $table->index('child_post_id');
            $table->index('transaction_id');

            $table->foreign('parent_post_id')
                ->references('id')->on('posts')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('child_post_id')
                ->references('id')->on('posts')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('transaction_id')
                ->references('id')->on('transactions')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
