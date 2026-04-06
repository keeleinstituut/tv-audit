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
        Schema::table('event_records', function (Blueprint $table) {
            $table->text('notification_type')->nullable();
            $table->jsonb('notification_params')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('event_records', function (Blueprint $table) {
            $table->dropColumn(['notification_type', 'notification_params']);
        });
    }
};
