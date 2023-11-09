<?php

namespace App\Listeners;

use App\Events\IncomingAuditLogMessageEvent;
use App\Models\EventRecord;
use AuditLogClient\Services\AuditLogMessageValidationService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use PhpAmqpLib\Wire\AMQPTable;

class AuditLogEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly AuditLogMessageValidationService $validationService)
    {
    }

    /**
     * Handle the event.
     *
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function handle(IncomingAuditLogMessageEvent $amqpEvent): void
    {
        static::authorize($amqpEvent);

        $validator = $this->validationService->makeValidator($amqpEvent->getBody());
        $validator->validate();

        EventRecord::create($validator->validated());

        $this->ackMessage($amqpEvent);
    }

    public function ackMessage(IncomingAuditLogMessageEvent $amqpEvent): void
    {
        $amqpEvent->message->ack();
    }

    /** @throws AuthorizationException */
    public static function authorize(IncomingAuditLogMessageEvent $amqpEvent): void
    {
        /** @var AMQPTable $applicationHeaders */
        $applicationHeaders = $amqpEvent->message->get('application_headers');
        Gate::forUser(null)->authorize('create', [EventRecord::class, $applicationHeaders['jwt']]);
    }
}
