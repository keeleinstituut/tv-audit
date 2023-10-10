<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->timestampTz('happened_at');
            $table->string('event_type');

            $table->uuid('trace_id')->nullable();
            $table->uuid('institution_id')->nullable();
            $table->uuid('acting_institution_user_id')->nullable();
            $table->string('target_object_type')->nullable();
            $table->jsonb('event_parameters')->nullable();
            $table->jsonb('target_object_before_event')->nullable();
            $table->jsonb('target_object_after_event')->nullable();
            $table->string('failure_type')->nullable()->comment('If not null, then the attempted action failed. The reason of failure is described by this column.');
        });

        $eventTypes = self::getEventTypesAsSqlSet();
        DB::statement(
            'ALTER TABLE events '.
            'ADD CONSTRAINT event_type_check '.
            "CHECK (event_type IN $eventTypes)"
        );

        $targetObjectTypes = self::getTargetObjectTypesAsSqlSet();
        DB::statement(
            'ALTER TABLE events '.
            'ADD CONSTRAINT target_object_type_check '.
            "CHECK (target_object_type IN $targetObjectTypes)"
        );

        $failureTypes = self::getFailureTypesAsSqlSet();
        DB::statement(
            'ALTER TABLE events '.
            'ADD CONSTRAINT failure_type_check '.
            "CHECK (failure_type IN $failureTypes)"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }

    public static function getEventTypesAsSqlSet(): string
    {
        $eventTypes = [
            'LOG_IN',
            'LOG_OUT',
            'SELECT_INSTITUTION',

            'CREATE_OBJECT',
            'MODIFY_OBJECT',
            'REMOVE_OBJECT',

            'COMPLETE_ASSIGNMENT', // TODO: Look into whether could be replaced by EDIT_OBJECT:ASSIGNMENT
            'FINISH_PROJECT', // TODO: Look into whether could be replaced by EDIT_OBJECT:PROJECT
            'APPROVE_ASSIGNMENT_RESULT', // TODO: Look into whether could be replaced by EDIT_OBJECT:PROJECT
            'REJECT_ASSIGNMENT_RESULT', // TODO: Look into whether could be replaced by EDIT_OBJECT:PROJECT
            'REWIND_WORKFLOW',

            'DISPATCH_NOTIFICATION',
            'DOWNLOAD_PROJECT_FILE',
            'EXPORT_INSTITUTION_USERS',
            'EXPORT_PROJECTS_REPORT',
            'EXPORT_TRANSLATION_MEMORY',
            'IMPORT_TRANSLATION_MEMORY',
            'SEARCH_LOGS',
            'EXPORT_LOGS',
        ];

        $actionTypesSQL = collect($eventTypes)
            ->map(fn (string $type) => "'$type'")
            ->join(',');

        return "($actionTypesSQL)";
    }

    public static function getTargetObjectTypesAsSqlSet(): string
    {
        $targetObjectTypes = [
            'USER',
            'INSTITUTION_USER',
            'ROLE',
            'INSTITUTION',

            'VENDOR',
            'INSTITUTION_DISCOUNT',
            'PROJECT',
            'SUBPROJECT',
            'ASSIGNMENT',
            'VOLUME', // TODO: Subsumed by ASSIGNMENT?

            'TRANSLATION_MEMORY',
        ];

        $targetObjectTypesSQL = collect($targetObjectTypes)
            ->map(fn (string $type) => "'$type'")
            ->join(',');

        return "($targetObjectTypesSQL)";
    }

    public static function getFailureTypesAsSqlSet(): string
    {
        $failureTypes = [
            'UNPROCESSABLE_ENTITY',
            'UNAUTHORIZED',
            'FORBIDDEN',
            'NOT_FOUND',
            'SERVER_ERROR',
        ];

        $failureTypesSQL = collect($failureTypes)
            ->map(fn (string $type) => "'$type'")
            ->join(',');

        return "($failureTypesSQL)";
    }
};
