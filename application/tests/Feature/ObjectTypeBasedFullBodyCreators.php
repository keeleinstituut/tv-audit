<?php

namespace Tests\Feature;

use App\Enums\EventType;
use App\Enums\ObjectType;
use App\Enums\PrivilegeKey;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;

class ObjectTypeBasedFullBodyCreators
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
                'surname' => 'User',
            ],
            'post_modification_subset' => [
                'forename' => 'User',
                'surname' => 'Test',
            ],

        ];
    }

    private static function buildExampleInstitutionUserSubsets(): array
    {
        return [
            'pre_modification_subset' => [
                'email' => 'test@email.dev',
                'phone' => '+372 5566 7788',
                'archived_at' => null,
                'deactivation_date' => '2023-07-22',
                'department_id' => Str::uuid()->toString(),
                'worktime_timezone' => 'Europe/Tallinn',
                'monday_worktime_start' => '08:00',
                'monday_worktime_end' => '17:00',
                'tuesday_worktime_start' => '09:00',
                'tuesday_worktime_end' => '10:00',
                'wednesday_worktime_start' => null,
                'wednesday_worktime_end' => null,
                'thursday_worktime_start' => null,
                'thursday_worktime_end' => null,
                'friday_worktime_start' => null,
                'friday_worktime_end' => null,
                'saturday_worktime_start' => null,
                'saturday_worktime_end' => null,
                'sunday_worktime_start' => null,
                'sunday_worktime_end' => null,
            ],
            'post_modification_subset' => [
                'email' => 'email@test.dev',
                'phone' => '+372 5599 88778',
                'archived_at' => '2023-07-22T03:00Z',
                'deactivation_date' => null,
                'department_id' => null,
                'worktime_timezone' => 'Europe/Berlin',
                'monday_worktime_start' => null,
                'monday_worktime_end' => null,
                'tuesday_worktime_start' => null,
                'tuesday_worktime_end' => null,
                'wednesday_worktime_start' => '10:00',
                'wednesday_worktime_end' => '14:00',
                'thursday_worktime_start' => '10:00',
                'thursday_worktime_end' => '14:00',
                'friday_worktime_start' => '10:00',
                'friday_worktime_end' => '14:00',
                'saturday_worktime_start' => '10:00',
                'saturday_worktime_end' => '14:00',
                'sunday_worktime_start' => '10:00',
                'sunday_worktime_end' => '14:00',
            ],
        ];
    }

    private static function buildExampleRoleSubsets(): array
    {
        return [
            'pre_modification_subset' => [
                'name' => 'Role 1',
                'is_root' => true,
                'privileges' => [
                    ['key' => PrivilegeKey::CreateProject->value],
                    ['key' => PrivilegeKey::ChangeClient->value],
                ],
            ],
            'post_modification_subset' => [
                'name' => 'Role 2',
                'is_root' => false,
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
                'name' => 'Test Institution',
                'short_name' => null,
                'phone' => '+37266778899',
                'email' => 'test@email.dev',
                'logo_url' => 'https://google.com/favicon.ico',
                'worktime_timezone' => 'Europe/Tallinn',
                'monday_worktime_start' => '08:00',
                'monday_worktime_end' => '17:00',
                'tuesday_worktime_start' => '09:00',
                'tuesday_worktime_end' => '10:00',
                'wednesday_worktime_start' => null,
                'wednesday_worktime_end' => null,
                'thursday_worktime_start' => null,
                'thursday_worktime_end' => null,
                'friday_worktime_start' => null,
                'friday_worktime_end' => null,
                'saturday_worktime_start' => null,
                'saturday_worktime_end' => null,
                'sunday_worktime_start' => null,
                'sunday_worktime_end' => null,
            ],
            'post_modification_subset' => [
                'name' => 'Institution Test',
                'short_name' => 'RIR',
                'phone' => '+37266778800',
                'email' => 'test@email.com',
                'logo_url' => null,
                'worktime_timezone' => 'Europe/Berlin',
                'monday_worktime_start' => null,
                'monday_worktime_end' => null,
                'tuesday_worktime_start' => null,
                'tuesday_worktime_end' => null,
                'wednesday_worktime_start' => '10:00',
                'wednesday_worktime_end' => '14:00',
                'thursday_worktime_start' => '10:00',
                'thursday_worktime_end' => '14:00',
                'friday_worktime_start' => '10:00',
                'friday_worktime_end' => '14:00',
                'saturday_worktime_start' => '10:00',
                'saturday_worktime_end' => '14:00',
                'sunday_worktime_start' => '10:00',
                'sunday_worktime_end' => '14:00',
            ],
        ];

    }

    private static function buildExampleVendorSubsets(): array
    {
        return [
            'pre_modification_subset' => [
                'company_name' => 'Vendor Company',
                'comment' => null,
                'discount_percentage_101' => 30.0,
                'discount_percentage_repetitions' => 70.0,
                'discount_percentage_100' => 20.0,
                'discount_percentage_95_99' => 10.0,
                'discount_percentage_85_94' => 5.0,
                'discount_percentage_75_84' => 9.0,
                'discount_percentage_50_74' => 3.0,
                'discount_percentage_0_49' => 2.0,
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
                'company_name' => 'Company Vendor',
                'comment' => 'Comment',
                'discount_percentage_101' => null,
                'discount_percentage_repetitions' => null,
                'discount_percentage_100' => null,
                'discount_percentage_95_99' => null,
                'discount_percentage_85_94' => null,
                'discount_percentage_75_84' => null,
                'discount_percentage_50_74' => null,
                'discount_percentage_0_49' => null,
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
                'discount_percentage_101' => 0.0,
                'discount_percentage_repetitions' => 0.0,
                'discount_percentage_100' => 0.0,
                'discount_percentage_95_99' => 0.0,
                'discount_percentage_85_94' => 0.0,
                'discount_percentage_75_84' => 0.0,
                'discount_percentage_50_74' => 0.0,
                'discount_percentage_0_49' => 0.0,
            ],
            'post_modification_subset' => [
                'discount_percentage_101' => 10.0,
                'discount_percentage_repetitions' => 20.0,
                'discount_percentage_100' => 30.0,
                'discount_percentage_95_99' => 40.0,
                'discount_percentage_85_94' => 50.0,
                'discount_percentage_75_84' => 60.0,
                'discount_percentage_50_74' => 70.0,
                'discount_percentage_0_49' => 80.0,
            ],
        ];

    }

    private static function buildExampleAssignmentSubsets(): array
    {
        return [
            'pre_modification_subset' => [
                'id' => Str::uuid()->toString(),
                'ext_id' => Str::uuid()->toString(),
                'deadline_at' => Date::yesterday()->toISOString(),
                'comments' => null,
                'assignee_comments' => null,
                'feature' => 'job_translation',
                'assignee' => null,
                'candidates' => [
                    [
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
                'volumes' => [
                    [
                        'id' => Str::uuid()->toString(),
                        'ext_id' => Str::uuid()->toString(),
                    ],
                ],
                'jobs' => [
                    [
                        'id' => Str::uuid()->toString(),
                        'name' => Str::uuid()->toString(),
                    ],
                ],
            ],
            'post_modification_subset' => [
                'id' => Str::uuid()->toString(),
                'ext_id' => Str::uuid()->toString(),
                'deadline_at' => Date::yesterday()->toISOString(),
                'comments' => 'Comment',
                'assignee_comments' => '+1',
                'feature' => 'job_overview',
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
                'candidates' => [],
                'volumes' => [
                    [
                        'id' => Str::uuid()->toString(),
                        'ext_id' => Str::uuid()->toString(),
                    ],
                    [
                        'id' => Str::uuid()->toString(),
                        'ext_id' => Str::uuid()->toString(),
                    ],
                ],
                'jobs' => [
                    [
                        'id' => Str::uuid()->toString(),
                        'name' => Str::uuid()->toString(),
                    ],
                    [
                        'id' => Str::uuid()->toString(),
                        'name' => Str::uuid()->toString(),
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
                'unit_type' => 'CHARACTERS',
                'unit_quantity' => '100',
                'unit_fee' => '100',
                'job' => [
                    'id' => Str::uuid()->toString(),
                    'name' => 'Job Name',
                ],
                'volume_analysis' => [
                    'total' => 10,
                    'tm_101' => 10,
                    'repetitions' => 10,
                    'tm_100' => 10,
                    'tm_95_99' => 10,
                    'tm_85_94' => 10,
                    'tm_75_84' => 10,
                    'tm_50_74' => 10,
                    'tm_0_49' => 10,
                    'files_names' => ['file1.docx, file2.docx'],
                ],
                'discount' => [
                    'discount_percentage_101' => 0.0,
                    'discount_percentage_repetitions' => 0.0,
                    'discount_percentage_100' => 0.0,
                    'discount_percentage_95_99' => 0.0,
                    'discount_percentage_85_94' => 0.0,
                    'discount_percentage_75_84' => 0.0,
                    'discount_percentage_50_74' => 0.0,
                    'discount_percentage_0_49' => 0.0,
                ],
            ],
            'post_modification_subset' => [
                'unit_type' => 'PAGES',
                'unit_quantity' => '1000',
                'unit_fee' => '1000',
                'job' => [
                    'id' => Str::uuid()->toString(),
                    'name' => 'Name Job',
                ],
                'volume_analysis' => [
                    'total' => 100,
                    'tm_101' => 100,
                    'repetitions' => 100,
                    'tm_100' => 100,
                    'tm_95_99' => 100,
                    'tm_85_94' => 100,
                    'tm_75_84' => 100,
                    'tm_50_74' => 100,
                    'tm_0_49' => 100,
                    'files_names' => ['file1.docx', 'file2.docx', 'file3.pdf'],
                ],
                'discount' => [
                    'discount_percentage_101' => 100.0,
                    'discount_percentage_repetitions' => 100.0,
                    'discount_percentage_100' => 100.0,
                    'discount_percentage_95_99' => 100.0,
                    'discount_percentage_85_94' => 100.0,
                    'discount_percentage_75_84' => 100.0,
                    'discount_percentage_50_74' => 100.0,
                    'discount_percentage_0_49' => 100.0,
                ],
            ],
        ];

    }

    private static function buildExampleProjectSubsets(): array
    {
        return [
            'pre_modification_subset' => [
                'ext_id' => Str::uuid()->toString(),
                'reference_number' => null,
                'comments' => null,
                'workflow_template_id' => Str::uuid()->toString(),
                'workflow_instance_ref' => Str::uuid()->toString(),
                'price' => 100,
                'deadline_at' => null,
                'event_start_at' => null,
                'status' => 'NEW',
                'source_files' => [
                    [
                        'id' => 1,
                        'name' => 'present',
                        'uuid' => Str::uuid()->toString(),
                        'file_name' => 'present.docx',
                    ],
                ],
                'help_files' => [
                    [
                        'id' => 2,
                        'name' => 'help',
                        'uuid' => Str::uuid()->toString(),
                        'file_name' => 'help.docx',
                    ],
                ],
                'final_files' => [
                    [
                        'id' => 3,
                        'name' => 'final',
                        'uuid' => Str::uuid()->toString(),
                        'file_name' => 'final.pdf',
                    ],
                ],

                'sub_projects' => [
                    [
                        'id' => Str::uuid()->toString(),
                        'ext_id' => 'LONG_ID',
                        'source_language_classifier_value' => [
                            'id' => Str::uuid()->toString(),
                            'type' => 'LANGUAGE',
                            'value' => 'ET-EE',
                            'name' => 'Eesti',
                        ],
                        'destination_language_classifier_value' => [
                            'id' => Str::uuid()->toString(),
                            'type' => 'LANGUAGE',
                            'value' => 'RU-RU',
                            'name' => 'Vene',
                        ],
                    ],
                ],

                'translation_domain_classifier_value' => [
                    'id' => Str::uuid()->toString(),
                    'type' => 'DOMAIN',
                    'value' => 'OSK',
                    'name' => 'OSK',
                ],

                'type_classifier_value' => [
                    'id' => 'required',
                    'type' => 'required',
                    'value' => 'required',
                    'name' => 'required',
                ],

                'client_institution_user' => [
                    'id' => Str::uuid()->toString(),
                    'user' => [
                        'id' => Str::uuid()->toString(),
                        'personal_identification_code' => '46812264771',
                        'forename' => 'forename',
                        'surname' => 'surname',
                    ],
                ],

                'manager_institution_user' => [
                    'id' => Str::uuid()->toString(),
                    'user' => [
                        'id' => Str::uuid()->toString(),
                        'personal_identification_code' => '46812264771',
                        'forename' => 'forename',
                        'surname' => 'surname',
                    ],
                ],
            ],
            'post_modification_subset' => [
                'ext_id' => Str::uuid()->toString(),
                'reference_number' => null,
                'comments' => null,
                'workflow_template_id' => Str::uuid()->toString(),
                'workflow_instance_ref' => Str::uuid()->toString(),
                'price' => 100,
                'deadline_at' => null,
                'event_start_at' => null,
                'status' => 'NEW',
                'source_files' => [
                    [
                        'id' => 1,
                        'name' => 'present',
                        'uuid' => Str::uuid()->toString(),
                        'file_name' => 'present.docx',
                    ],
                ],
                'help_files' => [
                    [
                        'id' => 2,
                        'name' => 'help',
                        'uuid' => Str::uuid()->toString(),
                        'file_name' => 'help.docx',
                    ],
                ],
                'final_files' => [
                    [
                        'id' => 3,
                        'name' => 'final',
                        'uuid' => Str::uuid()->toString(),
                        'file_name' => 'final.pdf',
                    ],
                ],

                'sub_projects' => [
                    [
                        'id' => Str::uuid()->toString(),
                        'ext_id' => 'LONG_ID',
                        'source_language_classifier_value' => [
                            'id' => Str::uuid()->toString(),
                            'type' => 'LANGUAGE',
                            'value' => 'ET-EE',
                            'name' => 'Eesti',
                        ],
                        'destination_language_classifier_value' => [
                            'id' => Str::uuid()->toString(),
                            'type' => 'LANGUAGE',
                            'value' => 'RU-RU',
                            'name' => 'Vene',
                        ],
                    ],
                ],

                'translation_domain_classifier_value' => [
                    'id' => Str::uuid()->toString(),
                    'type' => 'DOMAIN',
                    'value' => 'OSK',
                    'name' => 'OSK',
                ],

                'type_classifier_value' => [
                    'id' => 'required',
                    'type' => 'required',
                    'value' => 'required',
                    'name' => 'required',
                ],

                'client_institution_user' => [
                    'id' => Str::uuid()->toString(),
                    'user' => [
                        'id' => Str::uuid()->toString(),
                        'personal_identification_code' => '46812264771',
                        'forename' => 'forename',
                        'surname' => 'surname',
                    ],
                ],

                'manager_institution_user' => [
                    'id' => Str::uuid()->toString(),
                    'user' => [
                        'id' => Str::uuid()->toString(),
                        'personal_identification_code' => '46812264771',
                        'forename' => 'forename',
                        'surname' => 'surname',
                    ],
                ],
            ],
        ];
    }

    private static function buildExampleSubprojectSubsets(): array
    {
        return [
            'pre_modification_subset' => [
                'ext_id' => Str::uuid()->toString(),
                'deadline_at' => null,
                'price' => 30,
                'features' => ['feature_1'],
                'mt_enabled' => false,
                'source_language_classifier_value' => [
                    'id' => Str::uuid()->toString(),
                    'type' => 'LANGUAGE',
                    'value' => 'ET-EE',
                    'name' => 'Eesti',
                ],
                'destination_language_classifier_value' => [
                    'id' => Str::uuid()->toString(),
                    'type' => 'LANGUAGE',
                    'value' => 'RU-RU',
                    'name' => 'Vene',
                ],
                'assignments' => [
                    [
                        'id' => Str::uuid()->toString(),
                        'ext_id' => Str::uuid()->toString(),
                    ],
                ],
                'source_files' => [
                    [
                        'id' => 2,
                        'name' => 'source',
                        'uuid' => Str::uuid()->toString(),
                        'file_name' => 'src.docx',
                    ],
                ],
                'final_files' => [
                    [
                        'id' => 2,
                        'name' => 'final',
                        'uuid' => Str::uuid()->toString(),
                        'file_name' => 'final.docx',
                    ],
                ],
                'cat_files' => [
                    [
                        'id' => 2,
                        'name' => 'cat',
                        'uuid' => Str::uuid()->toString(),
                        'file_name' => 'cat.docs',
                    ],
                ],
                'cat_jobs' => [
                    [
                        'id' => Str::uuid()->toString(),
                        'name' => 'present',
                    ],
                ],
            ],
            'post_modification_subset' => [
                'ext_id' => Str::uuid()->toString(),
                'deadline_at' => null,
                'price' => 30,
                'features' => ['feature_1'],
                'mt_enabled' => false,
                'source_language_classifier_value' => [
                    'id' => Str::uuid()->toString(),
                    'type' => 'LANGUAGE',
                    'value' => 'ET-EE',
                    'name' => 'Eesti',
                ],
                'destination_language_classifier_value' => [
                    'id' => Str::uuid()->toString(),
                    'type' => 'LANGUAGE',
                    'value' => 'RU-RU',
                    'name' => 'Vene',
                ],
                'assignments' => [
                    [
                        'id' => Str::uuid()->toString(),
                        'ext_id' => Str::uuid()->toString(),
                    ],
                ],
                'source_files' => [
                    [
                        'id' => 2,
                        'name' => 'source',
                        'uuid' => Str::uuid()->toString(),
                        'file_name' => 'src.docx',
                    ],
                ],
                'final_files' => [
                    [
                        'id' => 2,
                        'name' => 'final',
                        'uuid' => Str::uuid()->toString(),
                        'file_name' => 'final.docx',
                    ],
                ],
                'cat_files' => [
                    [
                        'id' => 2,
                        'name' => 'cat',
                        'uuid' => Str::uuid()->toString(),
                        'file_name' => 'cat.docs',
                    ],
                ],
                'cat_jobs' => [
                    [
                        'id' => Str::uuid()->toString(),
                        'name' => 'present',
                    ],
                ],
            ],
        ];
    }
}
