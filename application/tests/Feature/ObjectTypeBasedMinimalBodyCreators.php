<?php

namespace Tests\Feature;

use App\Enums\EventType;
use App\Enums\ObjectType;
use App\Enums\PrivilegeKey;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class ObjectTypeBasedMinimalBodyCreators
{
    public static function buildObjectIdentityReferenceMessage(EventType $eventType, ObjectType $objectType): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => $eventType->value,
            'event_parameters' => [
                'object_type' => $objectType->value,
                'object_id' => Str::uuid()->toString(),
            ],
        ];
    }

    public static function buildModifyObjectMessage(ObjectType $objectType): array
    {
        return [
            ...static::createBaseSuccessMessageBody(),
            'event_type' => EventType::ModifyObject->value,
            'event_parameters' => [
                'object_type' => $objectType->value,
                'object_id' => Str::uuid()->toString(),
                ...static::buildExamplePrePostModificationSubsets($objectType),
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

    private static function buildExamplePrePostModificationSubsets(ObjectType $objectType): array
    {
        return match ($objectType) {
            ObjectType::User => static::buildExampleUserSubsets(),
            ObjectType::InstitutionUser => static::buildExampleInstitutionUserSubsets(),
            ObjectType::Role => static::buildExampleRoleSubsets(),
            ObjectType::Institution => static::buildExampleInstitutionSubsets(),
            ObjectType::Vendor => static::buildExampleVendorSubsets(),
            ObjectType::InstitutionDiscount => static::buildExampleInstitutionDiscountSubsets(),
            ObjectType::Project => static::buildExampleProjectSubsets(),
            ObjectType::Subproject => static::buildExampleSubprojectSubsets(),
            ObjectType::Assignment => static::buildExampleAssignmentSubsets(),
            ObjectType::Volume => static::buildExampleVolumeSubsets(),
            ObjectType::TranslationMemory => static::buildExampleTranslationMemorySubsets(),
        };
    }

    private static function buildExampleUserSubsets(): array
    {
        return [
            'pre_modification_subset' => [
                'forename' => 'Test',
            ],
            'post_modification_subset' => [
                'forename' => 'User',
            ],

        ];
    }

    private static function buildExampleInstitutionUserSubsets(): array
    {
        return [
            'pre_modification_subset' => [
                'email' => 'test@email.dev',
            ],
            'post_modification_subset' => [
                'email' => 'email@test.dev',
            ],
        ];
    }

    private static function buildExampleRoleSubsets(): array
    {
        return [
            'pre_modification_subset' => [
                'privileges' => [
                    ['key' => PrivilegeKey::CreateProject->value],
                    ['key' => PrivilegeKey::ChangeClient->value],
                ],
            ],
            'post_modification_subset' => [
                'privileges' => [
                    ['key' => PrivilegeKey::AddRole->value],
                ],

            ],
        ];

    }

    private static function buildExampleInstitutionSubsets(): array
    {
        return [
            'pre_modification_subset' => [
                'phone' => '+37266778899',
            ],
            'post_modification_subset' => [
                'phone' => '+37266778800',
            ],
        ];

    }

    private static function buildExampleVendorSubsets(): array
    {
        return [
            'pre_modification_subset' => [
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
            ],
            'post_modification_subset' => [
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
                    [
                        'id' => Str::uuid()->toString(),
                        'character_fee' => 9.0,
                        'word_fee' => 8.0,
                        'page_fee' => 7.0,
                        'minute_fee' => 6.0,
                        'hour_fee' => 5.0,
                        'minimal_fee' => 4.0,
                        'skill' => [
                            'id' => Str::uuid()->toString(),
                            'name' => 'Name Test',
                        ],
                        'source_language_classifier_value' => [
                            'id' => Str::uuid()->toString(),
                            'type' => 'LANGUAGE',
                            'value' => 'Value Test 1',
                            'name' => 'Name Test 2',
                        ],
                        'destination_language_classifier_value' => [
                            'id' => Str::uuid()->toString(),
                            'type' => 'LANGUAGE',
                            'value' => 'Value Test 1',
                            'name' => 'Name Test 2',
                        ],
                    ],
                ],
            ],
        ];
    }

    private static function buildExampleInstitutionDiscountSubsets(): array
    {
        return [
            'pre_modification_subset' => [
                'discount_percentage_0_49' => 0.0,
            ],
            'post_modification_subset' => [
                'discount_percentage_0_49' => 80.0,
            ],
        ];

    }

    private static function buildExampleAssignmentSubsets(): array
    {
        return [
            'pre_modification_subset' => [
                'assignee' => null,
            ],
            'post_modification_subset' => [
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
            ],
        ];
    }

    private static function buildExampleTranslationMemorySubsets(): array
    {
        // TODO
        return [
            'pre_modification_subset' => ['name' => 'a'],
            'post_modification_subset' => ['name' => 'b'],
        ];
    }

    private static function buildExampleVolumeSubsets(): array
    {
        return [
            'pre_modification_subset' => [
                'volume_analysis' => [
                    'files_names' => ['file1.docx, file2.docx'],
                ],
            ],
            'post_modification_subset' => [
                'volume_analysis' => [
                    'files_names' => ['file1.docx', 'file2.docx', 'file3.pdf'],
                ],
            ],
        ];

    }

    private static function buildExampleProjectSubsets(): array
    {
        return [
            'pre_modification_subset' => [
                'source_files' => [
                    [
                        'id' => 1,
                        'name' => 'present',
                        'uuid' => Str::uuid()->toString(),
                        'file_name' => 'present.docx',
                    ],
                ],
            ],
            'post_modification_subset' => [
                'source_files' => [
                    [
                        'id' => 1,
                        'name' => 'present',
                        'uuid' => Str::uuid()->toString(),
                        'file_name' => 'present.docx',
                    ],
                    [
                        'id' => 2,
                        'name' => 'help',
                        'uuid' => Str::uuid()->toString(),
                        'file_name' => 'help.docx',
                    ],
                ],
            ],
        ];
    }

    private static function buildExampleSubprojectSubsets(): array
    {
        return [
            'pre_modification_subset' => [
                'assignments' => [
                    [
                        'id' => Str::uuid()->toString(),
                        'ext_id' => Str::uuid()->toString(),
                    ],
                ],
            ],
            'post_modification_subset' => [
                'assignments' => [
                    [
                        'id' => Str::uuid()->toString(),
                        'ext_id' => Str::uuid()->toString(),
                    ],
                    [
                        'id' => Str::uuid()->toString(),
                        'ext_id' => Str::uuid()->toString(),
                    ],
                ],
            ],
        ];
    }
}
