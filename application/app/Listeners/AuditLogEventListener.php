<?php

namespace App\Listeners;

use App\Events\IncomingAuditLogMessageEvent;
use App\Models\EventRecord;
use AuditLogClient\Services\AuditLogMessageValidationService;
use Illuminate\Validation\ValidationException;

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
     */
    public function handle(IncomingAuditLogMessageEvent $amqpEvent): void
    {
        $validator = $this->validationService->makeValidator($amqpEvent->getBody());
        $validator->validate();

        EventRecord::create($validator->validated());

        $this->ackMessage($amqpEvent);
    }

    public function ackMessage(IncomingAuditLogMessageEvent $amqpEvent): void
    {
        $amqpEvent->message->ack();
    }
}
