<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestampsTz();
            $table->softDeletesTz();

            $table->string('trace_id')->nullable();
            $table->timestampTz('happened_at');
            $table->string('event_type');
            $table->jsonb('event_parameters')->nullable();
            $table->string('failure_type')->nullable()->comment('If not null, then the attempted action failed. The reason of failure is described by this column.');

            $table->uuid('context_institution_id')->nullable();
            $table->uuid('context_department_id')->nullable();

            $table->uuid('acting_institution_user_id')->nullable();
            $table->string('acting_user_pic')->nullable();
            $table->string('acting_user_forename')->nullable();
            $table->string('acting_user_surname')->nullable();
        });

        DB::statement(
            /** @lang PostgreSQL */
            "ALTER TABLE event_records
            ADD CONSTRAINT event_type_check
            CHECK (event_type IN (
                'LOG_IN',
                'LOG_OUT',
                'SELECT_INSTITUTION',
                'CREATE_OBJECT',
                'MODIFY_OBJECT',
                'REMOVE_OBJECT',
                'COMPLETE_ASSIGNMENT',
                'FINISH_PROJECT',
                'APPROVE_ASSIGNMENT_RESULT',
                'REJECT_ASSIGNMENT_RESULT',
                'REWIND_WORKFLOW',
                'DISPATCH_NOTIFICATION',
                'DOWNLOAD_PROJECT_FILE',
                'EXPORT_INSTITUTION_USERS',
                'EXPORT_PROJECTS_REPORT',
                'EXPORT_TRANSLATION_MEMORY',
                'IMPORT_TRANSLATION_MEMORY',
                'SEARCH_LOGS',
                'EXPORT_LOGS'
            ))"
        );

        DB::statement(
            /** @lang PostgreSQL */
            "ALTER TABLE event_records
            ADD CONSTRAINT failure_type_check
            CHECK (failure_type IN (
                'UNPROCESSABLE_ENTITY',
                'FORBIDDEN'
            ))"
        );

        DB::statement(
            /** @lang PostgreSQL */
            'CREATE OR REPLACE FUNCTION count_jsonb_object_keys(json_value JSONB)
            RETURNS BIGINT
            LANGUAGE sql IMMUTABLE
            AS $$
                SELECT count(*) FROM (SELECT jsonb_object_keys(json_value)) object_keys
            $$;'
        );

        DB::statement(
            /** @lang PostgreSQL */
            "CREATE OR REPLACE FUNCTION is_object_identity_subset_valid(object_type TEXT, object_identity_subset JSONB)
            RETURNS BOOLEAN
            LANGUAGE sql IMMUTABLE
            AS $$
                SELECT CASE
                    WHEN object_type = 'INSTITUTION_USER' THEN (
                        object_identity_subset->'id' IS NOT NULL
                        AND object_identity_subset->'user'->'id' IS NOT NULL
                        AND object_identity_subset->'user'->'personal_identification_code' IS NOT NULL
                        AND object_identity_subset->'user'->'forename' IS NOT NULL
                        AND object_identity_subset->'user'->'surname' IS NOT NULL
                    )
                    WHEN object_type IN ('ROLE', 'INSTITUTION', 'TRANSLATION_MEMORY') THEN (
                        object_identity_subset->'id' IS NOT NULL
                        AND object_identity_subset->'name' IS NOT NULL
                    )
                    WHEN object_type = 'VENDOR' THEN (
                        object_identity_subset->'id' IS NOT NULL
                        AND object_identity_subset->'institution_user'->'id' IS NOT NULL
                        AND object_identity_subset->'institution_user'->'user'->'id' IS NOT NULL
                        AND object_identity_subset->'institution_user'->'user'->'personal_identification_code' IS NOT NULL
                        AND object_identity_subset->'institution_user'->'user'->'forename' IS NOT NULL
                        AND object_identity_subset->'institution_user'->'user'->'surname' IS NOT NULL
                    )
                    WHEN object_type = 'INSTITUTION_DISCOUNT' THEN (object_identity_subset IS NULL)
                    WHEN object_type IN ('PROJECT', 'SUBPROJECT', 'ASSIGNMENT') THEN (
                        object_identity_subset->'id' IS NOT NULL
                        AND object_identity_subset->'ext_id' IS NOT NULL
                    )
                    ELSE FALSE
                END;
            $$;"
        );

        $objectTypeSetSql = self::getObjectTypesSqlSet();
        DB::statement(
            /** @lang PostgreSQL */
            "ALTER TABLE event_records
            ADD CONSTRAINT event_parameters_check
            CHECK (
                failure_type IS NOT NULL
                OR CASE
                    WHEN event_type = 'FINISH_PROJECT' THEN (
                        event_parameters->'project_id' IS NOT NULL
                        AND event_parameters->'project_ext_id' IS NOT NULL
                        AND count_jsonb_object_keys(event_parameters) = 2
                    )
                    WHEN event_type = 'REWIND_WORKFLOW' THEN (
                        event_parameters->'workflow_id' IS NOT NULL
                        AND event_parameters->'workflow_name' IS NOT NULL
                        AND count_jsonb_object_keys(event_parameters) = 2
                    )
                    WHEN event_type = 'DISPATCH_NOTIFICATION' THEN (
                        count_jsonb_object_keys(event_parameters) > 0  -- TODO!
                    )
                    WHEN event_type = 'DOWNLOAD_PROJECT_FILE' THEN (
                        event_parameters->'media_id' IS NOT NULL
                        AND event_parameters->'project_id' IS NOT NULL
                        AND event_parameters->'project_ext_id' IS NOT NULL
                        AND event_parameters->'file_name' IS NOT NULL
                        AND count_jsonb_object_keys(event_parameters) = 4
                    )
                    WHEN event_type = 'EXPORT_PROJECTS_REPORT' THEN (
                        (event_parameters ??& array['query_start_date', 'query_end_date', 'query_status'])
                        AND count_jsonb_object_keys(event_parameters) = 3
                    )
                    WHEN event_type = 'MODIFY_OBJECT' THEN (
                        event_parameters->>'object_type' IN $objectTypeSetSql
                        AND event_parameters->'pre_modification_subset' IS NOT NULL
                        AND event_parameters->'post_modification_subset' IS NOT NULL
                        AND count_jsonb_object_keys(event_parameters) = 4
                        AND is_object_identity_subset_valid(event_parameters->>'object_type', event_parameters->'object_identity_subset')
                    )
                    WHEN event_type = 'REMOVE_OBJECT' THEN (
                        event_parameters->>'object_type' IN $objectTypeSetSql
                        AND count_jsonb_object_keys(event_parameters) = 2
                        AND is_object_identity_subset_valid(event_parameters->>'object_type', event_parameters->'object_identity_subset')
                    )
                    WHEN event_type = 'CREATE_OBJECT' THEN (
                        event_parameters->>'object_type' IN $objectTypeSetSql
                        AND event_parameters->'object_data' IS NOT NULL
                        AND count_jsonb_object_keys(event_parameters) = 2
                    )
                    WHEN event_type IN ('IMPORT_TRANSLATION_MEMORY', 'EXPORT_TRANSLATION_MEMORY') THEN (
                        event_parameters->'translation_memory_id' IS NOT NULL
                        AND event_parameters->'translation_memory_name' IS NOT NULL
                        AND count_jsonb_object_keys(event_parameters) = 2
                    )
                    WHEN event_type IN ('SEARCH_LOGS', 'EXPORT_LOGS') THEN (
                        (event_parameters ??& array['query_start_datetime', 'query_end_datetime', 'query_event_type', 'query_text', 'query_department_id'])
                        AND count_jsonb_object_keys(event_parameters) = 5
                    )
                    WHEN event_type IN ('APPROVE_ASSIGNMENT_RESULT', 'REJECT_ASSIGNMENT_RESULT', 'COMPLETE_ASSIGNMENT') THEN (
                        event_parameters->'assignment_id' IS NOT NULL
                        AND event_parameters->'assignment_ext_id' IS NOT NULL
                        AND count_jsonb_object_keys(event_parameters) = 2
                    )
                    WHEN event_type IN ('LOG_IN', 'LOG_OUT', 'SELECT_INSTITUTION', 'EXPORT_INSTITUTION_USERS') THEN event_parameters IS NULL
                ELSE FALSE
                END
            )"
        );
    }

    private static function getObjectTypesSqlSet(): string
    {
        $targetObjectTypes = [
            'INSTITUTION_USER',
            'ROLE',
            'INSTITUTION',
            'VENDOR',
            'INSTITUTION_DISCOUNT',
            'PROJECT',
            'SUBPROJECT',
            'ASSIGNMENT',
            'TRANSLATION_MEMORY',
        ];

        $targetObjectTypesSQL = collect($targetObjectTypes)
            ->map(fn (string $type) => "'$type'")
            ->join(',');

        return "($targetObjectTypesSQL)";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_records');
        DB::statement(
            /** @lang PostgreSQL */
            'DROP FUNCTION count_jsonb_object_keys(jsonb);'
        );
    }
};
