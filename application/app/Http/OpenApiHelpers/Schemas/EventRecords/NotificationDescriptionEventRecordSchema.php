<?php

namespace App\Http\OpenApiHelpers\Schemas\EventRecords;

use App\Http\OpenApiHelpers\Schemas\EventRecords;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Event record whose parameters describe a dispatched notification',
    allOf: [
        new OA\Schema(ref: EventRecords\EventRecordCommonSchema::class),
        new OA\Schema(
            required: ['notification_type', 'notification_params'],
            properties: [
                new OA\Property(property: 'notification_type', type: 'string'),
                new OA\Property(
                    property: 'notification_params',
                    required: ['receiver_email'],
                    properties: [
                        new OA\Property(property: 'receiver_email', type: 'string'),
                        new OA\Property(property: 'receiver_name', type: 'string', nullable: true),
                        new OA\Property(property: 'variables', type: 'object', nullable: true),
                    ],
                    type: 'object',
                ),
            ],
        ),
    ]
)]
class NotificationDescriptionEventRecordSchema
{
}
