<?php

namespace App\Http\OpenApiHelpers\Schemas\EventRecords;

use OpenApi\Attributes as OA;

#[OA\Schema(
    title: 'Common MODIFY_OBJECT event_parameters Schema',
    required: [
        'pre_modification_subset',
        'post_modification_subset',
    ],
    properties: [
        new OA\Property(property: 'pre_modification_subset', type: 'object'),
        new OA\Property(property: 'post_modification_subset', type: 'object'),
    ],
    type: 'object'
)]
class ModifyObjectEventParametersCommonSchema
{
}
