<?php

namespace App\Http\OpenApiHelpers\Schemas\IdentitySubsets;

use AuditLogClient\Enums\AuditLogEventObjectType;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'INSTITUTION_USER',
    required: [
        'object_type',
        'object_identity_subset',
    ],
    properties: [
        new OA\Property(
            property: 'object_type',
            type: 'string',
            enum: [AuditLogEventObjectType::InstitutionUser]
        ),
        new OA\Property(
            property: 'object_identity_subset',
            required: ['id', 'user'],
            properties: [
                new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                new OA\Property(
                    property: 'user',
                    required: ['id', 'personal_identification_code', 'forename', 'surname'],
                    properties: [
                        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                        new OA\Property(property: 'personal_identification_code', type: 'string'),
                        new OA\Property(property: 'forename', type: 'string'),
                        new OA\Property(property: 'surname', type: 'string'),
                    ],
                    type: 'object'
                ),
            ],
            type: 'object'
        ),
    ],
    type: 'object'
)]
class InstitutionUserIdentitySubsetSchema
{
}
