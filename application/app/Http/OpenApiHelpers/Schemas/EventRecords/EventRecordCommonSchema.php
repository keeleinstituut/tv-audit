<?php

namespace App\Http\OpenApiHelpers\Schemas\EventRecords;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Common Event Record Schema',
    required: [
        'id',
        'happened_at',
        'acting_user_pic',
        'acting_user_forename',
        'acting_user_surname',
        'acting_institution_user_id',
        'trace_id',
        'context_department_id',
        'context_institution_id',
    ],
    properties: [
        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
        new OA\Property(property: 'happened_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'acting_user_pic', type: 'string', nullable: true),
        new OA\Property(property: 'acting_user_forename', type: 'string', nullable: true),
        new OA\Property(property: 'acting_user_surname', type: 'string', nullable: true),
        new OA\Property(property: 'acting_institution_user_id', type: 'string', nullable: true),
        new OA\Property(property: 'trace_id', type: 'string', nullable: true),
        new OA\Property(property: 'context_department_id', type: 'string', format: 'uuid', nullable: true),
        new OA\Property(property: 'context_institution_id', type: 'string', format: 'uuid', nullable: true),
    ],
    type: 'object'
)]
class EventRecordCommonSchema
{
}
