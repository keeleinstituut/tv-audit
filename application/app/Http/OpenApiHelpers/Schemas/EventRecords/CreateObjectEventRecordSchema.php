<?php

namespace App\Http\OpenApiHelpers\Schemas\EventRecords;

use App\Http\OpenApiHelpers\Schemas\EventRecords;
use App\Http\OpenApiHelpers\Schemas\IdentitySubsets;
use AuditLogClient\Enums\AuditLogEventType;
use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Event record whose parameters describe an attempt to create an object',
    allOf: [
        new OA\Schema(ref: EventRecords\EventRecordCommonSchema::class),
        new OA\Schema(
            required: ['event_type', 'event_parameters', 'failure_type'],
            properties: [
                new OA\Property(property: 'failure_type', type: 'null'),
                new OA\Property(
                    property: 'event_type',
                    type: 'string',
                    enum: [AuditLogEventType::CreateObject]
                ),
                new OA\Property(
                    property: 'event_parameters',
                    oneOf: [
                        new OA\Schema(allOf: [
                            new OA\Schema(ref: EventRecords\CreateObjectEventParametersCommonSchema::class),
                            new OA\Schema(ref: IdentitySubsets\InstitutionIdentitySubsetSchema::class),
                        ]),
                        new OA\Schema(allOf: [
                            new OA\Schema(ref: EventRecords\CreateObjectEventParametersCommonSchema::class),
                            new OA\Schema(ref: IdentitySubsets\RoleIdentitySubsetSchema::class),
                        ]),
                        new OA\Schema(allOf: [
                            new OA\Schema(ref: EventRecords\CreateObjectEventParametersCommonSchema::class),
                            new OA\Schema(ref: IdentitySubsets\InstitutionUserIdentitySubsetSchema::class),
                        ]),
                        new OA\Schema(allOf: [
                            new OA\Schema(ref: EventRecords\CreateObjectEventParametersCommonSchema::class),
                            new OA\Schema(ref: IdentitySubsets\VendorIdentitySubsetSchema::class),
                        ]),
                        new OA\Schema(allOf: [
                            new OA\Schema(ref: EventRecords\CreateObjectEventParametersCommonSchema::class),
                            new OA\Schema(ref: IdentitySubsets\InstitutionDiscountIdentitySubsetSchema::class),
                        ]),
                        new OA\Schema(allOf: [
                            new OA\Schema(ref: EventRecords\CreateObjectEventParametersCommonSchema::class),
                            new OA\Schema(ref: IdentitySubsets\ProjectIdentitySubsetSchema::class),
                        ]),
                        new OA\Schema(allOf: [
                            new OA\Schema(ref: EventRecords\CreateObjectEventParametersCommonSchema::class),
                            new OA\Schema(ref: IdentitySubsets\SubprojectIdentitySubsetSchema::class),
                        ]),
                        new OA\Schema(allOf: [
                            new OA\Schema(ref: EventRecords\CreateObjectEventParametersCommonSchema::class),
                            new OA\Schema(ref: IdentitySubsets\AssignmentIdentitySubsetSchema::class),
                        ]),
                        new OA\Schema(allOf: [
                            new OA\Schema(ref: EventRecords\CreateObjectEventParametersCommonSchema::class),
                            new OA\Schema(ref: IdentitySubsets\TranslationMemoryIdentitySubsetSchema::class),
                        ]),
                    ]
                ),
            ],
        ),
    ]
)]
class CreateObjectEventRecordSchema
{
}
