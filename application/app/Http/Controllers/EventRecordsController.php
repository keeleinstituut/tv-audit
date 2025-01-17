<?php

namespace App\Http\Controllers;

use App\Http\OpenApiHelpers as OAH;
use App\Http\Requests\SearchAuditLogRequest;
use App\Http\Resources\EventRecordResource;
use App\Listeners\AuditLogEventListener;
use App\Models\EventRecord;
use App\Policies\EventRecordPolicy;
use AuditLogClient\Enums\AuditLogEventType;
use AuditLogClient\Services\AuditLogMessageBuilder;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use League\Csv\ByteSequence;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Writer;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventRecordsController extends Controller
{
    public function __construct(protected AuditLogEventListener $auditLogListener)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return ResourceCollection<EventRecordResource>
     *
     * @throws AuthorizationException
     * @throws ValidationException
     */
    #[OA\Get(
        path: '/event-records',
        summary: 'List and optionally filter audit log event records belonging to the current institution (inferred from JWT)',
        parameters: [
            new OA\QueryParameter(
                name: 'start_datetime',
                description: 'Filter events to those which happened later than (or equal to) the given datetime',
                schema: new OA\Schema(type: 'string', format: 'date', nullable: true)
            ),
            new OA\QueryParameter(
                name: 'end_datetime',
                description: 'Filter events to those which happened before than (or equal to) the given datetime',
                schema: new OA\Schema(type: 'string', format: 'date', nullable: true)
            ),
            new OA\QueryParameter(
                name: 'department_id',
                description: 'Filter events to those which happened in the context of the specified department',
                schema: new OA\Schema(type: 'string', format: 'uuid', nullable: true)
            ),
            new OA\QueryParameter(
                name: 'acting_user_pic',
                description: 'Filter events to those which were done by specific institution user',
                schema: new OA\Schema(type: 'string', nullable: true)
            ),
            new OA\QueryParameter(
                name: 'event_type',
                description: 'Filter events to those which are of the specified type',
                schema: new OA\Schema(type: 'string', enum: AuditLogEventType::class, nullable: true)
            ),
            new OA\QueryParameter(
                name: 'text',
                description: 'Filter events to those whose data fuzzily matches the given text',
                schema: new OA\Schema(type: 'string', nullable: true)
            ),
            new OA\QueryParameter(
                name: 'page',
                schema: new OA\Schema(type: 'integer', default: 1)
            ),
            new OA\QueryParameter(
                name: 'per_page',
                schema: new OA\Schema(type: 'integer', default: 15)
            ),
        ],
        responses: [new OAH\Forbidden, new OAH\Unauthorized, new OAH\Invalid]
    )]
    #[OAH\PaginatedCollectionResponse(itemsRef: EventRecordResource::class, description: 'Filtered audit log event records of current institution')]
    public function index(SearchAuditLogRequest $request): ResourceCollection
    {
        $this->authorize('viewAny', EventRecord::class);

        $this->auditLogListener->createEventRecord(
            AuditLogMessageBuilder::makeUsingJWT()->toSearchLogsEvent(
                $request->validated('start_datetime'),
                $request->validated('end_datetime'),
                $request->validated('event_type'),
                $request->validated('department_id'),
                $request->validated('text'),
                $request->validated('acting_user_pic'),
            )
        );

        $paginatedQuery = $this->buildSearchQuery($request)->paginate()->appends($request->validated());

        return EventRecordResource::collection($paginatedQuery)->preserveQuery();
    }

    /**
     * Export records as a CSV file.
     *
     * @throws AuthorizationException
     * @throws CannotInsertRecord
     * @throws Exception
     * @throws InvalidArgument
     * @throws ValidationException
     */
    #[OA\Get(
        path: '/event-records/export',
        parameters: [
            new OA\QueryParameter(
                name: 'start_datetime',
                description: 'Filter events to those which happened later than (or equal to) the given datetime',
                schema: new OA\Schema(type: 'string', format: 'date', nullable: true)
            ),
            new OA\QueryParameter(
                name: 'end_datetime',
                description: 'Filter events to those which happened before than (or equal to) the given datetime',
                schema: new OA\Schema(type: 'string', format: 'date', nullable: true)
            ),
            new OA\QueryParameter(
                name: 'department_id',
                description: 'Filter events to those which happened in the context of the specified department',
                schema: new OA\Schema(type: 'string', format: 'uuid', nullable: true)
            ),
            new OA\QueryParameter(
                name: 'acting_user_pic',
                description: 'Filter events to those which were done by specific institution user',
                schema: new OA\Schema(type: 'string', format: 'uuid', nullable: true)
            ),
            new OA\QueryParameter(
                name: 'event_type',
                description: 'Filter events to those which are of the specified type',
                schema: new OA\Schema(type: 'string', enum: AuditLogEventType::class, nullable: true)
            ),
            new OA\QueryParameter(
                name: 'text',
                description: 'Filter events to those whose data fuzzily matches the given text',
                schema: new OA\Schema(type: 'string', nullable: true)
            ),
        ],
        responses: [new OAH\Forbidden, new OAH\Unauthorized, new OAH\Invalid]
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'CSV file of optionally filtered audit log event records in current institution (inferred from JWT)',
        content: new OA\MediaType(
            mediaType: 'text/csv',
            schema: new OA\Schema(type: 'string'),
            example: "\"Logikirje identifikaator\";\"Toimumishetk\";\"Tegutseja isikukood\";\"Tegutseja eesnimi\";\"Tegutseja perenimi\";\"Tegutseja andmeobjekti identifikaator\";\"Tegevuse tüüp\";\"Tegevuse parameetrid\";\"Päringu jälgimise identifikator\";\"Üksuse identifikaator\";\"Asutuse identifikaator\";\"Ebaõnnestumise põhjus (kui tegevus ebaõnnestus)\"\n9a712002-391c-4f17-9c49-a81b19b943e1;2014-10-05T15:11:09.000000Z;61012264271;Jayde;Graham;d12f9148-b8fb-35d1-b630-3a87e3cce73c;DISPATCH_NOTIFICATION;\"{\"TODO\":null}\";142de5b5-a984-3664-8161-f5a43ded0e6b;f7f85c96-0eea-3104-93a0-b28091d7d0a9;f85c6f87-2b32-4c2a-91ae-dd53ab9ba021;\n9a712002-348c-4670-a701-ea836f544f65;1981-08-14T08:40:50.000000Z;37609236792;Garrick;Toy;e7bc0a93-e8be-3514-b8eb-670215fa46ad;APPROVE_ASSIGNMENT_RESULT;\"{\"assignment_ext_id\":\"DWT-3448-56-K-720-GTLM-2\/8\",\"assignment_id\":\"6dadfc52-c288-3a65-b02d-22eec7879729\"}\";3444fab8-c48b-3451-ac0e-85854368b4b6;39fbd400-17ef-3d97-8eb2-1a07818e2c23;f85c6f87-2b32-4c2a-91ae-dd53ab9ba021;"
        )
    )]
    public function export(SearchAuditLogRequest $request): StreamedResponse
    {
        $this->authorize('export', EventRecord::class);

        $this->auditLogListener->createEventRecord(
            AuditLogMessageBuilder::makeUsingJWT()->toExportLogsEvent(
                $request->validated('start_datetime'),
                $request->validated('end_datetime'),
                $request->validated('event_type'),
                $request->validated('department_id'),
                $request->validated('text'),
                $request->validated('acting_user_pic'),
            )
        );

        $eventRecords = $this->buildSearchQuery($request)->get();

        $csvDocument = Writer::createFromString()->setDelimiter(';');
        $csvDocument->setOutputBOM(ByteSequence::BOM_UTF8);

        $csvDocument->insertOne([
            'Logikirje identifikaator',
            'Toimumishetk',
            'Tegutseja isikukood',
            'Tegutseja eesnimi',
            'Tegutseja perenimi',
            'Tegutseja andmeobjekti identifikaator',
            'Tegevuse tüüp',
            'Tegevuse parameetrid',
            'Päringu jälgimise identifikator',
            'Üksuse identifikaator',
            'Asutuse identifikaator',
            'Ebaõnnestumise põhjus (kui tegevus ebaõnnestus)',
        ]);

        $csvDocument->insertAll(
            $eventRecords->map(fn (EventRecord $event) => [
                $event->id,
                $event->happened_at->toISOString(),
                $event->acting_user_pic,
                $event->acting_user_forename,
                $event->acting_user_surname,
                $event->acting_institution_user_id,
                $event->event_type->value,
                is_null($event->event_parameters) ? null : json_encode(Arr::sortRecursive($event->event_parameters)),
                $event->trace_id,
                $event->context_department_id,
                $event->context_institution_id,
                $event->failure_type?->value,
            ])
        );

        return response()->streamDownload(
            $csvDocument->output(...),
            'exported_events.csv',
            ['Content-Type' => 'text/csv']
        );
    }

    public static function getBaseQuery(): Builder
    {
        return EventRecord::getModel()->withGlobalScope('policy', EventRecordPolicy::scope());
    }

    public function buildSearchQuery(SearchAuditLogRequest $request): Builder
    {
        return static::getBaseQuery()
            ->when($request->validated('acting_user_pic'), function (Builder $query, string $pic): void {
                $query->where('acting_user_pic', '=', $pic);
            })
            ->when($request->validated('start_datetime'), function (Builder $query, string $start): void {
                $query->where('happened_at', '>=', $start);
            })
            ->when($request->validated('end_datetime'), function (Builder $query, string $end): void {
                $query->where('happened_at', '<=', $end);
            })
            ->when($request->validated('department_id'), function (Builder $query, string $departmentId): void {
                $query->where('context_department_id', '=', $departmentId);
            })
            ->when($request->validated('event_type'), function (Builder $query, string $eventType): void {
                $query->where('event_type', '=', $eventType);
            })
            ->when($request->validated('text'), function (Builder $query, string $text): void {
                $query->where(function (Builder $textSubQuery) use ($text): void {
                    $textSubQuery
                        ->where('acting_user_pic', 'ILIKE', "%$text%")
                        ->orWhere('acting_user_forename', 'ILIKE', "%$text%")
                        ->orWhere('acting_user_surname', 'ILIKE', "%$text%")
                        ->orWhere('trace_id', 'ILIKE', "%$text%")
                        ->orWhere('acting_institution_user_id', 'ILIKE', "%$text%")
                        ->orWhere('event_type', 'ILIKE', "%$text%")
                        ->orWhere('failure_type', 'ILIKE', "%$text%")
                        ->orWhere('failure_type', 'ILIKE', "%$text%")
                        ->orWhere(DB::raw('event_parameters::text'), 'ILIKE', "%$text%")
                        ->orWhere(DB::raw('acting_user_forename || \' \' || acting_user_surname'), 'ILIKE', "%$text%");
                });
            })
            ->orderByDesc('happened_at');
    }
}
