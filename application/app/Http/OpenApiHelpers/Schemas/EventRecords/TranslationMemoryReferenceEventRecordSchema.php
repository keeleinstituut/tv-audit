<?php

namespace App\Http\OpenApiHelpers\Schemas\EventRecords;

use App\Http\OpenApiHelpers\Schemas\EventRecords;
use AuditLogClient\Enums\AuditLogEventType;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Event record whose parameters hold a reference to a translation memory',
    allOf: [
        new OA\Schema(ref: EventRecords\EventRecordCommonSchema::class),
        new OA\Schema(
            required: ['event_type', 'event_parameters', 'failure_type'],
            properties: [
                new OA\Property(property: 'failure_type', type: 'null'),
                new OA\Property(
                    property: 'event_type',
                    type: 'string',
                    enum: [
                        AuditLogEventType::ImportTranslationMemory,
                        AuditLogEventType::ExportTranslationMemory]
                ),
                new OA\Property(
                    property: 'event_parameters',
                    required: [
                        'translation_memory_id',
                        'translation_memory_name',
                    ],
                    properties: [
                        new OA\Property(property: 'translation_memory_id', type: 'string'),
                        new OA\Property(property: 'translation_memory_name', type: 'string'),
                    ],
                    type: 'object',
                ),
            ],
        ),
    ]
)]
class TranslationMemoryReferenceEventRecordSchema
{
}
