<?php

namespace App\Services;

readonly class ObjectSubsetValidationRules
{
    public static function buildUserRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.personal_identification_code" => 'sometimes',
            "$fieldNamePrefix.forename" => 'sometimes',
            "$fieldNamePrefix.surname" => 'sometimes',
        ];
    }

    public static function buildVolumeRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.unit_type" => 'sometimes',
            "$fieldNamePrefix.unit_quantity" => 'sometimes',
            "$fieldNamePrefix.unit_fee" => 'sometimes',

            "$fieldNamePrefix.job" => 'array',
            ...HistoricalIdentityValidationRules::buildCatJobRules("$fieldNamePrefix.job"),

            "$fieldNamePrefix.volume_analysis" => 'array',
            "$fieldNamePrefix.volume_analysis.total" => 'sometimes',
            "$fieldNamePrefix.volume_analysis.tm_101" => 'sometimes',
            "$fieldNamePrefix.volume_analysis.repetitions" => 'sometimes',
            "$fieldNamePrefix.volume_analysis.tm_100" => 'sometimes',
            "$fieldNamePrefix.volume_analysis.tm_95_99" => 'sometimes',
            "$fieldNamePrefix.volume_analysis.tm_85_94" => 'sometimes',
            "$fieldNamePrefix.volume_analysis.tm_75_84" => 'sometimes',
            "$fieldNamePrefix.volume_analysis.tm_50_74" => 'sometimes',
            "$fieldNamePrefix.volume_analysis.tm_0_49" => 'sometimes',
            "$fieldNamePrefix.volume_analysis.files_names" => 'sometimes',

            "$fieldNamePrefix.discount" => 'array',
            "$fieldNamePrefix.discount.discount_percentage_101" => 'sometimes',
            "$fieldNamePrefix.discount.discount_percentage_repetitions" => 'sometimes',
            "$fieldNamePrefix.discount.discount_percentage_100" => 'sometimes',
            "$fieldNamePrefix.discount.discount_percentage_95_99" => 'sometimes',
            "$fieldNamePrefix.discount.discount_percentage_85_94" => 'sometimes',
            "$fieldNamePrefix.discount.discount_percentage_75_84" => 'sometimes',
            "$fieldNamePrefix.discount.discount_percentage_50_74" => 'sometimes',
            "$fieldNamePrefix.discount.discount_percentage_0_49" => 'sometimes',
        ];
    }

    public static function buildAssignmentRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.id" => 'sometimes',
            "$fieldNamePrefix.ext_id" => 'sometimes',
            "$fieldNamePrefix.deadline_at" => 'sometimes',
            "$fieldNamePrefix.comments" => 'sometimes',
            "$fieldNamePrefix.assignee_comments" => 'sometimes',
            "$fieldNamePrefix.feature" => 'sometimes',

            "$fieldNamePrefix.assignee" => 'nullable',
            ...HistoricalIdentityValidationRules::buildVendorRules("$fieldNamePrefix.assignee"),

            "$fieldNamePrefix.candidates" => 'array',
            "$fieldNamePrefix.candidates.*" => 'array',
            ...HistoricalIdentityValidationRules::buildVendorRules("$fieldNamePrefix.candidates.*"),

            "$fieldNamePrefix.volumes" => 'array',
            "$fieldNamePrefix.volumes.*" => 'array',
            ...HistoricalIdentityValidationRules::buildVolumeRules("$fieldNamePrefix.volumes.*"),

            "$fieldNamePrefix.jobs" => 'array',
            "$fieldNamePrefix.jobs.*" => 'array',
            ...HistoricalIdentityValidationRules::buildCatJobRules("$fieldNamePrefix.jobs.*"),
        ];
    }

    public static function buildSubprojectRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.ext_id" => 'sometimes',
            "$fieldNamePrefix.deadline_at" => 'sometimes',
            "$fieldNamePrefix.price" => 'sometimes',
            "$fieldNamePrefix.features" => 'sometimes',
            "$fieldNamePrefix.mt_enabled" => 'sometimes',

            "$fieldNamePrefix.source_language_classifier_value" => ['sometimes', 'array'],
            ...HistoricalIdentityValidationRules::buildClassifierValueRules("$fieldNamePrefix.source_language_classifier_value"),

            "$fieldNamePrefix.destination_language_classifier_value" => ['sometimes', 'array'],
            ...HistoricalIdentityValidationRules::buildClassifierValueRules("$fieldNamePrefix.destination_language_classifier_value"),

            "$fieldNamePrefix.assignments" => ['sometimes', 'array'],
            "$fieldNamePrefix.assignments.*" => 'array',
            ...HistoricalIdentityValidationRules::buildAssignmentRules("$fieldNamePrefix.assignments.*"),

            "$fieldNamePrefix.source_files" => ['sometimes', 'array'],
            "$fieldNamePrefix.source_files.*" => 'array',
            ...HistoricalIdentityValidationRules::buildMediaRules("$fieldNamePrefix.source_files.*"),

            "$fieldNamePrefix.final_files" => ['sometimes', 'array'],
            "$fieldNamePrefix.final_files.*" => 'array',
            ...HistoricalIdentityValidationRules::buildMediaRules("$fieldNamePrefix.final_files.*"),

            "$fieldNamePrefix.cat_files" => ['sometimes', 'array'],
            "$fieldNamePrefix.cat_files.*" => 'array',
            ...HistoricalIdentityValidationRules::buildMediaRules("$fieldNamePrefix.cat_files.*"),

            "$fieldNamePrefix.cat_jobs" => ['sometimes', 'array'],
            "$fieldNamePrefix.cat_jobs.*" => 'array',
            ...HistoricalIdentityValidationRules::buildCatJobRules("$fieldNamePrefix.cat_jobs.*"),
        ];
    }

    public static function buildProjectRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.ext_id" => 'sometimes',
            "$fieldNamePrefix.reference_number" => 'sometimes',
            "$fieldNamePrefix.comments" => 'sometimes',
            "$fieldNamePrefix.workflow_template_id" => 'sometimes',
            "$fieldNamePrefix.workflow_instance_ref" => 'sometimes',
            "$fieldNamePrefix.price" => 'sometimes',
            "$fieldNamePrefix.deadline_at" => 'sometimes',
            "$fieldNamePrefix.event_start_at" => 'sometimes',
            "$fieldNamePrefix.status" => 'sometimes',

            "$fieldNamePrefix.source_files" => ['sometimes', 'array'],
            "$fieldNamePrefix.source_files.*" => 'array',
            ...HistoricalIdentityValidationRules::buildMediaRules("$fieldNamePrefix.source_files.*"),

            "$fieldNamePrefix.help_files" => ['sometimes', 'array'],
            "$fieldNamePrefix.help_files.*" => 'array',
            ...HistoricalIdentityValidationRules::buildMediaRules("$fieldNamePrefix.help_files.*"),

            "$fieldNamePrefix.final_files" => ['sometimes', 'array'],
            "$fieldNamePrefix.final_files.*" => 'array',
            ...HistoricalIdentityValidationRules::buildMediaRules("$fieldNamePrefix.final_files.*"),

            "$fieldNamePrefix.sub_projects" => ['sometimes', 'array'],
            "$fieldNamePrefix.sub_projects.*" => 'array',
            ...HistoricalIdentityValidationRules::buildSubProjectRules("$fieldNamePrefix.sub_projects.*"),

            "$fieldNamePrefix.translation_domain_classifier_value" => ['sometimes', 'array'],
            ...HistoricalIdentityValidationRules::buildClassifierValueRules("$fieldNamePrefix.translation_domain_classifier_value"),

            "$fieldNamePrefix.type_classifier_value" => ['sometimes', 'array'],
            ...HistoricalIdentityValidationRules::buildClassifierValueRules("$fieldNamePrefix.type_classifier_value"),

            "$fieldNamePrefix.client_institution_user" => ['sometimes', 'nullable', 'array'],
            ...HistoricalIdentityValidationRules::buildInstitutionUserRules("$fieldNamePrefix.client_institution_user"),

            "$fieldNamePrefix.manager_institution_user" => ['sometimes', 'nullable', 'array'],
            ...HistoricalIdentityValidationRules::buildInstitutionUserRules("$fieldNamePrefix.manager_institution_user"),
        ];
    }

    public static function buildInstitutionDiscountRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.discount_percentage_101" => 'sometimes',
            "$fieldNamePrefix.discount_percentage_repetitions" => 'sometimes',
            "$fieldNamePrefix.discount_percentage_100" => 'sometimes',
            "$fieldNamePrefix.discount_percentage_95_99" => 'sometimes',
            "$fieldNamePrefix.discount_percentage_85_94" => 'sometimes',
            "$fieldNamePrefix.discount_percentage_75_84" => 'sometimes',
            "$fieldNamePrefix.discount_percentage_50_74" => 'sometimes',
            "$fieldNamePrefix.discount_percentage_0_49" => 'sometimes',
        ];

    }

    public static function buildVendorRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.company_name" => 'sometimes',
            "$fieldNamePrefix.comment" => 'sometimes',

            "$fieldNamePrefix.discount_percentage_101" => 'sometimes',
            "$fieldNamePrefix.discount_percentage_repetitions" => 'sometimes',
            "$fieldNamePrefix.discount_percentage_100" => 'sometimes',
            "$fieldNamePrefix.discount_percentage_95_99" => 'sometimes',
            "$fieldNamePrefix.discount_percentage_85_94" => 'sometimes',
            "$fieldNamePrefix.discount_percentage_75_84" => 'sometimes',
            "$fieldNamePrefix.discount_percentage_50_74" => 'sometimes',
            "$fieldNamePrefix.discount_percentage_0_49" => 'sometimes',

            "$fieldNamePrefix.prices" => 'array',
            "$fieldNamePrefix.prices.*" => 'array',
            "$fieldNamePrefix.prices.*.id" => 'required',
            "$fieldNamePrefix.prices.*.character_fee" => 'sometimes',
            "$fieldNamePrefix.prices.*.word_fee" => 'sometimes',
            "$fieldNamePrefix.prices.*.page_fee" => 'sometimes',
            "$fieldNamePrefix.prices.*.minute_fee" => 'sometimes',
            "$fieldNamePrefix.prices.*.hour_fee" => 'sometimes',
            "$fieldNamePrefix.prices.*.minimal_fee" => 'sometimes',
            "$fieldNamePrefix.prices.*.skill" => 'array',
            "$fieldNamePrefix.prices.*.skill.id" => 'required',
            "$fieldNamePrefix.prices.*.skill.name" => 'sometimes',

            "$fieldNamePrefix.prices.*.source_language_classifier_value" => ['sometimes', 'array'],
            ...HistoricalIdentityValidationRules::buildClassifierValueRules("$fieldNamePrefix.prices.*.source_language_classifier_value"),

            "$fieldNamePrefix.prices.*.destination_language_classifier_value" => ['sometimes', 'array'],
            ...HistoricalIdentityValidationRules::buildClassifierValueRules("$fieldNamePrefix.prices.*.destination_language_classifier_value"),

        ];
    }

    public static function buildInstitutionRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.name" => 'sometimes',
            "$fieldNamePrefix.short_name" => 'sometimes',
            "$fieldNamePrefix.phone" => 'sometimes',
            "$fieldNamePrefix.email" => 'sometimes',
            "$fieldNamePrefix.logo_url" => 'sometimes',
            "$fieldNamePrefix.worktime_timezone" => 'sometimes',
            "$fieldNamePrefix.monday_worktime_start" => 'sometimes',
            "$fieldNamePrefix.monday_worktime_end" => 'sometimes',
            "$fieldNamePrefix.tuesday_worktime_start" => 'sometimes',
            "$fieldNamePrefix.tuesday_worktime_end" => 'sometimes',
            "$fieldNamePrefix.wednesday_worktime_start" => 'sometimes',
            "$fieldNamePrefix.wednesday_worktime_end" => 'sometimes',
            "$fieldNamePrefix.thursday_worktime_start" => 'sometimes',
            "$fieldNamePrefix.thursday_worktime_end" => 'sometimes',
            "$fieldNamePrefix.friday_worktime_start" => 'sometimes',
            "$fieldNamePrefix.friday_worktime_end" => 'sometimes',
            "$fieldNamePrefix.saturday_worktime_start" => 'sometimes',
            "$fieldNamePrefix.saturday_worktime_end" => 'sometimes',
            "$fieldNamePrefix.sunday_worktime_start" => 'sometimes',
            "$fieldNamePrefix.sunday_worktime_end" => 'sometimes',
        ];
    }

    public static function buildRoleRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.name" => 'sometimes',
            "$fieldNamePrefix.is_root" => 'sometimes',
            "$fieldNamePrefix.privileges" => ['sometimes', 'array'],
            "$fieldNamePrefix.privileges.*" => ['array'],
            "$fieldNamePrefix.privileges.*.key" => ['required'],
        ];
    }

    public static function buildInstitutionUserRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.email" => 'sometimes',
            "$fieldNamePrefix.phone" => 'sometimes',
            "$fieldNamePrefix.archived_at" => 'sometimes',
            "$fieldNamePrefix.deactivation_date" => 'sometimes',
            "$fieldNamePrefix.department_id" => 'sometimes',

            "$fieldNamePrefix.worktime_timezone" => 'sometimes',
            "$fieldNamePrefix.monday_worktime_start" => 'sometimes',
            "$fieldNamePrefix.monday_worktime_end" => 'sometimes',
            "$fieldNamePrefix.tuesday_worktime_start" => 'sometimes',
            "$fieldNamePrefix.tuesday_worktime_end" => 'sometimes',
            "$fieldNamePrefix.wednesday_worktime_start" => 'sometimes',
            "$fieldNamePrefix.wednesday_worktime_end" => 'sometimes',
            "$fieldNamePrefix.thursday_worktime_start" => 'sometimes',
            "$fieldNamePrefix.thursday_worktime_end" => 'sometimes',
            "$fieldNamePrefix.friday_worktime_start" => 'sometimes',
            "$fieldNamePrefix.friday_worktime_end" => 'sometimes',
            "$fieldNamePrefix.saturday_worktime_start" => 'sometimes',
            "$fieldNamePrefix.saturday_worktime_end" => 'sometimes',
            "$fieldNamePrefix.sunday_worktime_start" => 'sometimes',
            "$fieldNamePrefix.sunday_worktime_end" => 'sometimes',
        ];
    }

    public static function buildTranslationMemoryRules(string $fieldNamePrefix): array
    {
        return []; // TODO
    }
}
