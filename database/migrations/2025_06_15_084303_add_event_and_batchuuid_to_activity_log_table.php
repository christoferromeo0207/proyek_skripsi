<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::table(config('activitylog.table_name', 'activity_log'), function (Blueprint $table) {
            $table->uuid('batch_uuid')->nullable()->after('properties');
            $table->string('event')->nullable()->after('batch_uuid');
        });
    }

    public function down()
    {
        Schema::table(config('activitylog.table_name', 'activity_log'), function (Blueprint $table) {
            $table->dropColumn(['batch_uuid', 'event']);
        });
    }
};
