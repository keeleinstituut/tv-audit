<?php

namespace App\Services;

class HistoricalIdentityValidationRules
{
    public static function buildCatJobRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.id" => ['sometimes', 'required'],
            "$fieldNamePrefix.name" => ['sometimes', 'present'],
        ];
    }

    public static function buildVendorRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.id" => ['sometimes', 'required'],
            "$fieldNamePrefix.company_name" => ['sometimes', 'present'],

            "$fieldNamePrefix.institution_user" => ['sometimes', 'required', 'array'],
            static::buildInstitutionUserRules("$fieldNamePrefix.institution_user"),
        ];
    }

    public static function buildVolumeRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.id" => ['sometimes', 'required'],
            "$fieldNamePrefix.ext_id" => ['sometimes', 'present'],
        ];
    }

    public static function buildClassifierValueRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.id" => ['sometimes', 'required'],
            "$fieldNamePrefix.type" => ['sometimes', 'required'],
            "$fieldNamePrefix.value" => ['sometimes', 'required'],
            "$fieldNamePrefix.name" => ['sometimes', 'required'],
        ];
    }

    public static function buildAssignmentRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.id" => ['sometimes', 'required'],
            "$fieldNamePrefix.ext_id" => ['sometimes', 'present'],
        ];
    }

    public static function buildMediaRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.id" => ['sometimes', 'required'],
            "$fieldNamePrefix.name" => ['sometimes', 'present'],
            "$fieldNamePrefix.uuid" => ['sometimes', 'present'],
            "$fieldNamePrefix.file_name" => ['sometimes', 'present'],
        ];
    }

    public static function buildSubProjectRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.id" => ['sometimes', 'required'],
            "$fieldNamePrefix.ext_id" => ['sometimes', 'present'],

            "$fieldNamePrefix.source_language_classifier_value" => ['sometimes', 'array'],
            ...HistoricalIdentityValidationRules::buildClassifierValueRules("$fieldNamePrefix.source_language_classifier_value"),

            "$fieldNamePrefix.destination_language_classifier_value" => ['sometimes', 'array'],
            ...HistoricalIdentityValidationRules::buildClassifierValueRules("$fieldNamePrefix.destination_language_classifier_value"),
        ];
    }

    public static function buildInstitutionUserRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.id" => ['sometimes', 'required'],
            "$fieldNamePrefix.user" => ['sometimes', 'array'],
            ...HistoricalIdentityValidationRules::buildUserRules("$fieldNamePrefix.user"),
        ];
    }

    public static function buildUserRules(string $fieldNamePrefix): array
    {
        return [
            "$fieldNamePrefix.id" => ['sometimes', 'required'],
            "$fieldNamePrefix.personal_identification_code" => ['sometimes', 'required'],
            "$fieldNamePrefix.forename" => ['sometimes', 'required'],
            "$fieldNamePrefix.surname" => ['sometimes', 'required'],
        ];
    }
}
