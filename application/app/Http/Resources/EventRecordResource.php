<?php

namespace App\Http\Resources;

use App\Http\OpenApiHelpers as OAH;
use App\Models\EventRecord;
use AuditLogClient\Enums\AuditLogEventObjectType;
use AuditLogClient\Enums\AuditLogEventType;
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
        new OA\Schema(
            title: 'Event types which require no parameters',
            allOf: [
                new OA\Schema(ref: OAH\EventRecordCommonSchema::class),
                new OA\Schema(
                    required: ['event_type', 'event_parameters'],
                    properties: [
                        new OA\Property(
                            property: 'event_type',
                            type: 'string',
                            enum: [
                                AuditLogEventType::LogIn,
                                AuditLogEventType::LogOut,
                                AuditLogEventType::ExportInstitutionUsers,
                                AuditLogEventType::SelectInstitution,
                            ]
                        ),
                        new OA\Property(property: 'event_parameters', type: 'string', enum: [null], nullable: true),
                    ],
                ),
            ]
        ),
        new OA\Schema(
            title: 'Event types whose parameters hold a reference to an assignment',
            allOf: [
                new OA\Schema(ref: OAH\EventRecordCommonSchema::class),
                new OA\Schema(
                    required: ['event_type', 'event_parameters'],
                    properties: [
                        new OA\Property(
                            property: 'event_type',
                            type: 'string',
                            enum: [
                                AuditLogEventType::RejectAssignmentResult,
                                AuditLogEventType::ApproveAssignmentResult,
                                AuditLogEventType::CompleteAssignment,
                            ]
                        ),
                        new OA\Property(
                            property: 'event_parameters',
                            required: ['assignment_id', 'assignment_ext_id'],
                            properties: [
                                new OA\Property(property: 'assignment_id', type: 'string'),
                                new OA\Property(property: 'assignment_ext_id', type: 'string'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ]
        ),
        new OA\Schema(
            title: 'Event types whose parameters describe an audit log query',
            allOf: [
                new OA\Schema(ref: OAH\EventRecordCommonSchema::class),
                new OA\Schema(
                    required: ['event_type', 'event_parameters'],
                    properties: [
                        new OA\Property(
                            property: 'event_type',
                            type: 'string',
                            enum: [
                                AuditLogEventType::SearchLogs,
                                AuditLogEventType::ExportLogs,
                            ]
                        ),
                        new OA\Property(
                            property: 'event_parameters',
                            required: [
                                'query_start_datetime',
                                'query_end_datetime',
                                'query_event_type',
                                'query_text',
                                'query_department_id',
                            ],
                            properties: [
                                new OA\Property(property: 'query_start_datetime', type: 'string', nullable: true),
                                new OA\Property(property: 'query_end_datetime', type: 'string', nullable: true),
                                new OA\Property(property: 'query_event_type', type: 'string', nullable: true),
                                new OA\Property(property: 'query_text', type: 'string', nullable: true),
                                new OA\Property(property: 'query_department_id', type: 'string', nullable: true),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ]
        ),
        new OA\Schema(
            title: 'Event types whose parameters hold a reference to a translation memory',
            allOf: [
                new OA\Schema(ref: OAH\EventRecordCommonSchema::class),
                new OA\Schema(
                    required: ['event_type', 'event_parameters'],
                    properties: [
                        new OA\Property(
                            property: 'event_type',
                            type: 'string',
                            enum: [
                                AuditLogEventType::ImportTranslationMemory,
                                AuditLogEventType::ExportTranslationMemory]
                        ),
                        new OA\Property(
                            property: 'event_parameters',
                            required: [
                                'translation_memory_id',
                                'translation_memory_name',
                            ],
                            properties: [
                                new OA\Property(property: 'translation_memory_id', type: 'string'),
                                new OA\Property(property: 'translation_memory_name', type: 'string'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ]
        ),
        new OA\Schema(
            title: 'Event type whose parameters describe a projects export query',
            allOf: [
                new OA\Schema(ref: OAH\EventRecordCommonSchema::class),
                new OA\Schema(
                    required: ['event_type', 'event_parameters'],
                    properties: [
                        new OA\Property(
                            property: 'event_type',
                            type: 'string',
                            enum: [AuditLogEventType::ExportProjectsReport]
                        ),
                        new OA\Property(
                            property: 'event_parameters',
                            required: [
                                'query_start_date',
                                'query_end_date',
                                'query_status',
                            ],
                            properties: [
                                new OA\Property(property: 'query_start_date', type: 'string', nullable: true),
                                new OA\Property(property: 'query_end_date', type: 'string', nullable: true),
                                new OA\Property(property: 'query_status', type: 'string', nullable: true),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
            ]
        ),
        new OA\Schema(
            title: 'Event type whose parameters hold a reference to a project’s file',
            allOf: [
                new OA\Schema(ref: OAH\EventRecordCommonSchema::class),
                new OA\Schema(
                    required: ['event_type', 'event_parameters'],
                    properties: [
                        new OA\Property(
                            property: 'event_type',
                            type: 'string',
                            enum: [AuditLogEventType::DownloadProjectFile]
                        ),
                        new OA\Property(
                            property: 'event_parameters',
                            required: [
                                'media_id',
                                'project_id',
                                'project_ext_id',
                                'file_name',
                            ],
                            properties: [
                                new OA\Property(property: 'media_id', type: 'string'),
                                new OA\Property(property: 'project_id', type: 'string'),
                                new OA\Property(property: 'project_ext_id', type: 'string'),
                                new OA\Property(property: 'file_name', type: 'string'),
                            ],
                            type: 'object',
                        ),
                    ],
                ),
                new OA\Schema(
                    title: 'Event type whose parameters describe a dispatched notification',
                    allOf: [
                        new OA\Schema(ref: OAH\EventRecordCommonSchema::class),
                        new OA\Schema(
                            required: ['event_type', 'event_parameters'],
                            properties: [
                                new OA\Property(
                                    property: 'event_type',
                                    type: 'string',
                                    enum: [AuditLogEventType::DispatchNotification]
                                ),
                                new OA\Property(
                                    property: 'event_parameters',
                                    required: ['TODO'],
                                    properties: [new OA\Property(property: 'TODO', type: 'string', enum: [null], nullable: true)],
                                    type: 'object',
                                ),
                            ],
                        ),
                    ]
                ),
                new OA\Schema(
                    title: 'Event type whose parameters hold a reference to a workflow',
                    allOf: [
                        new OA\Schema(ref: OAH\EventRecordCommonSchema::class),
                        new OA\Schema(
                            required: ['event_type', 'event_parameters'],
                            properties: [
                                new OA\Property(
                                    property: 'event_type',
                                    type: 'string',
                                    enum: [AuditLogEventType::RewindWorkflow]
                                ),
                                new OA\Property(
                                    property: 'event_parameters',
                                    required: ['workflow_id', 'workflow_name'],
                                    properties: [
                                        new OA\Property(property: 'workflow_id', type: 'string'),
                                        new OA\Property(property: 'workflow_name', type: 'string'),
                                    ],
                                    type: 'object',
                                ),
                            ],
                        ),
                    ]
                ),
                new OA\Schema(
                    title: 'Event type whose parameters hold a reference to a project',
                    allOf: [
                        new OA\Schema(ref: OAH\EventRecordCommonSchema::class),
                        new OA\Schema(
                            required: ['event_type', 'event_parameters'],
                            properties: [
                                new OA\Property(
                                    property: 'event_type',
                                    type: 'string',
                                    enum: [AuditLogEventType::FinishProject]
                                ),
                                new OA\Property(
                                    property: 'event_parameters',
                                    required: [
                                        'project_id',
                                        'project_ext_id',
                                    ],
                                    properties: [
                                        new OA\Property(property: 'project_id', type: 'string'),
                                        new OA\Property(property: 'project_ext_id', type: 'string'),
                                    ],
                                    type: 'object',
                                ),
                            ],
                        ),
                    ]
                ),
                new OA\Schema(
                    title: 'Event type whose parameters describe an attempt to create an object',
                    allOf: [
                        new OA\Schema(ref: OAH\EventRecordCommonSchema::class),
                        new OA\Schema(
                            required: ['event_type', 'event_parameters'],
                            properties: [
                                new OA\Property(
                                    property: 'event_type',
                                    type: 'string',
                                    enum: [AuditLogEventType::CreateObject]
                                ),
                                new OA\Property(
                                    property: 'event_parameters',
                                    required: [
                                        'object_type',
                                        'object_data',
                                    ],
                                    properties: [
                                        new OA\Property(property: 'object_type', type: 'string', enum: AuditLogEventObjectType::class),
                                        new OA\Property(property: 'object_data', type: 'object'),
                                    ],
                                    type: 'object',
                                ),
                            ],
                        ),
                    ]
                ),
                new OA\Schema(
                    title: 'Event type whose parameters describe an attempt to remove an object',
                    allOf: [
                        new OA\Schema(ref: OAH\EventRecordCommonSchema::class),
                        new OA\Schema(
                            required: ['event_type', 'event_parameters'],
                            properties: [
                                new OA\Property(
                                    property: 'event_type',
                                    type: 'string',
                                    enum: [AuditLogEventType::RemoveObject]
                                ),
                                new OA\Property(
                                    property: 'event_parameters',
                                    required: [
                                        'object_type',
                                        'object_identity_subset',
                                    ],
                                    properties: [
                                        new OA\Property(property: 'object_type', type: 'string', enum: AuditLogEventObjectType::class),
                                        new OA\Property(property: 'object_identity_subset', type: 'object'),
                                    ],
                                    type: 'object',
                                ),
                            ],
                        ),
                    ]
                ),
                new OA\Schema(
                    title: 'Event type whose parameters describe an attempt to modify an object',
                    allOf: [
                        new OA\Schema(ref: OAH\EventRecordCommonSchema::class),
                        new OA\Schema(
                            required: ['event_type', 'event_parameters'],
                            properties: [
                                new OA\Property(
                                    property: 'event_type',
                                    type: 'string',
                                    enum: [AuditLogEventType::ModifyObject]
                                ),
                                new OA\Property(
                                    property: 'event_parameters',
                                    required: [
                                        'object_type',
                                        'object_identity_subset',
                                        'pre_modification_subset',
                                        'post_modification_subset',
                                    ],
                                    properties: [
                                        new OA\Property(property: 'object_type', type: 'string', enum: AuditLogEventObjectType::class),
                                        new OA\Property(property: 'object_identity_subset', type: 'object'),
                                        new OA\Property(property: 'pre_modification_subset', type: 'object'),
                                        new OA\Property(property: 'post_modification_subset', type: 'object'),
                                    ],
                                    type: 'object',
                                ),
                            ],
                        ),
                    ]
                ),

            ]
        ),

    ]
)]
class EventRecordResource extends JsonResource
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
