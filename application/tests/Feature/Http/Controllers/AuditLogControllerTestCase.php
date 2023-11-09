<?php

namespace Tests\Feature\Http\Controllers;

use AuditLogClient\Enums\AuditLogEventObjectType;
use AuditLogClient\Enums\AuditLogEventType;
use Carbon\CarbonInterface;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use League\Csv\Reader;
use Tests\TestCase;

class AuditLogControllerTestCase extends TestCase
{
    const TRACE_ID = '123-ABC';

    public static function buildQueryParameters(
        CarbonInterface $startDatetime = null,
        CarbonInterface $endDatetime = null,
        string $departmentId = null,
        AuditLogEventType $eventType = null,
        string $text = null
    ): array {
        return [
            'start_datetime' => $startDatetime?->toIso8601ZuluString(),
            'end_datetime' => $endDatetime?->toIso8601ZuluString(),
            'department_id' => $departmentId,
            'event_type' => $eventType?->value,
            'text' => $text,
        ];
    }

    /**
     * @return array<array{string, array}>
     */
    public static function provideSearchTextAndMatchingFactoryStates(): array
    {
        return [
            'Searching by project ext_id (FINISH_PROJECT)' => [
                'PPA-2023-01-K-354',
                [
                    'event_type' => AuditLogEventType::FinishProject->value,
                    'event_parameters' => [
                        'project_id' => Str::uuid()->toString(),
                        'project_ext_id' => 'PPA-2023-01-K-354',
                    ],
                ],
            ],
            'Searching by project ext_id (COMPLETE_ASSIGNMENT)' => [
                'PPA-2023-01-K-354',
                [
                    'event_type' => AuditLogEventType::CompleteAssignment->value,
                    'event_parameters' => [
                        'assignment_id' => Str::uuid()->toString(),
                        'assignment_ext_id' => 'PPA-2023-01-K-354-ETEN-1/1',
                    ],
                ],
            ],
            'Searching by project ext_id (DOWNLOAD_PROJECT_FILE)' => [
                'PPA-2023-01-K-354',
                [
                    'event_type' => AuditLogEventType::DownloadProjectFile->value,
                    'event_parameters' => [
                        'media_id' => Str::uuid()->toString(),
                        'project_id' => Str::uuid()->toString(),
                        'project_ext_id' => 'PPA-2023-01-K-354-ETEN-1/1',
                        'file_name' => 'lähtetekst.docx',
                    ],
                ],
            ],
            'Searching by project ext_id (SEARCH_LOGS)' => [
                'PPA-2023-01-K-354',
                [
                    'event_type' => AuditLogEventType::SearchLogs->value,
                    'event_parameters' => [
                        'query_start_datetime' => null,
                        'query_end_datetime' => null,
                        'query_event_type' => null,
                        'query_text' => 'PPA-2023-01-K-354',
                        'query_department_id' => null,
                    ],
                ],
            ],
            'Searching by project ext_id (CREATE_OBJECT: PROJECT)' => [
                'PPA-2023-01-K-354',
                [
                    'event_type' => AuditLogEventType::CreateObject->value,
                    'event_parameters' => [
                        'object_type' => AuditLogEventObjectType::Project->value,
                        'object_data' => [
                            'id' => Str::uuid()->toString(),
                            'ext_id' => 'PPA-2023-01-K-354',
                        ],
                        'object_identity_subset' => [
                            'id' => Str::uuid()->toString(),
                            'ext_id' => 'PPA-2023-01-K-354',
                        ],
                    ],
                ],
            ],
            'Searching by project ext_id (MODIFY_OBJECT: SUBPROJECT)' => [
                'PPA-2023-01-K-354',
                [
                    'event_type' => AuditLogEventType::ModifyObject->value,
                    'event_parameters' => [
                        'object_type' => AuditLogEventObjectType::Subproject->value,
                        'object_identity_subset' => [
                            'id' => Str::uuid()->toString(),
                            'ext_id' => 'PPA-2023-01-K-354',
                        ],
                        'pre_modification_subset' => [
                            'comment' => 'Hello',
                        ],
                        'post_modification_subset' => [
                            'comment' => null,
                        ],
                    ],
                ],
            ],
            'Searching by personal identification code' => [
                '50807205703',
                ['acting_user_pic' => '50807205703'],
            ],
            'Searching by forename' => [
                'Audiitorikesekene',
                ['acting_user_forename' => 'Audiitorikesekene'],
            ],
            'Searching by surname' => [
                'Audiitorikesekene',
                ['acting_user_surname' => 'Audiitorikesekene'],
            ],
            'Searching by full name' => [
                'Audioonatan Audioosna',
                [
                    'acting_user_forename' => 'Audioonatan',
                    'acting_user_surname' => 'Audioosna',
                ],
            ],
        ];
    }

    private static function createCsvReader(TestResponse $response): Reader
    {
        return Reader::createFromString($response->streamedContent())
            ->setDelimiter(';')
            ->setHeaderOffset(0);
    }
}
