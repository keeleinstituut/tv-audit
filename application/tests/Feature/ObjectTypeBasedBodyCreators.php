<?php

namespace Tests\Feature;

use App\Enums\PrivilegeKey;
use AuditLogClient\Enums\AuditLogEventObjectType;
use Illuminate\Support\Str;

class ObjectTypeBasedBodyCreators
{
    public static function buildObjectFromType(AuditLogEventObjectType $objectType): array
    {
        return match ($objectType) {
            AuditLogEventObjectType::User => ObjectTypeBasedBodyCreators::buildExampleUser(),
            AuditLogEventObjectType::InstitutionUser => ObjectTypeBasedBodyCreators::buildExampleInstitutionUser(),
            AuditLogEventObjectType::Role => ObjectTypeBasedBodyCreators::buildExampleRole(),
            AuditLogEventObjectType::Institution => ObjectTypeBasedBodyCreators::buildExampleInstitution(),
            AuditLogEventObjectType::Vendor => ObjectTypeBasedBodyCreators::buildExampleVendorSubsets(),
            AuditLogEventObjectType::InstitutionDiscount => ObjectTypeBasedBodyCreators::buildExampleInstitutionDiscount(),
            AuditLogEventObjectType::Project => ObjectTypeBasedBodyCreators::buildExampleProject(),
            AuditLogEventObjectType::Subproject => ObjectTypeBasedBodyCreators::buildExampleSubproject(),
            AuditLogEventObjectType::Assignment => ObjectTypeBasedBodyCreators::buildExampleAssignment(),
            AuditLogEventObjectType::TranslationMemory => ObjectTypeBasedBodyCreators::buildExampleTranslationMemory(),
        };
    }

    public static function buildExampleUser(): array
    {
        return [
            'forename' => 'Test',
        ];
    }

    public static function buildExampleInstitutionUser(): array
    {
        return [
            'email' => 'test@email.dev',
        ];
    }

    public static function buildExampleRole(): array
    {
        return [
            'privileges' => [
                ['key' => PrivilegeKey::CreateProject->value],
                ['key' => PrivilegeKey::ChangeClient->value],
            ],
        ];
    }

    public static function buildExampleInstitution(): array
    {
        return [
            'phone' => '+37266778899',
        ];

    }

    public static function buildExampleVendorSubsets(): array
    {
        return [
            'prices' => [
                [
                    'id' => Str::uuid()->toString(),
                    'character_fee' => 1.0,
                    'word_fee' => 2.0,
                    'page_fee' => 3.0,
                    'minute_fee' => 4.0,
                    'hour_fee' => 5.0,
                    'minimal_fee' => 6.0,
                    'skill' => [
                        'id' => Str::uuid()->toString(),
                        'name' => 'Test Name',
                    ],
                    'source_language_classifier_value' => [
                        'id' => Str::uuid()->toString(),
                        'type' => 'LANGUAGE',
                        'value' => 'Test Value 1',
                        'name' => 'Test Name 2',
                    ],
                    'destination_language_classifier_value' => [
                        'id' => Str::uuid()->toString(),
                        'type' => 'LANGUAGE',
                        'value' => 'Test Value 1',
                        'name' => 'Test Name 2',
                    ],
                ],
            ],
        ];
    }

    public static function buildExampleInstitutionDiscount(): array
    {
        return [
            'discount_percentage_0_49' => 0.0,
        ];

    }

    public static function buildExampleAssignment(): array
    {
        return [
            'assignee' => [
                'id' => Str::uuid()->toString(),
                'company_name' => 'Company Name',
                'institution_user' => [
                    'id' => Str::uuid()->toString(),
                    'user' => [
                        'id' => Str::uuid()->toString(),
                        'personal_identification_code' => '38911226041',
                        'forename' => 'Forename',
                        'surname' => 'Surname',
                    ],
                ],
            ],
        ];
    }

    public static function buildExampleTranslationMemory(): array
    {
        // TODO
        return [
            'name' => 'a',
        ];
    }

    public static function buildExampleProject(): array
    {
        return [
            'source_files' => [
                [
                    'id' => 1,
                    'name' => 'present',
                    'uuid' => Str::uuid()->toString(),
                    'file_name' => 'present.docx',
                ],
            ],
        ];
    }

    public static function buildExampleSubproject(): array
    {
        return [
            'assignments' => [
                [
                    'id' => Str::uuid()->toString(),
                    'ext_id' => Str::uuid()->toString(),
                ],
            ],
        ];
    }
}
