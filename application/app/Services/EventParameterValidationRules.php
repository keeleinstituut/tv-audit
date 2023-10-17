<?php

namespace App\Services;

use App\Enums\ObjectType;
use Illuminate\Validation\Rule;

class EventParameterValidationRules
{
    public static function buildObjectIdentityReferenceRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.object_type" => ['required', Rule::enum(ObjectType::class)],
            "$fieldNamePrefix.object_id" => 'required',
        ];
    }

    public static function buildModifyObjectParametersRules(?ObjectType $objectType, string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.object_type" => ['required', Rule::enum(ObjectType::class)],
            "$fieldNamePrefix.object_id" => 'required',

            "$fieldNamePrefix.pre_modification_subset" => ['required', 'array'],
            ...static::buildModifyObjectSpecificRules($objectType, "$fieldNamePrefix.pre_modification_subset"),

            "$fieldNamePrefix.post_modification_subset" => ['required', 'array'],
            ...static::buildModifyObjectSpecificRules($objectType, "$fieldNamePrefix.post_modification_subset"),
        ];
    }

    public static function buildNoParametersRules(): array
    {
        return []; // event type expects no parameters
    }

    public static function buildAssignmentReferenceRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.assignment_id" => 'required',
        ];
    }

    public static function buildProjectReferenceRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.project_id" => 'required',
        ];
    }

    public static function buildWorkflowReferenceRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.workflow_id" => 'required',
        ];
    }

    public static function buildWorkflowNotificationDescriptionRules(string $fieldNamePrefix): array
    {
        return [
            // TODO
        ];
    }

    public static function buildProjectFileReferenceRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.media_id" => 'required',
            "$fieldNamePrefix.project_id" => 'required',
        ];
    }

    public static function buildProjectExportParametersRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.start_date" => ['present', 'nullable', 'date', 'before_or_equal:end_date'],
            "$fieldNamePrefix.end_date" => ['present', 'nullable', 'date', 'after_or_equal:start_date'],
            "$fieldNamePrefix.status" => ['present', 'nullable', 'string'],
        ];
    }

    public static function buildTranslationMemoryReferenceRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.translation_memory_id" => 'required',
        ];
    }

    public static function buildAuditLogSearchParametersRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.start_datetime" => ['present', 'nullable', 'date'],
            "$fieldNamePrefix.end_datetime" => ['present', 'nullable', 'date'],
            "$fieldNamePrefix.event_type" => ['present', 'nullable', 'string'],
            "$fieldNamePrefix.query_text" => ['present', 'nullable', 'string'],
        ];
    }

    private static function buildModifyObjectSpecificRules(ObjectType $objectType, string $fieldNamePrefix): array
    {
        return match ($objectType) {
            ObjectType::User => ObjectSubsetValidationRules::buildUserRules($fieldNamePrefix),
            ObjectType::InstitutionUser => ObjectSubsetValidationRules::buildInstitutionUserRules($fieldNamePrefix),
            ObjectType::Role => ObjectSubsetValidationRules::buildRoleRules($fieldNamePrefix),
            ObjectType::Institution => ObjectSubsetValidationRules::buildInstitutionRules($fieldNamePrefix),
            ObjectType::Vendor => ObjectSubsetValidationRules::buildVendorRules($fieldNamePrefix),
            ObjectType::InstitutionDiscount => ObjectSubsetValidationRules::buildInstitutionDiscountRules($fieldNamePrefix),
            ObjectType::Project => ObjectSubsetValidationRules::buildProjectRules($fieldNamePrefix),
            ObjectType::Subproject => ObjectSubsetValidationRules::buildSubprojectRules($fieldNamePrefix),
            ObjectType::Assignment => ObjectSubsetValidationRules::buildAssignmentRules($fieldNamePrefix),
            ObjectType::Volume => ObjectSubsetValidationRules::buildVolumeRules($fieldNamePrefix),
            ObjectType::TranslationMemory => ObjectSubsetValidationRules::buildTranslationMemoryRules($fieldNamePrefix)
        };
    }
}
