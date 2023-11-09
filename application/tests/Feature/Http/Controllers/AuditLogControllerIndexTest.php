<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\PrivilegeKey;
use App\Http\Controllers\EventRecordsController;
use App\Models\EventRecord;
use AuditLogClient\Enums\AuditLogEventType;
use Faker\Generator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use Tests\AuthHelpers;

class AuditLogControllerIndexTest extends AuditLogControllerTestCase
{
    public function test_all_event_records_returned_without_filters(): void
    {
        $institutionId = Str::uuid()->toString();
        $eventRecords = EventRecord::factory()->state(['context_institution_id' => $institutionId])->count(100)->create();
        $queryParameters = static::buildQueryParameters();
        $this->assertIndexReturnsExpectedData($queryParameters, $eventRecords, $institutionId, true);
    }

    public function test_expected_records_returned_with_start_datetime_filter(): void
    {
        $start = Date::now();
        $institutionId = Str::uuid()->toString();

        EventRecord::factory()
            ->state(['context_institution_id' => $institutionId])
            ->forEachSequence(
                ['happened_at' => $start->subSecond()],
                ['happened_at' => $start->subDay()],
                ['happened_at' => $start->subMonth()],
                ['happened_at' => $start->subYear()],
                ['happened_at' => $start->subYears(10)],
            )
            ->create();

        $recordsAfterOrEqualsStart = EventRecord::factory()
            ->state(['context_institution_id' => $institutionId])
            ->forEachSequence(
                ['happened_at' => $start],
                ['happened_at' => $start->addSecond()],
                ['happened_at' => $start->addDay()],
                ['happened_at' => $start->addMonth()],
                ['happened_at' => $start->addYear()],
                ['happened_at' => $start->addYears(10)],
            )
            ->create();

        $queryParameters = static::buildQueryParameters(startDatetime: $start);
        $this->assertIndexReturnsExpectedData($queryParameters, $recordsAfterOrEqualsStart, $institutionId, true);
    }

    public function test_expected_records_returned_with_end_datetime_filter(): void
    {
        $end = Date::now();
        $institutionId = Str::uuid()->toString();

        EventRecord::factory()
            ->state(['context_institution_id' => $institutionId])
            ->forEachSequence(
                ['happened_at' => $end->addSecond()],
                ['happened_at' => $end->addDay()],
                ['happened_at' => $end->addMonth()],
                ['happened_at' => $end->addYear()],
                ['happened_at' => $end->addYears(10)],
            )
            ->create();

        $recordsBeforeOrEqualsEnd = EventRecord::factory()
            ->state(['context_institution_id' => $institutionId])
            ->forEachSequence(
                ['happened_at' => $end],
                ['happened_at' => $end->subSecond()],
                ['happened_at' => $end->subDay()],
                ['happened_at' => $end->subMonth()],
                ['happened_at' => $end->subYear()],
                ['happened_at' => $end->subYears(10)],
            )
            ->create();

        $queryParameters = static::buildQueryParameters(endDatetime: $end);
        $this->assertIndexReturnsExpectedData($queryParameters, $recordsBeforeOrEqualsEnd, $institutionId, true);

    }

    public function test_expected_records_returned_with_department_id_filter(): void
    {
        $institutionId = Str::uuid()->toString();
        EventRecord::factory()->state(['context_institution_id' => $institutionId])->count(30)->create();

        $departmentId = Str::uuid()->toString();
        $recordsBeforeOrEqualsEnd = EventRecord::factory()
            ->state(['context_institution_id' => $institutionId])
            ->count(30)
            ->state(['context_department_id' => $departmentId])
            ->create();

        $queryParameters = static::buildQueryParameters(departmentId: $departmentId);
        $this->assertIndexReturnsExpectedData($queryParameters, $recordsBeforeOrEqualsEnd, $institutionId, false);
    }

    public function test_expected_records_returned_with_event_type_filter(): void
    {
        $logInEventType = AuditLogEventType::LogIn;
        $institutionId = Str::uuid()->toString();

        EventRecord::factory()
            ->state(['context_institution_id' => $institutionId])
            ->sequence(
                ...collect(AuditLogEventType::cases())
                    ->reject(fn (AuditLogEventType $type) => $type === $logInEventType)
                    ->map(fn (AuditLogEventType $type) => ['event_type' => $type->value])
                    ->all()
            )
            ->count(30)
            ->create();

        $logInEvents = EventRecord::factory()
            ->state(['context_institution_id' => $institutionId])
            ->count(5)
            ->state(['event_type' => $logInEventType->value])
            ->create();

        $queryParameters = static::buildQueryParameters(eventType: $logInEventType);
        $this->assertIndexReturnsExpectedData($queryParameters, $logInEvents, $institutionId, false);
    }

    /**
     * @dataProvider provideSearchTextAndMatchingFactoryStates
     */
    public function test_expected_records_returned_with_text_filter(string $searchText, array $matchingFactoryState): void
    {
        $institutionId = Str::uuid()->toString();
        EventRecord::factory()
            ->state(['context_institution_id' => $institutionId])
            ->count(100)
            ->create();

        $matchingEventRecords = EventRecord::factory()
            ->count(10)
            ->state(['context_institution_id' => $institutionId])
            ->state($matchingFactoryState)
            ->create();

        $queryParameters = static::buildQueryParameters(text: $searchText);
        $this->assertIndexReturnsExpectedData($queryParameters, $matchingEventRecords, $institutionId, true);
    }

    public function test_expected_records_returned_with_multiple_filters(): void
    {
        $institutionId = Str::uuid()->toString();
        $departmentId = Str::uuid()->toString();
        $pic = app(Generator::class)->estonianPIC();

        EventRecord::factory()->count(30)->state(['context_institution_id' => $institutionId])->state(['context_department_id' => $departmentId])->create();
        EventRecord::factory()->count(30)->state(['context_institution_id' => $institutionId])->state(['acting_user_pic' => $pic])->create();

        $matchingEventRecords = EventRecord::factory()
            ->state(['context_institution_id' => $institutionId])
            ->count(30)
            ->state(['context_department_id' => $departmentId, 'acting_user_pic' => $pic])
            ->create();

        $queryParameters = static::buildQueryParameters(departmentId: $departmentId, text: $pic);
        $this->assertIndexReturnsExpectedData($queryParameters, $matchingEventRecords, $institutionId, false);
    }

    public function test_results_empty_when_no_match(): void
    {
        $institutionId = Str::uuid()->toString();
        EventRecord::factory()
            ->state(['context_institution_id' => $institutionId])
            ->count(100)
            ->create();

        $queryParameters = static::buildQueryParameters(text: Str::random());
        $this->assertIndexReturnsExpectedData($queryParameters, Collection::empty(), $institutionId, true);
    }

    /**
     * @param  Collection<EventRecord>  $expectedEventRecords
     */
    private function assertIndexReturnsExpectedData(array $queryParameters, Collection $expectedEventRecords, string $institutionId, bool $expectTestQueriesInResults): void
    {
        $tolkevaravClaims = AuthHelpers::createTolkevaravClaims($institutionId, PrivilegeKey::ViewAuditLog);

        $this->assertPaginatedResponsesHaveIdsRecursively(
            $tolkevaravClaims,
            action([EventRecordsController::class, 'index'], $queryParameters),
            $expectedEventRecords->keyBy('id'),
            Collection::empty(),
            Collection::empty(),
            $expectTestQueriesInResults,
            AuthHelpers::createAuthHeaders($tolkevaravClaims)
        );
    }

    private function assertPaginatedResponsesHaveIdsRecursively(array $tolkevaravClaims, string $url, Collection $remainingExpectedRecords, Collection $foundExpectedRecords, Collection $testQueryRecords, bool $expectTestQueriesInResults, array $headers): void
    {
        $currentRequestTraceId = Str::random();

        $response = $this
            ->withHeaders([config('amqp.audit_logs.trace_id_http_header') => $currentRequestTraceId, ...$headers])
            ->getJson($url);
        $this->assertResponseAsExpectedInGeneral($response);

        $lastQueryEventRecord = EventRecord::where([
            'trace_id' => $currentRequestTraceId,
        ])->orderByDesc('happened_at')->firstOrFail();

        $testQueryRecords->put($lastQueryEventRecord->id, $lastQueryEventRecord);

        collect($response->json('data'))
            ->each(function (array $actualRecord) use ($testQueryRecords, $foundExpectedRecords, $remainingExpectedRecords) {
                /** @var EventRecord $expectedRecordModel */
                $expectedRecordModel = $remainingExpectedRecords->first(fn (EventRecord $record) => $record->id === $actualRecord['id']);

                if ($expectedRecordModel === null) {
                    $this->assertTrue(
                        $foundExpectedRecords->contains(fn (EventRecord $record) => $record->id === $actualRecord['id'])
                        || $testQueryRecords->contains(fn (EventRecord $record) => $record->id === $actualRecord['id'])
                    );

                    return;
                }

                $expectedRecordData = Arr::only(
                    $expectedRecordModel->toArray(),
                    ['id', 'happened_at', 'acting_user_pic', 'acting_user_forename', 'acting_user_surname', 'acting_institution_user_id', 'event_type', 'event_parameters', 'trace_id', 'context_department_id', 'context_institution_id', 'failure_type']
                );
                $this->assertArraysEqualIgnoringOrder(
                    $expectedRecordData,
                    $actualRecord
                );
                $remainingExpectedRecords->forget($expectedRecordModel->id);
                $foundExpectedRecords->put($expectedRecordModel->id, $expectedRecordModel);
            });

        if ($remainingExpectedRecords->isEmpty()) {
            return;
        }

        $this->assertIsString($response->json('links.next'));

        $this->assertPaginatedResponsesHaveIdsRecursively(
            $tolkevaravClaims,
            $response->json('links.next'),
            $remainingExpectedRecords,
            $testQueryRecords,
            $foundExpectedRecords,
            $expectTestQueriesInResults,
            $headers
        );
    }

    private function assertResponseAsExpectedInGeneral(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsArray('data');

        $response->assertJsonIsObject('links');
        $this->assertIsString($response->json('links.first'));
        $this->assertIsString($response->json('links.last'));
        $this->assertArrayHasKey('next', $response->json('links'));
        $this->assertArrayHasKey('prev', $response->json('links'));

        $response->assertJsonIsObject('meta');
        $this->assertIsInt($response->json('meta.total'));
        $this->assertIsInt($response->json('meta.current_page'));
        $this->assertIsInt($response->json('meta.last_page'));
        $this->assertIsInt($response->json('meta.per_page'));
        $this->assertArrayHasKey('from', $response->json('meta'));
        $this->assertArrayHasKey('to', $response->json('meta'));
    }

    /**
     * @param  Collection<EventRecord>  $eventRecords
     */
    public static function sort(Collection $eventRecords): Collection
    {
        return $eventRecords->sortByDesc('happened_at')->values();
    }
}
