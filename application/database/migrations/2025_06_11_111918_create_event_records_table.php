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
        Schema::create('event_records', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->timestampTz('happened_at', 3)->nullable();

            $table->text('actor_pic')->nullable();
            $table->text('actor_name')->nullable();
            $table->text('actor_session')->nullable();
            $table->text('actor_department_id')->nullable();
            $table->text('actor_institution_id')->nullable();
            $table->text('actor_institution_user_id')->nullable();

            $table->text('action')->nullable();
            $table->text('web_path')->nullable();


            $table->text('path')->nullable();
            $table->string('request_method')->nullable();
            $table->json('request_query')->nullable();
            $table->json('request_body')->nullable();

            $table->integer('response_status_code')->nullable();

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_records');
    }
};
