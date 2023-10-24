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
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Reader;
use Tests\AuthHelpers;

class AuditLogControllerExportTest extends AuditLogControllerTestCase
{
    /**
     * @throws Exception
     */
    public function test_all_event_records_returned_without_filters(): void
    {
        $institutionId = Str::uuid()->toString();
        $eventRecords = EventRecord::factory()->state(['context_institution_id' => $institutionId])->count(100)->create();
        $queryParameters = static::buildQueryParameters();
        $this->assertExportedFileContainsOnlyExpectedData($queryParameters, $eventRecords, $institutionId);
    }

    /**
     * @throws Exception
     */
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
        $this->assertExportedFileContainsOnlyExpectedData($queryParameters, $recordsAfterOrEqualsStart, $institutionId);
    }

    /**
     * @throws Exception
     */
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
        $this->assertExportedFileContainsOnlyExpectedData($queryParameters, $recordsBeforeOrEqualsEnd, $institutionId);

    }

    /**
     * @throws Exception
     */
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
        $this->assertExportedFileContainsOnlyExpectedData($queryParameters, $recordsBeforeOrEqualsEnd, $institutionId);
    }

    /**
     * @throws Exception
     */
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
        $this->assertExportedFileContainsOnlyExpectedData($queryParameters, $logInEvents, $institutionId);
    }

    /**
     * @dataProvider provideSearchTextAndMatchingFactoryStates
     *
     * @throws Exception
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
        $this->assertExportedFileContainsOnlyExpectedData($queryParameters, $matchingEventRecords, $institutionId);
    }

    /**
     * @throws Exception
     */
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
        $this->assertExportedFileContainsOnlyExpectedData($queryParameters, $matchingEventRecords, $institutionId);
    }

    /**
     * @throws Exception
     */
    public function test_results_empty_when_no_match(): void
    {
        $institutionId = Str::uuid()->toString();
        EventRecord::factory()
            ->state(['context_institution_id' => $institutionId])
            ->count(100)
            ->create();

        $queryParameters = static::buildQueryParameters(text: Str::random());
        $this->assertExportedFileContainsOnlyExpectedData($queryParameters, new Collection(), $institutionId);
    }

    /**
     * @param  Collection<EventRecord>  $expectedEventRecords
     *
     * @throws Exception
     */
    private function assertExportedFileContainsOnlyExpectedData(array $queryParameters, Collection $expectedEventRecords, string $institutionId): void
    {
        $response = $this
            ->withHeaders(AuthHelpers::createAuthHeadersWithPrivilege($institutionId, PrivilegeKey::ExportAuditLog))
            ->getJson(action([EventRecordsController::class, 'export'], $queryParameters));

        $response
            ->assertSuccessful()
            ->assertDownload('exported_events.csv');

        // And file data should contain users of first institution in expected format
        $expectedResponseData = $expectedEventRecords
            ->map(fn (EventRecord $event) => [
                'Logikirje identifikaator' => $event->id,
                'Toimumishetk' => $event->happened_at->toISOString(),
                'Tegutseja isikukood' => $event->acting_user_pic ?? '',
                'Tegutseja eesnimi' => $event->acting_user_forename ?? '',
                'Tegutseja perenimi' => $event->acting_user_surname ?? '',
                'Tegutseja andmeobjekti identifikaator' => $event->acting_institution_user_id ?? '',
                'Tegevuse tüüp' => $event->event_type->value,
                'Tegevuse parameetrid' => is_null($event->event_parameters) ? '' : json_encode(Arr::sortRecursive($event->event_parameters)),
                'Päringu jälgimise identifikator' => $event->trace_id ?? '',
                'Üksuse identifikaator' => $event->context_department_id ?? '',
                'Asutuse identifikaator' => $event->context_institution_id ?? '',
                'Ebaõnnestumise põhjus (kui tegevus ebaõnnestus)' => $event->failure_type?->value ?? '',
            ]);

        $actualResponseCsvDocument = static::createCsvReader($response);

        $this->assertArraysEqualIgnoringOrder(
            $expectedResponseData->jsonSerialize(),
            $actualResponseCsvDocument->jsonSerialize()
        );
    }

    /**
     * @throws InvalidArgument
     * @throws Exception
     */
    private static function createCsvReader(TestResponse $response): Reader
    {
        return Reader::createFromString($response->streamedContent())
            ->setDelimiter(';')
            ->setHeaderOffset(0);
    }
}
