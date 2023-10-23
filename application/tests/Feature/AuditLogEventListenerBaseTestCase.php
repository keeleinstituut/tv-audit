<?php

namespace Tests\Feature;

use App\Events\IncomingAuditLogMessageEvent;
use App\Models\EventRecord;
use AuditLogClient\DataTransferObjects\AuditLogMessage;
use AuditLogClient\Services\AuditLogPublisher;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;
use Illuminate\Validation\ValidationException;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PhpAmqpLib\Message\AMQPMessage;
use SyncTools\AmqpConnectionRegistry;
use Tests\AuthHelpers;
use Tests\CreatesApplication;
use Tests\TestCase;
use Throwable;

use function app;

/**
 * Important notes:
 *  * These tests depend on RabbitMQ running and working.
 *  * These tests assume the audit-log-events queue is empty.
 *  ** Queue must be emptied if there are old messages in the way.
 */
class AuditLogEventListenerBaseTestCase extends TestCase
{
    use CreatesApplication, MockeryPHPUnitIntegration;

    protected AuditLogPublisher $publisher;

    protected CarbonInterval $sleepDuration;

    public function setUp(): void
    {
        parent::setUp();

        $this->sleepDuration = CarbonInterval::milliseconds(100);

        Config::set('amqp.publisher', [
            'exchanges' => [
                [
                    'exchange' => env('AUDIT_LOG_EVENTS_EXCHANGE'),
                    'type' => 'topic',
                ],
            ],
        ]);

        Artisan::call('amqp:setup');

        $this->publisher = app(AuditLogPublisher::class);

        AuthHelpers::fakeServiceValidationResponse();
    }

    protected function assertEventIsRecorded(AuditLogMessage $auditLogMessage): void
    {
        $channel = app(AmqpConnectionRegistry::class)->getConnection()->channel();
        $queue = env('AUDIT_LOG_EVENTS_QUEUE');

        try {
            $this->publisher->publish($auditLogMessage);
        } catch (ValidationException|Throwable $e) {
            $this->fail('Exception was thrown when publishing message: '.$e->getMessage()."\n".$e->getTraceAsString());
        }

        /** @var AMQPMessage $message */
        while (empty($message = $channel->basic_get($queue))) {
            Sleep::for($this->sleepDuration);
        }

        $this->assertJson($message->getBody());
        $messageBody = json_decode($message->getBody(), true);
        $this->assertEquals($auditLogMessage->traceId, $messageBody['trace_id']);

        $messageEvent = IncomingAuditLogMessageEvent::produceFromMessage($message);
        Event::dispatch($messageEvent);

        Sleep::for($this->sleepDuration);

        $savedEventRecord = EventRecord::firstWhere(['trace_id' => $auditLogMessage->traceId]);
        $this->assertNotNull($savedEventRecord);
        $this->assertArrayHasSubsetIgnoringOrder(
            [
                ...$auditLogMessage->toArray(),
                'happened_at' => $auditLogMessage->happenedAt->milliseconds(0)->toISOString(),
            ],
            [
                ...$savedEventRecord->toArray(),
                'event_type' => $savedEventRecord->event_type?->value,
                'failure_type' => $savedEventRecord->failure_type?->value,
            ]
        );
    }
}
