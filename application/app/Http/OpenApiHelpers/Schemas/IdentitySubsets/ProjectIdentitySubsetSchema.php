<?php

namespace App\Http\OpenApiHelpers\Schemas\IdentitySubsets;

use AuditLogClient\Enums\AuditLogEventObjectType;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'PROJECT (TODO)',
    required: [
        'object_type',
        'object_identity_subset',
    ],
    properties: [
        new OA\Property(
            property: 'object_type',
            type: 'string',
            enum: [AuditLogEventObjectType::Project]
        ),
        new OA\Property(
            property: 'object_identity_subset',
            required: null, // TODO
            properties: [new OA\Property(property: 'TODO')], // TODO
            type: 'object',

        ),
    ],
    type: 'object'
)]
class ProjectIdentitySubsetSchema
{
}
