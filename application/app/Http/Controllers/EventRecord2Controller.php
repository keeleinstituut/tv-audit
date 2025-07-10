<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Resources\EventRecord2Resource;
use App\Http\Requests\SearchAuditLogRequest;
use App\Models\EventRecord2;
use App\Policies\EventRecordPolicy;


class EventRecord2Controller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(SearchAuditLogRequest $request)
    {
        $this->authorize('viewAny', EventRecord::class);

        $data = EventRecord2::getModel()
            ->orderBy('happened_at', 'desc')
            ->paginate();

        $paginatedQuery = $this->buildSearchQuery($request)->paginate()->appends($request->validated());

        return EventRecord2Resource::collection($paginatedQuery);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    private function getBaseQuery() {
        return EventRecord2::getModel()->withGlobalScope('policy', EventRecordPolicy::scope());
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
