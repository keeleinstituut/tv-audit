<?php

namespace Tests\Feature;

use App\Enums\PrivilegeKey;
use AuditLogClient\Enums\AuditLogEventObjectType;
use Illuminate\Support\Str;

class ObjectDataCreators
{
    public static function buildObjectFromType(AuditLogEventObjectType $objectType): array
    {
        return match ($objectType) {
            AuditLogEventObjectType::InstitutionUser => ObjectDataCreators::institutionUser(),
            AuditLogEventObjectType::Role => ObjectDataCreators::role(),
            AuditLogEventObjectType::Institution => ObjectDataCreators::institution(),
            AuditLogEventObjectType::Vendor => ObjectDataCreators::vendor(),
            AuditLogEventObjectType::InstitutionDiscount => ObjectDataCreators::institutionDiscount(),
            AuditLogEventObjectType::Project => ObjectDataCreators::project(),
            AuditLogEventObjectType::Subproject => ObjectDataCreators::subproject(),
            AuditLogEventObjectType::Assignment => ObjectDataCreators::assignment(),
            AuditLogEventObjectType::TranslationMemory => ObjectDataCreators::translationMemory(),
        };
    }

    public static function user(): array
    {
        return [
            'forename' => 'Test',
        ];
    }

    public static function institutionUser(): array
    {
        return [
            'email' => 'test@email.dev',
        ];
    }

    public static function role(): array
    {
        return [
            'privileges' => [
                ['key' => PrivilegeKey::CreateProject->value],
                ['key' => PrivilegeKey::ChangeClient->value],
            ],
        ];
    }

    public static function institution(): array
    {
        return [
            'phone' => '+37266778899',
        ];

    }

    public static function vendor(): array
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

    public static function institutionDiscount(): array
    {
        return [
            'discount_percentage_0_49' => 0.0,
        ];

    }

    public static function assignment(): array
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

    public static function translationMemory(): array
    {
        // TODO
        return [
            'name' => 'a',
        ];
    }

    public static function project(): array
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

    public static function subproject(): array
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
