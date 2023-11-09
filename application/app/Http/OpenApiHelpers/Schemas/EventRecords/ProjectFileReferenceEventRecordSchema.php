<?php

namespace App\Http\OpenApiHelpers\Schemas\EventRecords;

use App\Http\OpenApiHelpers\Schemas\EventRecords;
use AuditLogClient\Enums\AuditLogEventType;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Event record whose parameters hold a reference to a project’s file',
    allOf: [
        new OA\Schema(ref: EventRecords\EventRecordCommonSchema::class),
        new OA\Schema(
            required: ['event_type', 'event_parameters', 'failure_type'],
            properties: [
                new OA\Property(property: 'failure_type', type: 'null'),
                new OA\Property(
                    property: 'event_type',
                    type: 'string',
                    enum: [AuditLogEventType::DownloadProjectFile]
                ),
                new OA\Property(
                    property: 'event_parameters',
                    required: [
                        'media_id',
                        'project_id',
                        'project_ext_id',
                        'file_name',
                    ],
                    properties: [
                        new OA\Property(property: 'media_id', type: 'string'),
                        new OA\Property(property: 'project_id', type: 'string'),
                        new OA\Property(property: 'project_ext_id', type: 'string'),
                        new OA\Property(property: 'file_name', type: 'string'),
                    ],
                    type: 'object',
                ),
            ],
        ),
    ]
)]
class ProjectFileReferenceEventRecordSchema
{
}
