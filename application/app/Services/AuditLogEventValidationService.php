<?php

namespace App\Services;

use App\Enums\EventType;
use App\Enums\FailureType;
use App\Enums\ObjectType;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuditLogEventValidationService
{
    public function makeValidator(array $messageBody): \Illuminate\Contracts\Validation\Validator
    {
        $eventType = EventType::tryFrom(Arr::get($messageBody, 'event_type'));
        $objectType = ObjectType::tryFrom(Arr::get($messageBody, 'event_parameters.object_type'));

        return Validator::make($messageBody, $this->rules($eventType, $objectType));
    }

    public function rules(?EventType $eventType, ?ObjectType $objectType): array
    {
        return [
            'happened_at' => ['required', 'date'],
            'trace_id' => ['present', 'nullable', 'string'],
            'event_type' => ['required', Rule::enum(EventType::class)],
            'failure_type' => ['present', 'nullable', Rule::enum(FailureType::class)],
            'context_institution_id' => ['present', 'nullable', 'uuid'],
            'acting_institution_user_id' => ['present', 'nullable', 'uuid'],
            'context_department_id' => ['present', 'nullable', 'uuid'],
            'acting_user_pic' => ['present', 'nullable', 'string'],
            'event_parameters' => ['present', 'nullable', 'array'],
            ...static::buildSpecificEventParameterRules('event_parameters', $eventType, $objectType),
        ];
    }

    public static function buildSpecificEventParameterRules(string $fieldNamePrefix, ?EventType $eventType, ?ObjectType $objectType): array
    {
        return match ($eventType) {
            EventType::FinishProject => EventParameterValidationRules::buildProjectReferenceRules($fieldNamePrefix),
            EventType::RewindWorkflow => EventParameterValidationRules::buildWorkflowReferenceRules($fieldNamePrefix),
            EventType::DispatchNotification => EventParameterValidationRules::buildWorkflowNotificationDescriptionRules($fieldNamePrefix),
            EventType::DownloadProjectFile => EventParameterValidationRules::buildProjectFileReferenceRules($fieldNamePrefix),
            EventType::ExportProjectsReport => EventParameterValidationRules::buildProjectExportParametersRules($fieldNamePrefix),
            EventType::ModifyObject => EventParameterValidationRules::buildModifyObjectParametersRules($objectType, $fieldNamePrefix),
            EventType::RemoveObject,
            EventType::CreateObject => EventParameterValidationRules::buildObjectIdentityReferenceRules($fieldNamePrefix),
            EventType::ImportTranslationMemory,
            EventType::ExportTranslationMemory => EventParameterValidationRules::buildTranslationMemoryReferenceRules($fieldNamePrefix),
            EventType::SearchLogs,
            EventType::ExportLogs => EventParameterValidationRules::buildAuditLogSearchParametersRules($fieldNamePrefix),
            EventType::RejectAssignmentResult,
            EventType::ApproveAssignmentResult,
            EventType::CompleteAssignment => EventParameterValidationRules::buildAssignmentReferenceRules($fieldNamePrefix),
            EventType::LogOut,
            EventType::ExportInstitutionUsers,
            EventType::SelectInstitution,
            EventType::LogIn,
            null => EventParameterValidationRules::buildNoParametersRules(), // event type expects no parameters
        };
    }
}
