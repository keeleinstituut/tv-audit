<?php

namespace Tests\Feature;

use App\Enums\EventType;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class ExampleMessageCreators
{
    public static function forLogIn(): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => EventType::LogIn->value,
            'event_parameters' => null,
            'context_institution_id' => null,
            'acting_institution_user_id' => null,
            'context_department_id' => null,
        ];
    }

    public static function forLogOut(): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => EventType::LogOut->value,
            'event_parameters' => null,
            'context_institution_id' => null,
            'acting_institution_user_id' => null,
            'context_department_id' => null,
        ];
    }

    public static function forSelectInstitution(): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => EventType::SelectInstitution->value,
            'event_parameters' => null,
            'context_department_id' => null,
        ];
    }

    public static function forExportInstitutionUsers(): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => EventType::ExportInstitutionUsers->value,
            'event_parameters' => null,
        ];
    }

    public static function createFinishProjectExampleMessageBody(): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => EventType::FinishProject->value,
            'event_parameters' => ['project_id' => Str::uuid()->toString()],
        ];
    }

    public static function createRewindWorkflowExampleMessageBody(): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => EventType::RewindWorkflow->value,
            'event_parameters' => ['workflow_id' => Str::uuid()->toString()],
        ];
    }

    public static function forDispatchNotification(): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => EventType::DispatchNotification->value,
            'event_parameters' => ['todo' => null], // TODO!
        ];
    }

    public static function forDownloadProjectFile(): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => EventType::DownloadProjectFile->value,
            'event_parameters' => [
                'media_id' => Str::uuid()->toString(),
                'project_id' => Str::uuid()->toString(),
            ],
        ];
    }

    public static function forExportProjectsReport(): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => EventType::ExportProjectsReport->value,
            'event_parameters' => [
                'start_date' => null,
                'end_date' => null,
                'status' => null,
            ],
        ];
    }

    public static function forImportTranslationMemory(): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => EventType::ImportTranslationMemory->value,
            'event_parameters' => [
                'translation_memory_id' => Str::uuid()->toString(),
            ],
        ];
    }

    public static function forExportTranslationMemory(): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => EventType::ExportTranslationMemory->value,
            'event_parameters' => [
                'translation_memory_id' => Str::uuid()->toString(),
            ],
        ];
    }

    public static function forSearchLogs(): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => EventType::SearchLogs->value,
            'event_parameters' => [
                'start_datetime' => Date::now()->toISOString(),
                'end_datetime' => null,
                'event_type' => EventType::LogIn->value,
                'query_text' => 'test',
            ],
        ];
    }

    public static function forExportLogs(): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => EventType::ExportLogs->value,
            'event_parameters' => [
                'start_datetime' => null,
                'end_datetime' => Date::now()->toISOString(),
                'event_type' => null,
                'query_text' => null,
            ],
        ];
    }

    public static function forRejectAssignmentResult(): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => EventType::RejectAssignmentResult->value,
            'event_parameters' => [
                'assignment_id' => Str::uuid()->toString(),
            ],
        ];
    }

    public static function forApproveAssignmentResult(): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => EventType::ApproveAssignmentResult->value,
            'event_parameters' => [
                'assignment_id' => Str::uuid()->toString(),
            ],
        ];
    }

    public static function forCompleteAssignment(): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => EventType::ApproveAssignmentResult->value,
            'event_parameters' => [
                'assignment_id' => Str::uuid()->toString(),
            ],
        ];
    }

    public static function createBaseSuccessMessageBody(): array
    {
        return [
            'happened_at' => Date::now()->milliseconds(0)->toISOString(),
            'trace_id' => Str::random(),
            'failure_type' => null,
            'context_institution_id' => Str::uuid()->toString(),
            'acting_institution_user_id' => Str::uuid()->toString(),
            'context_department_id' => Str::uuid()->toString(),
            'acting_user_pic' => '48202129530',
        ];
    }
}
