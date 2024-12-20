<?php

namespace Tests\Feature;

use AuditLogClient\Enums\AuditLogEventObjectType;
use AuditLogClient\Enums\AuditLogEventType;
use AuditLogClient\Services\AuditLogMessageBuilder;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class AuditLogEventListenerSuccessTest extends AuditLogEventListenerBaseTestCase
{
    public function test_finish_project_event()
    {
        $message = static::createRandomizedMessageBuilder()->toApproveProjectEvent(
            Str::uuid()->toString(),
            Str::random()
        );

        $this->assertEventIsRecorded($message);
    }

    public function test_rewind_workflow_event()
    {
        $message = static::createRandomizedMessageBuilder()->toRewindWorkflowEvent(
            Str::uuid()->toString(),
            fake()->colorName()
        );

        $this->assertEventIsRecorded($message);
    }

    public function test_download_project_file_event()
    {
        $message = static::createRandomizedMessageBuilder()->toDownloadProjectFileEvent(
            Str::uuid()->toString(),
            Str::uuid()->toString(),
            fake()->regexify('[A-Z]{3}-\d{4}-\d{2}-[STK]-\d{3}'),
            fake()->word().'.'.fake()->fileExtension()
        );

        $this->assertEventIsRecorded($message);
    }

    public function test_export_projects_report_event()
    {
        $message = static::createRandomizedMessageBuilder()->toExportProjectsReportEvent(null, null, 'NEW');
        $this->assertEventIsRecorded($message);
    }

    public function test_import_translation_memory_event()
    {
        $message = static::createRandomizedMessageBuilder()->toImportTranslationMemoryEvent(
            Str::uuid()->toString(),
            fake()->colorName()
        );

        $this->assertEventIsRecorded($message);
    }

    public function test_export_translation_memory_event()
    {
        $message = static::createRandomizedMessageBuilder()->toExportTranslationMemoryEvent(
            Str::uuid()->toString(),
            fake()->colorName()
        );

        $this->assertEventIsRecorded($message);
    }

    public function test_search_logs_event()
    {
        $message = static::createRandomizedMessageBuilder()->toSearchLogsEvent(
            null,
            null,
            AuditLogEventType::LogIn->value,
            null,
            fake()->firstName()
        );

        $this->assertEventIsRecorded($message);
    }

    public function test_export_logs_event()
    {
        $message = static::createRandomizedMessageBuilder()->toExportLogsEvent(
            Date::yesterday()->format('Y-m-d'),
            Date::tomorrow()->format('Y-m-d'),
            null,
            Str::uuid()->toString(),
            null
        );

        $this->assertEventIsRecorded($message);
    }

    public function test_reject_assignment_result_event()
    {
        $message = static::createRandomizedMessageBuilder()->toRejectAssignmentResultEvent(
            Str::uuid()->toString(),
            Str::random()
        );

        $this->assertEventIsRecorded($message);
    }

    public function test_approve_assignment_result_event()
    {
        $message = static::createRandomizedMessageBuilder()->toApproveAssignmentResultEvent(
            Str::uuid()->toString(),
            Str::random()
        );

        $this->assertEventIsRecorded($message);
    }

    public function test_complete_assignment_event()
    {
        $message = static::createRandomizedMessageBuilder()->toCompleteAssignmentEvent(
            Str::uuid()->toString(),
            Str::random()
        );

        $this->assertEventIsRecorded($message);
    }

    public function test_log_out_event()
    {
        $message = static::createRandomizedMessageBuilder()->toLogOutEvent();
        $this->assertEventIsRecorded($message);
    }

    public function test_log_in_event()
    {
        $message = static::createRandomizedMessageBuilder()->toLogInEvent();
        $this->assertEventIsRecorded($message);
    }

    public function test_export_institution_users_event()
    {
        $message = static::createRandomizedMessageBuilder()->toExportInstitutionusers();
        $this->assertEventIsRecorded($message);
    }

    public function test_select_institution_event()
    {
        $message = static::createRandomizedMessageBuilder()->toSelectInstitutionEvent();
        $this->assertEventIsRecorded($message);
    }

    /** @dataProvider provideAllObjectTypes */
    public function test_remove_object_with_all_object_types(AuditLogEventObjectType $objectType)
    {
        $message = static::createRandomizedMessageBuilder()->toRemoveObjectEvent(
            $objectType,
            ObjectIdentityCreators::buildObjectFromType($objectType)
        );

        $this->assertEventIsRecorded($message);
    }

    /** @dataProvider provideAllObjectTypes */
    public function test_create_object_with_all_object_types(AuditLogEventObjectType $objectType)
    {
        $message = static::createRandomizedMessageBuilder()->toCreateObjectEvent(
            $objectType,
            ObjectDataCreators::buildObjectFromType($objectType),
            ObjectIdentityCreators::buildObjectFromType($objectType)
        );

        $this->assertEventIsRecorded($message);
    }

    /** @dataProvider provideAllObjectTypes */
    public function test_modify_object_with_all_object_types(AuditLogEventObjectType $objectType)
    {
        $message = static::createRandomizedMessageBuilder()->toModifyObjectEvent(
            $objectType,
            ObjectIdentityCreators::buildObjectFromType($objectType),
            ObjectDataCreators::buildObjectFromType($objectType),
            ObjectDataCreators::buildObjectFromType($objectType)
        );

        $this->assertEventIsRecorded($message);
    }

    /** @return array<array{ AuditLogEventObjectType }> */
    public static function provideAllObjectTypes(): array
    {
        return collect(AuditLogEventObjectType::cases())
            ->mapWithKeys(fn (AuditLogEventObjectType $objectType) => [$objectType->value => [$objectType]])
            ->all();
    }

    /**
     * @todo
     */
    public function test_dispatch_notification_event()
    {
        // TODO: Implement once data structure for notifications is determined
        $this->markTestIncomplete('Test not implemented as notification data structure is not yet known.');
        //        $message = static::createRandomizedMessageBuilder()->toDispatchNotificationEvent();
        //        $this->assertEventIsRecorded($message);
    }

    /** @return array<array{ AuditLogEventType, ?array }> */
    public static function provideEventTypesAndRandomEventParameters(): array
    {
        $eventParametersExamples = [
            null,
            [],
            ['randomInput' => Str::uuid()->toString()],
            ['randomArray' => [null, 'abc', 1]],
        ];

        return collect(AuditLogEventType::cases())
            ->crossJoin($eventParametersExamples)
            ->mapWithKeys(function (array $eventTypeAndEventParameters) {
                [$eventType, $eventParameters] = $eventTypeAndEventParameters;
                $eventParametersJSON = json_encode($eventParameters);

                return ["$eventType->value ($eventParametersJSON)" => [$eventType, $eventParameters]];
            })
            ->all();
    }

    /** @dataProvider provideEventTypesAndRandomEventParameters */
    public function test_events_with_unprocessable_entity_failure(AuditLogEventType $eventType, ?array $eventParameters)
    {
        $message = static::createRandomizedMessageBuilder()->toEventWithUnprocessableEntityFailure($eventType, $eventParameters);

        $this->assertEventIsRecorded($message);
    }

    private static function createRandomizedMessageBuilder(): AuditLogMessageBuilder
    {
        return AuditLogMessageBuilder::make(
            traceId: Str::random(),
            actingUserPic: '48202129530',
            actingUserForename: 'Mari',
            actingUserSurname: 'Maasikas',
            contextInstitutionId: Str::uuid()->toString(),
            actingInstitutionUserId: Str::uuid()->toString(),
            contextDepartmentId: Str::uuid()->toString(),
        );
    }
}
