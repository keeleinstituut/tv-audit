<?php

namespace App\Http\OpenApiHelpers\Schemas\IdentitySubsets;

use AuditLogClient\Enums\AuditLogEventObjectType;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'INSTITUTION_DISCOUNT',
    required: [
        'object_type',
        'object_identity_subset',
    ],
    properties: [
        new OA\Property(
            property: 'object_type',
            type: 'string',
            enum: [AuditLogEventObjectType::InstitutionDiscount]
        ),
        new OA\Property(
            property: 'object_identity_subset',
            required: ['id', 'institution'],
            properties: [
                new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                new OA\Property(
                    property: 'institution',
                    required: ['id', 'name'],
                    properties: [
                        new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                        new OA\Property(property: 'name', type: 'string'),
                    ],
                    type: 'object'
                ),
            ],
            type: 'object'
        ),
    ],
    type: 'object'
)]
class InstitutionDiscountIdentitySubsetSchema
{
}
