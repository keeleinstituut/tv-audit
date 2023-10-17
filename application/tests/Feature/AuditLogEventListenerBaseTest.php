<?php

namespace Tests\Feature;

use App\Events\AuditLogEvent;
use App\Models\EventRecord;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Sleep;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use SyncTools\AmqpConnectionRegistry;
use SyncTools\AmqpPublisher;
use Tests\CreatesApplication;
use Tests\TestCase;

use function app;

/**
 * Important notes:
 *  * These tests depend on RabbitMQ running and working.
 *  * These tests assume the audit-log-events queue is empty.
 *  ** Queue must be emptied if there are old messages in the way.
 */
class AuditLogEventListenerBaseTest extends TestCase
{
    use CreatesApplication, MockeryPHPUnitIntegration;

    protected AmqpPublisher $publisher;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('amqp.publisher', [
            'exchanges' => [
                [
                    'exchange' => 'audit-log-events',
                    'type' => 'topic',
                ],
            ],
        ]);

        Artisan::call('amqp:setup');

        $this->publisher = app(AmqpPublisher::class);
    }

    protected function assertEventConsumption(array $body): void
    {
        $this->publisher->publish($body, 'audit-log-events');

        $channel = app(AmqpConnectionRegistry::class)->getConnection()->channel();
        while (empty($message = $channel->basic_get('audit-log-events'))) {
            Sleep::for(CarbonInterval::second());
        }

        $auditLogEvent = AuditLogEvent::produceFromMessage($message);
        Event::dispatch($auditLogEvent);

        Sleep::for(CarbonInterval::second());

        $savedEventRecord = EventRecord::firstWhere(['trace_id' => $body['trace_id']]);
        $this->assertNotNull($savedEventRecord);
        $this->assertArrayHasSubsetIgnoringOrder($body, $savedEventRecord->toArray());
    }
}
