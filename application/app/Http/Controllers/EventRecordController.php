<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use League\Csv\ByteSequence;
use League\Csv\Writer;
use OpenApi\Attributes as OA;
use App\Http\OpenApiHelpers as OAH;
use App\Http\Resources\EventRecordResource;
use App\Http\Requests\SearchAuditLogRequest;
use App\Models\EventRecord;
use App\Policies\EventRecordPolicy;

class EventRecordController extends Controller
{
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
                schema: new OA\Schema(type: 'string', nullable: true)
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
    public function index(SearchAuditLogRequest $request)
    {
        $this->authorize('viewAny', EventRecord::class);

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
                schema: new OA\Schema(type: 'string', nullable: true)
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
        )
    )]
    public function export(SearchAuditLogRequest $request): StreamedResponse
    {
        $this->authorize('export', EventRecord::class);

        $eventRecords = $this->buildSearchQuery($request)->get();

        $csvDocument = Writer::createFromString()->setDelimiter(';');
        $csvDocument->setOutputBOM(ByteSequence::BOM_UTF8);

        $csvDocument->insertOne([
            'Logikirje identifikaator',
            'Toimumishetk',

            'Tegutseja isikukood',
            'Tegutseja nimi',
            'Tegutseja identifikaator',
            'Üksuse identifikaator',
            'Asutuse identifikaator',

            'Tegevuse tüüp',
            'Tegutseja aadress veebilehel',

            'Päringu aadress',
            'Päringu meetod',
            'Päringu parameetrid',
            'Päringu sisu',
            'Päringu tulemuse HTTP kood',
        ]);

        $csvDocument->insertAll(
            $eventRecords->map(fn (EventRecord $event) => [
                $event->id,
                $event->happened_at->toISOString(),

                $event->actor_pic,
                $event->actor_name,
                $event->actor_institution_user_id,
                $event->actor_department_id,
                $event->actor_institution_id,

                $event->action,
                $event->web_path,

                $event->path,
                $event->request_method,
                json_encode($event->request_query),
                json_encode($event->request_body),
                $event->response_status_code,
            ])
        );

        return response()->streamDownload(
            $csvDocument->output(...),
            'exported_events.csv',
            ['Content-Type' => 'text/csv']
        );
    }

    /**
     * Display a listing of action fields saved to EventRecord objects
     */
    public function indexActions(Request $request)
    {
        $this->authorize('viewAny', EventRecord::class);

        // Query actions over all saved EventRecords since action field is generic and not institution specific
        $data = EventRecord::getModel()->select('action')->distinct()->get()->pluck('action');

        // Use JsonResource directly to output a simple array of strings
        return JsonResource::make($data);
    }

    private function getBaseQuery() {
        return EventRecord::getModel()->withGlobalScope('policy', EventRecordPolicy::scope());
    }

    private function buildSearchQuery(SearchAuditLogRequest $request): Builder
    {
        return static::getBaseQuery()
            ->when($request->validated('acting_user_pic'), function (Builder $query, string $pic): void {
                $query->where('actor_pic', '=', $pic);
            })
            ->when($request->validated('start_datetime'), function (Builder $query, string $start): void {
                $query->where('happened_at', '>=', $start);
            })
            ->when($request->validated('end_datetime'), function (Builder $query, string $end): void {
                $query->where('happened_at', '<=', $end);
            })
            ->when($request->validated('department_id'), function (Builder $query, string $departmentId): void {
                $query->where('actor_department_id', '=', $departmentId);
            })
            ->when($request->validated('event_type'), function (Builder $query, string $eventType): void {
                $query->where('action', '=', $eventType);
            })
            ->when($request->validated('text'), function (Builder $query, string $text): void {
                $query->where(function (Builder $textSubQuery) use ($text): void {
                    $textSubQuery
                        ->where('actor_pic', 'ILIKE', "%$text%")
                        ->orWhere('actor_name', 'ILIKE', "%$text%")
                        ->orWhere('actor_institution_user_id', 'ILIKE', "%$text%")
                        ->orWhere('action', 'ILIKE', "%$text%");
                });
            })
            ->orderByDesc('happened_at');
    }
}
