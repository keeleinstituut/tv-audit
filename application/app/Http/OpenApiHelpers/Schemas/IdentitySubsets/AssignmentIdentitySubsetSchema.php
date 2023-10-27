<?php

namespace App\Http\OpenApiHelpers\Schemas\IdentitySubsets;

use AuditLogClient\Enums\AuditLogEventObjectType;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'ASSIGNMENT',
    required: [
        'object_type',
        'object_identity_subset',
    ],
    properties: [
        new OA\Property(
            property: 'object_type',
            type: 'string',
            enum: [AuditLogEventObjectType::Assignment]
        ),
        new OA\Property(
            property: 'object_identity_subset',
            required: ['id', 'ext_id'],
            properties: [
                new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                new OA\Property(property: 'ext_id', type: 'string'),
            ],
            type: 'object',
        ),
    ],
    type: 'object'
)]
class AssignmentIdentitySubsetSchema
{
}
