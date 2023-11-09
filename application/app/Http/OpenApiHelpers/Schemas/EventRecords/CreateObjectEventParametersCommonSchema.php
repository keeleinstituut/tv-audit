<?php

namespace App\Http\OpenApiHelpers\Schemas\EventRecords;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Common CREATE_OBJECT event_parameters Schema',
    required: [
        'object_data',
    ],
    properties: [
        new OA\Property(
            property: 'object_data',
            description: 'All relevant data that the object was created with.',
            type: 'object'
        ),
    ],
    type: 'object'
)]
class CreateObjectEventParametersCommonSchema
{
}
