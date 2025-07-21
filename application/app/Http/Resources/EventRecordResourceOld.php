<?php

namespace App\Http\Resources;

use App\Http\OpenApiHelpers as OAH;
use App\Models\EventRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

/**
 * @mixin EventRecord
 */
#[OA\Schema(
    title: 'Event Record',
    required: [
        'id',
        'happened_at',
        'acting_user_pic',
        'acting_user_forename',
        'acting_user_surname',
        'acting_institution_user_id',
        'event_type',
        'event_parameters',
        'trace_id',
        'context_department_id',
        'context_institution_id',
        'failure_type',
    ],
    type: 'object',
    oneOf: [
        new OA\Schema(ref: OAH\Schemas\EventRecords\ParameterlessEventRecordSchema::class),
        new OA\Schema(ref: OAH\Schemas\EventRecords\AssignmentReferenceEventRecordSchema::class),
        new OA\Schema(ref: OAH\Schemas\EventRecords\AuditLogQueryEventRecordSchema::class),
        new OA\Schema(ref: OAH\Schemas\EventRecords\TranslationMemoryReferenceEventRecordSchema::class),
        new OA\Schema(ref: OAH\Schemas\EventRecords\ProjectExportQueryEventRecordSchema::class),
        new OA\Schema(ref: OAH\Schemas\EventRecords\ProjectFileReferenceEventRecordSchema::class),
        new OA\Schema(ref: OAH\Schemas\EventRecords\NotificationDescriptionEventRecordSchema::class),
        new OA\Schema(ref: OAH\Schemas\EventRecords\WorkflowReferenceEventRecordSchema::class),
        new OA\Schema(ref: OAH\Schemas\EventRecords\ProjectReferenceEventRecordSchema::class),
        new OA\Schema(ref: OAH\Schemas\EventRecords\CreateObjectEventRecordSchema::class),
        new OA\Schema(ref: OAH\Schemas\EventRecords\RemoveObjectEventRecordSchema::class),
        new OA\Schema(ref: OAH\Schemas\EventRecords\ModifyObjectEventRecordSchema::class),
        new OA\Schema(ref: OAH\Schemas\EventRecords\FailedAttemptEventRecordSchema::class),
    ]
)]
class EventRecordResourceOld extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->only(
            'id',
            'happened_at',
            'acting_user_pic',
            'acting_user_forename',
            'acting_user_surname',
            'acting_institution_user_id',
            'event_type',
            'event_parameters',
            'trace_id',
            'context_department_id',
            'context_institution_id',
            'failure_type',
        );
    }
}
